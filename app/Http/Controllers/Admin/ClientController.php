<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use App\Models\Branchs;
use App\Models\Clients;
use App\Models\Country;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Models\Representative;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Traits\FirebaseNotificationTrait;
use Flasher\Toastr\Laravel\Facade\Toastr;
use Flasher\Toastr\Prime\ToastrInterface;

class ClientController extends Controller
{
    use FirebaseNotificationTrait;
    // public function __construct()
    // {
    //     // Ensure auth:admin middleware is applied
    //     $this->middleware('auth:admin');
    //     // Apply Spatie permission middleware
    //     $this->middleware('permission:عرض العملاء', ['only' => ['index', 'providers']]);
    //     $this->middleware('permission:إضافة عميل', ['only' => ['store']]);
    //     $this->middleware('permission:تعديل العملاء', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:حذف العملاء', ['only' => ['destroy']]);
    //     $this->middleware('permission:تغيير حالة العملاء', ['only' => ['changeStatus']]);
    //     $this->middleware('permission:إرسال إشعار للعميل', ['only' => ['sendNotificationToUser', 'sendnotifications', 'sendnotification', 'sendNotificationSingle']]);
    // }
    public function index()
    {
        $clients = Clients::where('type', 1)->get();
        return view('Admin.clients.index', compact('clients'));
    }

    public function providers()
    {
        $clients = Clients::where('type', 2)
            ->orderBy('id', 'desc')
            ->get();
        return view('Admin.clients.providers', compact('clients'));
    }


    public function create()
    {
        $countries = Country::get();
        $regions = Region::get();
        return view('Admin.branchs.create', compact('countries', 'regions'));
    }

    public function store(Request $request)
    {
        Branchs::create([
            'name' => $request->name_ar,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address' => $request->address,
            'link_address' => $request->map,
            'price' => $request->price,
            'country_id' => $request->country_id,
            'region_id' => $request->regions_id,
            'key' => $request->key,
        ]);


        toastr::success('success', 'sucessfully added');
        return redirect()->route('admin.branch.index');
    }

    public function edit($id)
    {

        $branch = Branchs::find($id);
        $countries = Country::get();
        $regions = Region::get();
        return view('Admin.branchs.edit', compact('countries', 'branch', 'regions'));
    }

