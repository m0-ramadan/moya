<?php

namespace App\Http\Controllers\Api\Driver;

use App\DataTransferObjects\PhoneLoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Driver\CompleteProfileRequest;
use App\Http\Requests\Driver\RegisterDriverRequest;
use App\Http\Resources\Driver\CountryResource;
use App\Http\Resources\Driver\DriverResource;
use App\Models\Country;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use App\Traits\UploadFileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DriverAuthController extends Controller
{
    use ApiResponseTrait, UploadFileTrait;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * إرسال OTP للسائق
     */
    public function sendOtp(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => ['required', 'string'],
                'country_code' => ['required', 'string'],
            ]);

            $dto = new PhoneLoginData(
                $request->country_code,
                $request->phone_number
            );

            $res = $this->authService->sendOtp($dto, $request);

            return $this->successResponse([
                'phone' => $res['phone'],
                'method' => $res['method'],
                'otp' => $res['otp'],
            ], 'تم إرسال رمز التحقق بنجاح');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * التحقق من OTP للسائق
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $fullPhone = $request->input('phone_number');
            $otp = $request->input('otp');

            $res = $this->authService->verifyOtp($fullPhone, $otp);
            $user = $res['user'];

            // التحقق إذا كان المستخدم مسجل كسائق
            $driver = $user->driver;
            $isRegistered = $driver && $driver->is_verified;

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'phone' => $user->full_phone,
                    'name' => $user->name,
                    'is_verified' => $user->isPhoneVerified(),
                    'is_driver' => ! is_null($driver),
                    'driver_status' => $driver ? $driver->status : null,
                    'driver_is_verified' => $driver ? $driver->is_verified : false,
                ],
                'token' => $res['token'],
                'token_type' => 'Bearer',
                'is_registered' => $isRegistered,
            ], 'تم التحقق من رمز OTP بنجاح');
        } catch (\Exception $e) {
            return $this->validationError(['otp' => [$e->getMessage()]]);
        }
    }

    /**
     * تسجيل سائق جديد
     */



