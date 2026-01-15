<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Order;
use App\Models\Driver;
use App\Events\OrderRated;
use App\Models\OrderOffer;
use App\Models\OrderRating;
use Illuminate\Http\Request;
use App\Models\DriverLocation;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Services\GoogleMapsService;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    use ApiResponseTrait;

    /**
     * تقييم السائق
     */
    public function rateDriver(Request $request, $orderId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'aspects' => 'nullable|array',
            'aspects.punctuality' => 'nullable|integer|min:1|max:5',
            'aspects.service_quality' => 'nullable|integer|min:1|max:5',
            'aspects.communication' => 'nullable|integer|min:1|max:5',
            'aspects.carefulness' => 'nullable|integer|min:1|max:5',
        ]);

        $user = auth()->user();
        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
            ->where('order_status_id', 4) // تم التسليم
            ->with('driver')
            ->firstOrFail();

        // التحقق من أن المستخدم لم يقم بالتقييم من قبل
        $existingRating = OrderRating::where('order_id', $orderId)
            ->where('rated_by', 'user')
            ->first();

        if ($existingRating) {
            return $this->errorResponse('لقد قمت بتقييم هذا الطلب مسبقاً', 400);
        }

        try {
            DB::beginTransaction();

            $rating = OrderRating::create([
                'order_id' => $orderId,
                'driver_id' => $order->driver_id,
                'user_id' => $user->id,
                'rated_by' => 'user',
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'aspects' => $validated['aspects'] ?? [],
            ]);

            // تحديث متوسط تقييم السائق
            $this->updateDriverAverageRating($order->driver_id);

            // تحديث إحصائيات السائق
            $this->updateDriverStats($order->driver_id);

            DB::commit();

            // Broadcast Event
            event(new OrderRated($rating));

            return $this->successResponse([
                'rating' => $rating,
                'driver_avg_rating' => $order->driver->fresh()->average_rating,
                'message' => 'شكراً لك على تقييمك!',
            ], 'تم إضافة التقييم بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('فشل إضافة التقييم', 500);
        }
    }

    /**
     * تقييم المستخدم (من قبل السائق)
     */
    public function rateUser(Request $request, $orderId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'aspects' => 'nullable|array',
        ]);

        $driver = auth()->user()->driver;

        if (!$driver) {
            return $this->errorResponse('يجب أن تكون سائقاً', 403);
        }

        $order = Order::where('driver_id', $driver->id)
            ->where('id', $orderId)
            ->where('order_status_id', 4) // تم التسليم
            ->firstOrFail();

        // التحقق من أن السائق لم يقم بالتقييم من قبل
        $existingRating = OrderRating::where('order_id', $orderId)
            ->where('rated_by', 'driver')
            ->first();

        if ($existingRating) {
            return $this->errorResponse('لقد قمت بتقييم هذا الطلب مسبقاً', 400);
        }

        try {
            DB::beginTransaction();

            $rating = OrderRating::create([
                'order_id' => $orderId,
                'driver_id' => $driver->id,
                'user_id' => $order->user_id,
                'rated_by' => 'driver',
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'aspects' => $validated['aspects'] ?? [],
            ]);

            // تحديث تقييم المستخدم
            $this->updateUserRatingStats($order->user_id);

            DB::commit();

            event(new OrderRated($rating));

            return $this->successResponse(
                $rating,
                'تم تقييم المستخدم بنجاح'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('فشل إضافة التقييم', 500);
        }
    }

    /**
     * جلب جميع العروض للطلب
     */
    public function getOrderOffers($orderId)
    {
        $user = auth()->user();
        $order = Order::where('user_id', $user->id)
            ->findOrFail($orderId);

        $offers = \App\Models\OrderOffer::where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();

        // إضافة معلومات المسافة للعروض النشطة
        if ($order->order_status_id == 1) { // طلب معلق
            $offers = $offers->map(function ($offer) use ($order) {
                if ($offer->status == 'pending') {
                    $driverLocation = DriverLocation::where('driver_id', $offer->driver_id)
                        ->latest()
                        ->first();

                    if ($driverLocation && $order->location) {
                        $googleMapsService = app(\App\Services\GoogleMapsService::class);
                        $distanceInfo = $googleMapsService->calculateHaversineDistance(
                            $driverLocation->latitude,
                            $driverLocation->longitude,
                            $order->location->latitude,
                            $order->location->longitude
                        );

                        $offer->distance_info = $distanceInfo;
                    }
                }

                return $offer;
            });
        }

        return $this->successResponse([
            'order_id' => $order->id,
            'order_status' => $order->status?->name,
            'total_offers' => $offers->count(),
            'active_offers' => $offers->where('status', 'pending')->count(),
            'accepted_offer' => $offers->where('status', 'accepted')->first(),
            'offers' => $offers,
        ], 'تم جلب العروض بنجاح');
    }

    /**
     * جلب تقييمات السائق
     */
    public function getDriverRatings($driverId)
    {
        $ratings = OrderRating::with(['user:id,name', 'order:id,created_at'])
            ->where('driver_id', $driverId)
            ->where('rated_by', 'user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $driver = \App\Models\Driver::findOrFail($driverId);

        return $this->successResponse([
            'driver' => [
                'id' => $driver->id,
                'name' => $driver->full_name,
                'average_rating' => $driver->average_rating,
                'total_ratings' => $driver->total_ratings,
                'total_orders' => $driver->total_orders,
            ],
            'ratings' => $ratings,
            'rating_summary' => [
                '5_stars' => $ratings->where('rating', 5)->count(),
                '4_stars' => $ratings->where('rating', 4)->count(),
                '3_stars' => $ratings->where('rating', 3)->count(),
                '2_stars' => $ratings->where('rating', 2)->count(),
                '1_star' => $ratings->where('rating', 1)->count(),
            ],
        ], 'تم جلب التقييمات بنجاح');
    }

    // ========== الدوال المساعدة ==========

    private function updateDriverAverageRating($driverId)
    {
        $driver = \App\Models\Driver::find($driverId);

        if ($driver) {
            $averageRating = OrderRating::where('driver_id', $driverId)
                ->where('rated_by', 'user')
                ->avg('rating');

            $totalRatings = OrderRating::where('driver_id', $driverId)
                ->where('rated_by', 'user')
                ->count();

            $driver->update([
                'average_rating' => round($averageRating, 1),
                'total_ratings' => $totalRatings,
            ]);
        }
    }

    private function updateDriverStats($driverId)
    {
        $driver = \App\Models\Driver::find($driverId);

        if ($driver) {
            // حساب نسبة التقييمات الإيجابية (4-5 نجوم)
            $positiveRatings = OrderRating::where('driver_id', $driverId)
                ->where('rated_by', 'user')
                ->whereIn('rating', [4, 5])
                ->count();

            $totalRatings = $driver->total_ratings;
            $positiveRate = $totalRatings > 0 ? round(($positiveRatings / $totalRatings) * 100, 1) : 0;

            $driver->update([
                'positive_rating_rate' => $positiveRate,
            ]);
        }
    }

    private function updateUserRatingStats($userId)
    {
        $user = \App\Models\User::find($userId);

        if ($user) {
            $averageRating = OrderRating::where('user_id', $userId)
                ->where('rated_by', 'driver')
                ->avg('rating');

            $user->update([
                'driver_rating' => round($averageRating, 1),
            ]);
        }
    }
}
