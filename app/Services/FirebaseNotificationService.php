<?php

namespace App\Services;

use App\Models\Notification;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FirebaseNotificationService
{
    protected $messaging;
    protected $auth;

    public function __construct()
    {
        try {
            $firebase = (new Factory)
                ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS')))
                ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

            $this->messaging = $firebase->createMessaging();
            $this->auth = $firebase->createAuth();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send notification to single device
     */
    public function sendToDevice(string $deviceToken, array $notificationData, array $data = []): bool
    {
        try {
            $notification = FirebaseNotification::create(
                $notificationData['title'] ?? 'Notification',
                $notificationData['body'] ?? '',
                $notificationData['image'] ?? null
            );

            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData(array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'sound' => 'default',
                    'badge' => '1',
                ]));

            // Optional: Add platform-specific configurations
            if (isset($data['android_channel_id'])) {
                $message = $message->withAndroidConfig(
                    AndroidConfig::fromArray([
                        'notification' => [
                            'channel_id' => $data['android_channel_id'],
                            'sound' => 'default',
                            'priority' => 'high',
                        ],
                        'ttl' => '3600s',
                    ])
                );
            }

            $this->messaging->send($message);

            Log::info('Firebase notification sent successfully', [
                'device_token' => substr($deviceToken, 0, 20) . '...',
                'title' => $notificationData['title'] ?? '',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase notification failed: ' . $e->getMessage(), [
                'device_token' => substr($deviceToken, 0, 20) . '...',
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $deviceTokens, array $notificationData, array $data = []): array
    {
        if (empty($deviceTokens)) {
            return ['successful' => 0, 'failed' => 0];
        }

        try {
            $notification = FirebaseNotification::create(
                $notificationData['title'] ?? 'Notification',
                $notificationData['body'] ?? '',
                $notificationData['image'] ?? null
            );

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData(array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]));

            $report = $this->messaging->sendMulticast($message, $deviceTokens);

            return [
                'successful' => $report->successes()->count(),
                'failed' => $report->failures()->count(),
                'invalid_tokens' => $report->invalidTokens(),
            ];
        } catch (\Exception $e) {
            Log::error('Firebase multicast notification failed: ' . $e->getMessage());
            return ['successful' => 0, 'failed' => count($deviceTokens), 'invalid_tokens' => []];
        }
    }

    /**
     * Send notification to topic subscribers
     */
    public function sendToTopic(string $topic, array $notificationData, array $data = []): bool
    {
        try {
            $notification = FirebaseNotification::create(
                $notificationData['title'] ?? 'Notification',
                $notificationData['body'] ?? '',
                $notificationData['image'] ?? null
            );

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData(array_merge($data, [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ]));

            $this->messaging->send($message);

            Log::info('Firebase topic notification sent', ['topic' => $topic]);
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase topic notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Subscribe device to topic
     */
    public function subscribeToTopic(array $deviceTokens, string $topic): bool
    {
        try {
            $this->messaging->subscribeToTopic($topic, $deviceTokens);
            Log::info('Device subscribed to topic', [
                'topic' => $topic,
                'device_count' => count($deviceTokens),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase topic subscription failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Unsubscribe device from topic
     */
    public function unsubscribeFromTopic(array $deviceTokens, string $topic): bool
    {
        try {
            $this->messaging->unsubscribeFromTopic($topic, $deviceTokens);
            Log::info('Device unsubscribed from topic', [
                'topic' => $topic,
                'device_count' => count($deviceTokens),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase topic unsubscription failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification with image
     */
    public function sendWithImage(string $deviceToken, array $notificationData, string $imageUrl, array $data = []): bool
    {
        $notificationData['image'] = $imageUrl;
        return $this->sendToDevice($deviceToken, $notificationData, $data);
    }

    /**
     * Validate Firebase token
     */
    public function validateToken(string $token): bool
    {
        try {
            $this->auth->verifyIdToken($token);
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase token validation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification with custom sound
     */
    public function sendWithCustomSound(string $deviceToken, array $notificationData, string $sound = 'default', array $data = []): bool
    {
        try {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(FirebaseNotification::create(
                    $notificationData['title'] ?? 'Notification',
                    $notificationData['body'] ?? '',
                    $notificationData['image'] ?? null
                ))
                ->withData(array_merge($data, [
                    'sound' => $sound,
                ]));

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('Firebase notification with custom sound failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification and save to database
     */
    public function sendAndSaveNotification($user, array $notificationData, array $firebaseData = [], string $deviceToken = null): array
    {
        $result = [
            'database_saved' => false,
            'firebase_sent' => false,
            'notification' => null,
        ];

        // Save to database
        try {
            $notification = $user->notify($notificationData);
            $result['database_saved'] = true;
            $result['notification'] = $notification;
        } catch (\Exception $e) {
            Log::error('Failed to save notification to database: ' . $e->getMessage());
        }

        // Send to Firebase if device token provided
        if ($deviceToken) {
            $result['firebase_sent'] = $this->sendToDevice(
                $deviceToken,
                [
                    'title' => $notificationData['title'] ?? 'Notification',
                    'body' => $notificationData['message'] ?? '',
                ],
                array_merge($firebaseData, [
                    'notification_id' => $notification->id ?? null,
                    'type' => $notificationData['type'] ?? 'info',
                ])
            );
        }

        return $result;
    }
}
