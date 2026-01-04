<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\DeviceToken;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\FacadesLog;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseNotificationService;
use App\Http\Requests\SendNotificationRequest;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    private $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        //  $this->middleware('auth');
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
    /**
     * Send notification to specific user by ID.
     */
    public function sendToUser(SendNotificationRequest $request)
    {
        try {

            $sender = Auth::user();
            $receiver = User::findOrFail($request->user_id);

            // إنشاء الإشعار في قاعدة البيانات
            $notification = $receiver->createNotification([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type ?? 'info',
                'data' => $request->data ?? [],
            ]);

            // إرسال إلى Firebase إذا كان مطلوبًا
            $firebaseResult = null;
            if ($request->boolean('send_to_firebase', true)) {
                $firebaseResult = $this->sendFirebaseNotification($receiver, $notification, $request);
            }

            // تسجيل النشاط (اختياري)
            //  $this->logNotificationActivity($sender, $receiver, $notification);

            return $this->successResponse(
                [
                    'notification' => $notification,
                    'firebase_result' => $firebaseResult,
                    'receiver' => [
                        'id' => $receiver->id,
                        'name' => $receiver->name,
                        'email' => $receiver->email,
                    ]
                ],
                'تم إرسال الإشعار بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
            return $this->errorResponse(
                'فشل إرسال الإشعار: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Send notification to multiple users.
     */
    public function sendToMultipleUsers(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string',
            'data' => 'nullable|array',
        ]);

        $results = [];
        $sender = Auth::user();

        foreach ($request->user_ids as $userId) {
            try {
                $receiver = User::find($userId);

                $notification = $receiver->createNotification([
                    'title' => $request->title,
                    'message' => $request->message,
                    'type' => $request->type ?? 'info',
                    'data' => $request->data ?? [],
                ]);

                // إرسال إلى Firebase
                $firebaseResult = $this->sendFirebaseNotification($receiver, $notification, $request);

                $results[] = [
                    'user_id' => $userId,
                    'success' => true,
                    'notification_id' => $notification->id,
                    'firebase_result' => $firebaseResult,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'user_id' => $userId,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $failedCount = count($results) - $successCount;

        return $this->successResponse(
            [
                'results' => $results,
                'summary' => [
                    'total' => count($results),
                    'successful' => $successCount,
                    'failed' => $failedCount,
                ]
            ],
            "تم إرسال الإشعار إلى {$successCount} مستخدم، فشل {$failedCount}"
        );
    }

    /**
     * Send Firebase notification.
     */
    private function sendFirebaseNotification(User $user, Notification $notification, Request $request): array
    {
        try {
            // الحصول على tokens الأجهزة النشطة للمستخدم
            $deviceTokens = $user->activeDeviceTokens()->pluck('token')->toArray();

            if (empty($deviceTokens)) {
                return [
                    'sent' => false,
                    'message' => 'No active device tokens found',
                    'device_count' => 0,
                ];
            }

            $sendAsBroadcast = $request->boolean('send_as_broadcast', false);
            $firebaseData = array_merge($request->data ?? [], [
                'notification_id' => $notification->id,
                'type' => $notification->type,
                'user_id' => $user->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);

            if ($sendAsBroadcast) {
                // إرسال لجميع أجهزة المستخدم
                $result = $this->firebaseService->sendToMultipleDevices(
                    $deviceTokens,
                    [
                        'title' => $notification->title,
                        'body' => $notification->message,
                    ],
                    $firebaseData
                );

                return [
                    'sent' => true,
                    'method' => 'broadcast',
                    'device_count' => count($deviceTokens),
                    'successful' => $result['successful'] ?? 0,
                    'failed' => $result['failed'] ?? 0,
                ];
            } else {
                // إرسال للأجهزة بشكل فردي (أو الجهاز الرئيسي)
                $results = [];
                foreach ($deviceTokens as $token) {
                    $success = $this->firebaseService->sendToDevice(
                        $token,
                        [
                            'title' => $notification->title,
                            'body' => $notification->message,
                        ],
                        $firebaseData
                    );

                    $results[] = [
                        'token' => substr($token, 0, 20) . '...',
                        'success' => $success,
                    ];
                }

                $successCount = count(array_filter($results, fn($r) => $r['success']));

                return [
                    'sent' => true,
                    'method' => 'individual',
                    'device_count' => count($deviceTokens),
                    'successful' => $successCount,
                    'failed' => count($deviceTokens) - $successCount,
                    'results' => $results,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Firebase notification failed: ' . $e->getMessage());
            return [
                'sent' => false,
                'error' => $e->getMessage(),
                'device_count' => 0,
            ];
        }
    }

    /**
     * Log notification activity.
     */
    // private function logNotificationActivity(User $sender, User $receiver, Notification $notification): void
    // {
    //     // يمكنك إضافة سجل للنشاط هنا
    //     activity()
    //         ->causedBy($sender)
    //         ->performedOn($receiver)
    //         ->withProperties([
    //             'notification_id' => $notification->id,
    //             'title' => $notification->title,
    //             'type' => $notification->type,
    //         ])
    //         ->log('sent_notification');
    // }

    /**
     * Register/save device token for push notifications.
     */
    public function registerDeviceToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|in:android,ios,web',
            'device_name' => 'nullable|string',
            'device_model' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        $user = Auth::user(); // ممكن null

        $deviceToken = DeviceToken::updateOrCreate(
            [
                'token' => $request->token,
            ],
            [
                'user_id'      => $user?->id, // 👈 مهم
                'device_type'  => $request->device_type,
                'device_name'  => $request->device_name,
                'device_model' => $request->device_model,
                'app_version'  => $request->app_version,
                'is_active'    => true,
            ]
        );

        return $this->successResponse(
            $deviceToken,
            'تم تسجيل الجهاز بنجاح'
        );
    }


    /**
     * Remove device token.
     */
    public function removeDeviceToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $deleted = DeviceToken::where('token', $request->token)->delete();

        if ($deleted) {
            return $this->successResponse(null, 'تم إزالة الجهاز');
        }

        return $this->errorResponse('الجهاز غير موجود', 404);
    }


    /**
     * Get user's device tokens.
     */
    public function getUserDevices($userId)
    {
        // صلاحيات: فقط المدير أو المستخدم نفسه
        $currentUser = Auth::user();

        if ($currentUser->id != $userId && !$currentUser->hasRole('admin')) {
            return $this->errorResponse('غير مصرح لك', 403);
        }

        $user = User::findOrFail($userId);
        $devices = $user->deviceTokens()->get();

        return $this->successResponse(
            $devices,
            'تم جلب أجهزة المستخدم بنجاح'
        );
    }

    /**
     * Send test notification to specific user.
     */
    public function sendTestToUser(Request $request, $userId)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string',
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($userId);

        $notification = $receiver->createNotification([
            'title' => $request->title ?? 'إشعار تجريبي',
            'message' => $request->message ?? 'هذا إشعار تجريبي لاختبار النظام',
            'type' => 'system',
            'data' => ['test' => true],
        ]);

        // إرسال Firebase
        $firebaseResult = $this->sendFirebaseNotification($receiver, $notification, $request);

        return $this->successResponse(
            [
                'notification' => $notification,
                'firebase_result' => $firebaseResult,
            ],
            'تم إرسال الإشعار التجريبي بنجاح'
        );
    }
}
