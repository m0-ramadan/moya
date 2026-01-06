<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChatsController extends Controller
{
    /**
     * عرض قائمة المحادثات
     */
    public function index(Request $request)
    {
        // إحصائيات المحادثات
        $stats = [
            'total_chats' => Chat::count(),
            'total_messages' => Message::count(),
            'unread_messages' => Message::where('is_read', false)->count(),
            'today_chats' => Chat::whereDate('created_at', Carbon::today())->count(),
            'today_messages' => Message::whereDate('created_at', Carbon::today())->count(),
            'user_user_chats' => Chat::where('type', 'user_user')->count(),
            'user_driver_chats' => Chat::where('type', 'user_driver')->count(),
            'driver_driver_chats' => Chat::where('type', 'driver_driver')->count(),
        ];

        // الاستعلام الأساسي للمحادثات مع البيانات المرتبطة
        $query = Chat::with([
            'messages' => function ($q) {
                $q->latest()->limit(5); // آخر 5 رسائل لكل محادثة
            },
            'latestMessage.sender',
            'participantDetails' // علاقة جديدة سنقوم بإنشائها
        ]);

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب المشاركين
        if ($request->filled('participant')) {
            $participantId = $request->participant;
            $query->whereJsonContains('participants', $participantId);
        }

        // فلترة حسب الرسائل غير المقروءة
        if ($request->has('unread_only')) {
            $query->whereHas('messages', function ($q) {
                $q->where('is_read', false);
            });
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // بحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('chat_uuid', 'like', "%{$search}%")
                    ->orWhere('last_message', 'like', "%{$search}%")
                    ->orWhereHas('messages', function ($q2) use ($search) {
                        $q2->where('message', 'like', "%{$search}%");
                    });
            });
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'last_message_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // الترقيم
        $chats = $query->paginate(20);

        // جلب المستخدمين والسائقين للفلترة
        $users = User::select('id', 'name')->get();
        $drivers = Driver::select('id', 'full_name')->get();

        // حساب الرسائل غير المقروءة لكل محادثة
        foreach ($chats as $chat) {
            $chat->unread_count = $chat->messages()
                ->where('is_read', false)
                ->count();

            // جلب معلومات المشاركين
            $chat->participants_info = $this->getParticipantsInfo($chat);
        }

        return view('Admin.chats.index', compact('chats', 'stats', 'users', 'drivers'));
    }

    /**
     * عرض تفاصيل محادثة معينة
     */
    public function show(Chat $chat)
    {
        // تحميل البيانات المرتبطة
        $chat->load([
            'messages.sender',
            'messages' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }
        ]);

        // جلب معلومات المشاركين
        $participantsInfo = $this->getParticipantsInfo($chat);

        // جلب الإحصائيات الخاصة بالمحادثة
        $chatStats = [
            'total_messages' => $chat->messages()->count(),
            'unread_messages' => $chat->messages()->where('is_read', false)->count(),
            'voice_messages' => $chat->messages()->where('message_type', 'voice')->count(),
            'image_messages' => $chat->messages()->where('message_type', 'image')->count(),
            'file_messages' => $chat->messages()->where('message_type', 'file')->count(),
            'first_message_date' => $chat->messages()->oldest()->first()->created_at ?? null,
            'last_message_date' => $chat->messages()->latest()->first()->created_at ?? null,
        ];

        // تحديث حالة القراءة للرسائل
        $chat->messages()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return view('Admin.chats.show', compact('chat', 'participantsInfo', 'chatStats'));
    }

    /**
     * حذف محادثة
     */
    public function destroy(Chat $chat)
    {
        try {
            // حذف جميع الرسائل أولاً
            $chat->messages()->delete();

            // حذف المحادثة
            $chat->delete();

            return redirect()->route('admin.chats.index')
                ->with('success', 'تم حذف المحادثة بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('admin.chats.index')
                ->with('error', 'حدث خطأ أثناء حذف المحادثة');
        }
    }

    /**
     * حذف رسالة معينة
     */
    public function destroyMessage(Message $message)
    {
        try {
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الرسالة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الرسالة'
            ], 500);
        }
    }

    /**
     * عرض إحصائيات المحادثات
     */
    public function statistics()
    {
        // إحصائيات عامة
        $generalStats = [
            'total_chats' => Chat::count(),
            'total_messages' => Message::count(),
            'avg_messages_per_chat' => Chat::count() > 0 ? round(Message::count() / Chat::count(), 2) : 0,
            'active_chats_today' => Chat::whereDate('last_message_at', Carbon::today())->count(),
            'active_chats_week' => Chat::whereBetween('last_message_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
        ];

        // إحصائيات حسب النوع
        $typeStats = [
            'user_user' => [
                'count' => Chat::where('type', 'user_user')->count(),
                'messages' => Message::whereHas('chat', function ($q) {
                    $q->where('type', 'user_user');
                })->count(),
            ],
            'user_driver' => [
                'count' => Chat::where('type', 'user_driver')->count(),
                'messages' => Message::whereHas('chat', function ($q) {
                    $q->where('type', 'user_driver');
                })->count(),
            ],
            'driver_driver' => [
                'count' => Chat::where('type', 'driver_driver')->count(),
                'messages' => Message::whereHas('chat', function ($q) {
                    $q->where('type', 'driver_driver');
                })->count(),
            ],
        ];

        // إحصائيات الرسائل حسب النوع
        $messageTypeStats = Message::select('message_type', DB::raw('COUNT(*) as count'))
            ->groupBy('message_type')
            ->get()
            ->pluck('count', 'message_type')
            ->toArray();

        // إحصائيات الرسائل خلال الأسبوع
        $weekMessages = Message::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // أكثر المستخدمين نشاطاً
        $topUsers = Message::select(
            'sender_id',
            'sender_type',
            DB::raw('COUNT(*) as message_count')
        )
            ->groupBy('sender_id', 'sender_type')
            ->orderBy('message_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                if ($item->sender_type === 'App\Models\User') {
                    $user = User::find($item->sender_id);
                    $item->name = $user ? $user->name : 'Unknown';
                    $item->type = 'User';
                } else {
                    $driver = Driver::find($item->sender_id);
                    $item->name = $driver ? $driver->name : 'Unknown';
                    $item->type = 'Driver';
                }
                return $item;
            });

        return view('Admin.chats.statistics', compact(
            'generalStats',
            'typeStats',
            'messageTypeStats',
            'weekMessages',
            'topUsers'
        ));
    }

    /**
     * عرض المحادثات الحية (Real-time)
     */
    public function live()
    {
        // جلب أحدث المحادثات النشطة
        $recentChats = Chat::with(['latestMessage.sender'])
            ->where('last_message_at', '>=', Carbon::now()->subMinutes(30))
            ->orderBy('last_message_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($chat) {
                $chat->unread_count = $chat->messages()
                    ->where('is_read', false)
                    ->count();
                $chat->participants_info = $this->getParticipantsInfo($chat);
                return $chat;
            });

        // جلب أحدث الرسائل مباشرة
        $recentMessages = Message::with(['chat', 'sender'])
            ->where('created_at', '>=', Carbon::now()->subMinutes(10))
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('Admin.chats.live', compact('recentChats', 'recentMessages'));
    }

    /**
     * الحصول على معلومات المشاركين
     */
    private function getParticipantsInfo(Chat $chat)
    {
        $participants = [];

        foreach ($chat->participants as $participantId) {
            // محاولة العثور على مستخدم
            $user = User::find($participantId);
            if ($user) {
                $participants[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'type' => 'user',
                    'is_online' => $user->last_seen && $user->last_seen->diffInMinutes(now()) < 5
                ];
                continue;
            }

            // محاولة العثور على سائق
            $driver = Driver::find($participantId);
            if ($driver) {
                $participants[] = [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email,
                    'avatar' => $driver->avatar,
                    'type' => 'driver',
                    'is_online' => $driver->last_seen && $driver->last_seen->diffInMinutes(now()) < 5
                ];
            }
        }

        return $participants;
    }

    /**
     * تصدير المحادثات
     */
    public function export(Request $request)
    {
        // هنا يمكنك إضافة كود لتصدير المحادثات بصيغة CSV أو Excel
        // حسب احتياجاتك
    }
}