    public function update(Request $request)
    {
        $branch = Branchs::find($request->id); // تأكد أنك جلبت الفرع الموجود
        $branch->update([
            'name' => $request->name_ar,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address' => $request->address,
            'link_address' => $request->map,
            'price' => $request->price,
            'country_id' => $request->country_id,
            'region_id' => $request->regions_id,
            'key' => $request->key,
        ]);

        toastr()->success('Success', 'Successfully updated');
        return redirect()->route('admin.branch.index');
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $client = Clients::findOrFail($id);

            $notificationSent = false;
            $notificationTitle = 'حذف الحساب';
            $notificationBody = 'تم حذف حسابك بنجاح.';

            $deviceTokens = $client->deviceToken()->pluck('device_token')->toArray();
            foreach ($deviceTokens as $deviceToken) {
                $notificationRequest = new Request([
                    'device_token' => $deviceToken,
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'icon' => '',
                ]);

                $response = $this->sendFirebaseNotification($notificationRequest);
                if ($response && $response->getStatusCode() === 200) {
                    $notificationSent = true;
                } else {
                    \Log::error('Failed to send deletion notification to client', [
                        'client_id' => $client->id,
                        'device_token' => $deviceToken,
                        'error' => $response ? ($response->getData()->error ?? 'Unknown error') : 'No response',
                    ]);
                }
            }

            // Delete the client
            $client->delete();

            DB::commit();

            Toastr::success('تم حذف المستخدم بنجاح' . ($notificationSent ? ' وتم إرسال الإشعار' : ''), 'نجاح');
            return redirect()->route('admin.client.providers');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Toastr::error('العميل غير موجود', 'خطأ');
            return redirect()->route('admin.client.providers');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('حدث خطأ: ' . $e->getMessage(), 'خطأ');
            return redirect()->route('admin.client.providers');
        }
    }


    public function getRegions($country_id)
    {
        $regions = Region::where('country_id', $country_id)->get();
        return response()->json($regions);
    }


    public function changeStatus($id)
    {
        try {
            DB::beginTransaction();

            $client = Clients::findOrFail($id);

            $newStatus = $client->status == 1 ? 0 : 1;
            $client->status = $newStatus;
            $client->save();

            $notificationSent = false;
            $notificationTitle = $newStatus == 1 ? 'تفعيل الحساب' : 'إلغاء تفعيل الحساب';
            $notificationBody = $newStatus == 1 ? 'تم تفعيل حسابك بنجاح.' : 'تم إلغاء تفعيل حسابك.';

            $deviceTokens = $client->deviceToken()->pluck('device_token')->toArray();
            foreach ($deviceTokens as $deviceToken) {
                $notificationRequest = new Request([
                    'device_token' => $deviceToken,
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'icon' => '',
                ]);

                $response = $this->sendFirebaseNotification($notificationRequest);
                if ($response && $response->getStatusCode() === 200) {
                    $notificationSent = true;
                } else {
                    \Log::error('Failed to send status change notification to client', [
                        'client_id' => $client->id,
                        'device_token' => $deviceToken,
                        'new_status' => $newStatus,
                        'error' => $response ? ($response->getData()->error ?? 'Unknown error') : 'No response',
                    ]);
                }
            }

            DB::commit();

            Toastr::success('تم تحديث حالة العميل بنجاح' . ($notificationSent ? ' وتم إرسال الإشعار' : ''), 'نجاح');
            return redirect()->back();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Toastr::error('العميل غير موجود', 'خطأ');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('حدث خطأ: ' . $e->getMessage(), 'خطأ');
            return redirect()->back();
        }
    }
    public function searchByPhone(Request $request)
    {
        try {
            $phone = $request->query('phone');
            $client = Clients::where('phone', $phone)
                ->orWhere('phone2', $phone)
                ->select('name', 'country_id', 'region_id', 'address')
                ->first();

            return response()->json([
                'success' => true,
                'client' => $client ? [
                    'name' => $client->name,
                    'country_id' => $client->country_id,
                    'region_id' => $client->region_id,
                    'address' => $client->address,
                    'products' => $client->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'sale_price' => $product->sale_price,
                            'in_stock_quantity' => $product->in_stock_quantity,
                        ];
                    })->toArray(),
                ] : null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in searchByPhone: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching client'], 500);
        }
    }

    public function sendNotificationToUser($id)
    {

        $client = Clients::findOrFail($id);
        return view('Admin.clients.sendnotification', compact('client'));
    }

    //sendnotification
    public function sendnotification(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:1000',
            ], [
                'title.required' => 'حقل العنوان مطلوب.',
                'title.string' => 'حقل العنوان يجب أن يكون نصًا.',
                'title.max' => 'حقل العنوان يجب ألا يتجاوز 255 حرفًا.',
                'content.required' => 'حقل المحتوى مطلوب.',
                'content.string' => 'حقل المحتوى يجب أن يكون نصًا.',
                'content.max' => 'حقل المحتوى يجب ألا يتجاوز 1000 حرف.',
            ]);

            // Validate the id parameter
            if (!in_array($id, [2, 1])) {
                throw new \Exception('نوع غير صالح. يجب أن يكون 1 زباين أو 2 تجار.');
            }

            DB::beginTransaction();

            // Get representatives or drivers based on type
            $typeName = $id == 1 ? 'زباين' : 'تجار';
            $representatives = Clients::where('type', $id)->get();

            if ($representatives->isEmpty()) {
                DB::rollBack();
                Toastr::error("لم يتم العثور على {$typeName}", 'خطأ');
                return redirect()->route('admin.client.index');
            }

            // Collect device tokens
            $deviceTokens = DeviceToken::where('model_type', 'App\Models\Clients')
                ->whereIn('model_id', $representatives->pluck('id'))
                ->pluck('device_token')
                ->toArray();

            if (empty($deviceTokens)) {
                DB::rollBack();
                Toastr::warning("لا توجد أجهزة مسجلة ل{$typeName}", 'تحذير');
                return redirect()->route('admin.client.index');
            }

            // Send notifications to all device tokens
            $notificationSent = false;
            foreach ($deviceTokens as $token) {
                $notificationRequest = new Request([
                    'device_token' => $token,
                    'title' => $request->title,
                    'body' => $request->content,
                    'icon' => '',
                ]);

                $response = $this->sendFirebaseNotification($notificationRequest);
                if ($response && $response->getStatusCode() === 200) {
                    $notificationSent = true;
                } else {
                    \Log::error('Failed to send notification to device', [
                        'type' => $id,
                        'type_name' => $typeName,
                        'device_token' => $token,
                        'title' => $request->title,
                        'error' => $response ? ($response->getData()->error ?? 'Unknown error') : 'No response',
                    ]);
                }
            }

            DB::commit();

            if ($notificationSent) {
                Toastr::success("تم إرسال الإشعار إلى {$typeName} بنجاح", 'نجاح');
            } else {
                Toastr::warning("تم المحاولة ولكن فشل إرسال بعض الإشعارات إلى {$typeName}", 'تحذير');
            }
            return redirect()->route('admin.client.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('حدث خطأ: ' . $e->getMessage(), 'خطأ');
            return redirect()->route('admin.client.index');
        }
    }
    public function sendNotificationSingle(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'client_id' => 'required|exists:clients,id',
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:1000',
            ], [
                'client_id.required' => 'حقل اختيار الزبون مطلوب.',
                'client_id.exists' => 'الزبون المحدد غير موجود.',
                'title.required' => 'حقل العنوان مطلوب.',
                'title.string' => 'حقل العنوان يجب أن يكون نصًا.',
                'title.max' => 'حقل العنوان يجب ألا يتجاوز 255 حرفًا.',
                'content.required' => 'حقل المحتوى مطلوب.',
                'content.string' => 'حقل المحتوى يجب أن يكون نصًا.',
                'content.max' => 'حقل المحتوى يجب ألا يتجاوز 1000 حرف.',
            ]);

            // Ensure the provided id matches the client_id from the form
            if ($id != $request->client_id) {
                throw new \Exception('معرف الزبون غير متطابق.');
            }

            DB::beginTransaction();

            // Get the specific client
            $client = Clients::findOrFail($id);

            // Collect device tokens for the client
            $deviceTokens = DeviceToken::where('model_type', 'App\Models\Clients')
                ->where('model_id', $client->id)
                ->pluck('device_token')
                ->toArray();

            if (empty($deviceTokens)) {
                DB::rollBack();
                Toastr::warning("لا توجد أجهزة مسجلة للزبون {$client->name}", 'تحذير');
                return redirect()->route('admin.client.index');
            }

            // Send notifications to the client's device tokens
            $notificationSent = false;
            foreach ($deviceTokens as $token) {
                $notificationRequest = new Request([
                    'device_token' => $token,
                    'title' => $request->title,
                    'body' => $request->content,
                    'icon' => '',
                ]);

                $response = $this->sendFirebaseNotification($notificationRequest);
                if ($response && $response->getStatusCode() === 200) {
                    $notificationSent = true;
                } else {
                    \Log::error('Failed to send notification to device', [
                        'client_id' => $client->id,
                        'client_name' => $client->name,
                        'device_token' => $token,
                        'title' => $request->title,
                        'error' => $response ? ($response->getData()->error ?? 'Unknown error') : 'No response',
                    ]);
                }
            }

            DB::commit();

            if ($notificationSent) {
                Toastr::success("تم إرسال الإشعار إلى {$client->name} بنجاح", 'نجاح');
            } else {
                Toastr::warning("تم المحاولة ولكن فشل إرسال الإشعار إلى {$client->name}", 'تحذير');
            }
            return redirect()->route('admin.client.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('حدث خطأ: ' . $e->getMessage(), 'خطأ');
            return redirect()->route('admin.client.index');
        }
    }
    public function sendnotifications()
    {

        return view('Admin.clients.sendnotifications');
    }
}
