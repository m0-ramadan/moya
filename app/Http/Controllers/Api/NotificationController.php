<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    private $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->middleware('auth');
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get all notifications for authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type');

        $notifications = $user->customNotifications()
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($request->has('unread_only'), fn($q) => $q->unread())
            ->paginate($request->get('per_page', 15));

        return $this->paginated(
            $notifications,
            'تم جلب الإشعارات بنجاح'
        );
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $count = $user->customUnreadNotifications()->count();

        return $this->successResponse(
            ['count' => $count],
            'تم جلب عدد الإشعارات غير المقروءة بنجاح'
        );
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->markAsRead();

        return $this->successResponse(
            $notification->fresh(),
            'تم تعليم الإشعار كمقروء'
        );
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = $user->markAllNotificationsAsRead();

        return $this->successResponse(
            ['count' => $count],
            "{$count} إشعار تم تعليمها كمقروءة"
        );
    }

    /**
     * Delete notification.
     */
    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return $this->successResponse(
            null,
            'تم حذف الإشعار بنجاح'
        );
    }

    /**
     * Clear all notifications.
     */
    public function clearAll()
    {
        $user = Auth::user();

        $count = $user->clearCustomNotifications();

        return $this->successResponse(
            ['count' => $count],
            "{$count} إشعار تم مسحها"
        );
    }

    /**
     * Test Firebase notification (Admin only - keep as is or protect with policy/gate).
     */
    public function testFirebase(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
        ]);

        $success = $this->firebaseService->sendToDevice(
            $request->device_token,
            [
                'title' => $request->title,
                'body'  => $request->body,
            ],
            $request->get('data', [])
        );

        if ($success) {
            return $this->successResponse(
                null,
                'تم إرسال الإشعار التجريبي بنجاح'
            );
        }

        return $this->errorResponse(
            'فشل إرسال الإشعار التجريبي',
            500
        );
    }

    /**
     * Get notification statistics.
     */
    public function getStats()
    {
        $user = Auth::user();

        $stats = [
            'total'  => $user->totalNotificationsCount(),
            'unread' => $user->customUnreadNotifications()->count(),
            'read'   => $user->customReadNotifications()->count(),
            'by_type' => $user->customNotifications()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
        ];

        return $this->successResponse(
            $stats,
            'تم جلب إحصائيات الإشعارات بنجاح'
        );
    }

    /**
     * Get latest notifications.
     */
    public function latest(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $notifications = $user->getLatestNotifications($limit);

        return $this->successResponse(
            $notifications,
            'تم جلب أحدث الإشعارات بنجاح'
        );
    }

    /**
     * Get notifications by type.
     */
    public function byType(Request $request, $type)
    {
        $user = Auth::user();

        $notifications = $user->getNotificationsByType($type)
            ->paginate($request->get('per_page', 15));

        return $this->paginated(
            $notifications,
            "تم جلب إشعارات النوع {$type} بنجاح"
        );
    }
}