public function register(Request $request)
{
    Log::info('Driver Register: request started');

    try {
        // 1️⃣ المستخدم الحالي
        $user = auth()->user();
        Log::info('Driver Register: auth user', [
            'user_id' => optional($user)->id,
        ]);

        if (! $user) {
            Log::warning('Driver Register: unauthenticated');
            return $this->errorResponse('يجب تسجيل الدخول أولاً', 401);
        }

        // 2️⃣ بيانات الريكوست (من غير الملفات)
        Log::info('Driver Register: request data', [
            'data' => $request->except([
                'id_image',
                'id_image_back',
                'license_image',
                'license_image_back',
                'photo',
                'vehicle_registration_image',
            ]),
        ]);

        // 3️⃣ أسماء الملفات المرفوعة
        Log::info('Driver Register: uploaded files', [
            'files' => array_keys($request->allFiles()),
        ]);

        // 4️⃣ Validation
        Log::info('Driver Register: validation started');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',

            'citizenship' => 'required|in:saudi,resident',
            'country_id' => 'required_if:citizenship,resident|exists:countries,id',

            'national_id' => 'required|string|max:20|unique:drivers,national_id',
            'id_number' => 'required|string|max:20|unique:drivers,id_number',
            'id_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'id_image_back' => 'required|image|mimes:jpg,jpeg,png|max:5120',

            'license_number' => 'required|string|max:50|unique:drivers,license_number',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:today',
            'license_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'license_image_back' => 'required|image|mimes:jpg,jpeg,png|max:5120',

            'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',

            'vehicle_size' => 'required',
            'is_vehicle_owner' => 'required',
            'vehicle_plate_number' => 'required|string|max:20',
            'vehicle_registration_number' => 'nullable|string|max:50',
            'vehicle_registration_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',

            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',

            'preferred_working_hours' => 'nullable|string',
            'max_daily_orders' => 'nullable|integer|min:1|max:20',
            'radius_km' => 'nullable|integer|min:5|max:100',

            'bank_name' => 'nullable|string|max:100',
            'iban_number' => 'nullable|string|max:34',
        ]);

        if ($validator->fails()) {
            Log::warning('Driver Register: validation failed', [
                'errors' => $validator->errors(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        Log::info('Driver Register: validation passed');

        // 5️⃣ منع التكرار
        if ($user->driver) {
            Log::warning('Driver Register: user already driver', [
                'user_id' => $user->id,
            ]);

            return $this->errorResponse('أنت مسجل كسائق بالفعل', 400);
        }

        // 6️⃣ بدء المعاملة
        DB::beginTransaction();
        Log::info('Driver Register: DB transaction started');

        // 7️⃣ رفع الملفات
        Log::info('Driver Register: uploading documents');

        $uploadedFiles = $this->uploadDriverDocuments($request, $user->id);

        Log::info('Driver Register: documents uploaded', [
            'files' => array_keys($uploadedFiles),
        ]);

        // 8️⃣ إنشاء السائق
        Log::info('Driver Register: creating driver');
$driver = Driver::create([

    // الربط بالمستخدم
    'user_id' => $user->id,

    // الجنسية
    'citizenship' => $validator->validated()['citizenship'] ?? null,
    'country_id'  => $validator->validated()['country_id'] ?? null,

    // بيانات شخصية
    'date_of_birth' => $validator->validated()['date_of_birth'] ?? null,
    'national_id'   => $validator->validated()['national_id'] ?? null,
    'iqama_number'  => $validator->validated()['iqama_number'] ?? null,
    'iqama_expiry_date' => $validator->validated()['iqama_expiry_date'] ?? null,

    // الصور
    'personal_photo' => $uploadedFiles['personal_photo'] ?? null,
    'id_image_front' => $uploadedFiles['id_image_front'] ?? null,
    'id_image_back'  => $uploadedFiles['id_image_back'] ?? null,

    // رخصة القيادة
    'license_number'       => $validator->validated()['license_number'] ?? null,
    'license_expiry_date'  => $validator->validated()['license_expiry_date'] ?? null,
    'license_image_front'  => $uploadedFiles['license_image_front'] ?? null,
    'license_image_back'   => $uploadedFiles['license_image_back'] ?? null,

    // المركبة
    'vehicle_size'                 => $validator->validated()['vehicle_size'] ?? null,
    'is_vehicle_owner'             => $validator->validated()['is_vehicle_owner'] ?? null,
    'vehicle_plate_number'         => $validator->validated()['vehicle_plate_number'] ?? null,
    'vehicle_registration_number'  => $validator->validated()['vehicle_registration_number'] ?? null,
    'vehicle_residency_number'     => $validator->validated()['vehicle_residency_number'] ?? null,

    // رخصة السير
    'vehicle_registration_image' => $uploadedFiles['vehicle_registration_image'] ?? null,

    // التحقق والحالة
    'is_verified' => false,
    'verified_at' => null,
    'rejection_reason' => null,

    'status'    => 'pending',
    'is_active' => false,
]);


        Log::info('Driver Register: driver created', [
            'driver_id' => $driver->id,
        ]);

        // 9️⃣ تحديث المستخدم
        $user->update(['type' => 'driver']);
        Log::info('Driver Register: user updated to driver', [
            'user_id' => $user->id,
        ]);

        DB::commit();
        Log::info('Driver Register: DB transaction committed');

        // 🔟 إشعار الأدمن
        try {
            $this->sendAdminNotification($driver);
            Log::info('Driver Register: admin notified');
        } catch (\Exception $e) {
            Log::error('Driver Register: admin notification failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return $this->successResponse([
            'driver' => $driver,
        ], 'تم تسجيل طلبك بنجاح وسيتم مراجعته خلال 24 ساعة');

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Driver Register: exception occurred', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return $this->errorResponse('فشل تسجيل السائق', 500);
    }
}



    /**
     * إكمال ملف السائق (للسائقين المسجلين)
     */
    public function completeProfile(CompleteProfileRequest $request)
    {
        try {
            $user = $request->user();
            $driver = $user->driver;

            if (! $driver) {
                return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
            }

            DB::beginTransaction();

            $data = $request->validated();

            // رفع الصور المحدثة
            if ($request->hasFile('photo')) {
                $this->deleteOldFile($driver->personal_photo);
                $data['personal_photo'] = $this->uploadProfilePhoto($request->file('photo'), intval($driver->id));
            }

            if ($request->hasFile('id_image')) {
                $this->deleteOldFile($driver->id_image);
                $data['id_image'] = $this->uploadIdImage($request->file('id_image'), $driver->id, 'front');
            }

            if ($request->hasFile('id_image_back')) {
                $this->deleteOldFile($driver->id_image_back);
                $data['id_image_back'] = $this->uploadIdImage($request->file('id_image_back'), $driver->id, 'back');
            }
            if ($request->has('name') || $request->hasFile('photo')) {
                $this->deleteOldFile($user->avatar);
                $photo = $this->uploadProfilePhoto($request->file('photo'), intval($driver->id));

                $user->update(['name' => $request->name, 'avatar' => $photo]);
            }

            // تحديث بيانات السائق
            $driver->update($data);

            // // تحديث بيانات المركبة
            // if ($request->hasAny(['vehicle_plate_number', 'vehicle_model', 'vehicle_year'])) {
            //   //  $vehicle = $driver->vehicle ?? new \App\Models\Vehicle();
            //     $vehicle->driver_id = $driver->id;
            //     $vehicle->fill($request->only([
            //         'vehicle_plate_number',
            //         'vehicle_model',
            //         'vehicle_year',
            //         'vehicle_color',
            //         'vehicle_type',
            //         'capacity_liters'
            //     ]));
            //     $vehicle->save();
            // }

            DB::commit();

            return $this->successResponse([
                'driver' => new DriverResource($driver),
            ], 'تم تحديث الملف الشخصي بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse('فشل تحديث الملف الشخصي: '.$e->getMessage(), 500);
        }
    }

    /**
     * جلب ملف السائق
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $driver = $user->driver()->first();

        if (! $driver) {
            return $this->errorResponse('لم يتم العثور على بيانات السائق', 404);
        }

        return $this->successResponse([
            'driver' => new DriverResource($driver),
            'documents_status' => $driver->documents_status,
            'status_badge' => $driver->status_badge,
            'has_expired_documents' => $driver->has_expired_documents,
            'next_renewal_date' => $driver->next_renewal_date,
        ]);
    }

    /**
     * جلب الدول المتاحة
     */
    public function countries()
    {
        $countries = Country::active()->ordered()->get();

        return $this->successResponse(CountryResource::collection($countries));
    }

    /**
     * تسجيل خروج السائق
     */
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()?->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * التحقق من حالة التسجيل
     */
    public function checkRegistration(Request $request)
    {
        $user = $request->user();
        $driver = $user->driver;

        if (! $driver) {
            return $this->successResponse([
                'is_registered' => false,
                'message' => 'لم يتم تسجيلك كسائق بعد',
            ]);
        }

        return $this->successResponse([
            'is_registered' => true,
            'driver' => $driver->only(['id', 'full_name', 'status', 'is_verified', 'is_active']),
            'profile_completion_percentage' => $this->calculateProfileCompletion($driver),
            'missing_documents' => $this->getMissingDocuments($driver),
        ]);
    }

    // ========== الدوال المساعدة ==========

    /**
     * رفع وثائق السائق
     */
    private function uploadDriverDocuments($request, $userId)
    {
        $uploads = [];

        // رفع الصورة الشخصية
        if ($request->hasFile('photo')) {
            $uploads['photo'] = $this->uploadProfilePhoto($request->file('photo'), $userId);
        }

        // رفع صورة الهوية (الوجه الأمامي)
        if ($request->hasFile('id_image')) {
            $uploads['id_image'] = $this->uploadIdImage($request->file('id_image'), $userId, 'front');
        }

        // رفع صورة الهوية (الوجه الخلفي)
        if ($request->hasFile('id_image_back')) {
            $uploads['id_image_back'] = $this->uploadIdImage($request->file('id_image_back'), $userId, 'back');
        }

        // رفع رخصة القيادة (الوجه الأمامي)
        if ($request->hasFile('license_image')) {
            $uploads['license_image'] = $this->uploadLicenseImage($request->file('license_image'), $userId, 'front');
        }

        // رفع رخصة القيادة (الوجه الخلفي)
        if ($request->hasFile('license_image_back')) {
            $uploads['license_image_back'] = $this->uploadLicenseImage($request->file('license_image_back'), $userId, 'back');
        }

        // رفع رخصة السيارة
        if ($request->hasFile('vehicle_registration_image')) {
            $uploads['vehicle_registration_image'] = $this->uploadVehicleRegistrationImage(
                $request->file('vehicle_registration_image'),
                $userId
            );
        }

        return $uploads;
    }

    /**
     * إنشاء مركبة
     */
    // private function createVehicle($driverId, $request)
    // {
    //     $vehicleData = [
    //         'driver_id' => $driverId,
    //         'plate_number' => $request->vehicle_plate_number,
    //         'registration_number' => $request->vehicle_registration_number,
    //         'model' => $request->vehicle_model,
    //         'year' => $request->vehicle_year,
    //         'color' => $request->vehicle_color,
    //         'type' => $request->vehicle_type,
    //         'capacity_liters' => $request->vehicle_capacity,
    //     ];

    //     return \App\Models\Vehicle::create($vehicleData);
    // }

    /**
     * إرسال إشعار للمسؤول
     */
    private function sendAdminNotification($driver)
    {
        try {
            // إرسال إشعار Firebase للمسؤولين
            $adminTokens = \App\Models\User::with('activeDeviceTokens')
                ->get()
                ->pluck('activeDeviceTokens')
                ->flatten()
                ->pluck('token')
                ->toArray();

            if (! empty($adminTokens)) {
                $firebaseService = app(\App\Services\FirebaseNotificationService::class);

                $firebaseService->sendToMultipleDevices($adminTokens, [
                    'title' => 'طلب تسجيل سائق جديد',
                    'body' => 'تم تقديم طلب تسجيل سائق جديد: '.$driver->full_name,
                    'image' => null,
                ], [
                    'driver_id' => $driver->id,
                    'type' => 'new_driver_registration',
                    'action' => 'review_driver',
                ]);
            }

            // تسجيل في قاعدة البيانات
            DB::table('admin_notifications')->insert([
                'title' => 'طلب تسجيل سائق جديد',
                'message' => 'تم تقديم طلب تسجيل سائق جديد: '.$driver->full_name,
                'type' => 'driver_registration',
                'data' => json_encode(['driver_id' => $driver->id]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification: '.$e->getMessage());
        }
    }

    /**
     * حساب نسبة اكتمال الملف
     */
    private function calculateProfileCompletion($driver)
    {
        $requiredFields = [
            'first_name',
            'father_name',
            'grandfather_name',
            'family_name',
            'date_of_birth',
            'id_number',
            'license_number',
            'expiry_date',
            'citizenship',
            'vehicle_size',
            'is_vehicle_owner',
        ];

        $documentFields = [
            'photo',
            'id_image',
            'id_image_back',
            'license_image',
            'license_image_back',
            'vehicle_registration_image',
        ];

        $completed = 0;
        $total = count($requiredFields) + count($documentFields);

        // التحقق من الحقول المطلوبة
        foreach ($requiredFields as $field) {
            if (! empty($driver->$field)) {
                $completed++;
            }
        }

        // التحقق من المستندات
        foreach ($documentFields as $field) {
            if (! empty($driver->$field) && file_exists(public_path($driver->$field))) {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }

    /**
     * جلب المستندات المفقودة
     */
    private function getMissingDocuments($driver)
    {
        $documents = [
            'photo' => 'الصورة الشخصية',
            'id_image' => 'صورة الهوية (الوجه الأمامي)',
            'id_image_back' => 'صورة الهوية (الوجه الخلفي)',
            'license_image' => 'رخصة القيادة (الوجه الأمامي)',
            'license_image_back' => 'رخصة القيادة (الوجه الخلفي)',
            'vehicle_registration_image' => 'رخصة السيارة',
        ];

        $missing = [];

        foreach ($documents as $field => $name) {
            if (empty($driver->$field) || ! file_exists(public_path($driver->$field))) {
                $missing[] = $name;
            }
        }

        return $missing;
    }
}
