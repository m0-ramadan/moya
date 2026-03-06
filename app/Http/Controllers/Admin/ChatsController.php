<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use App\Models\Driver;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
            'messages' => fn($q) => $q->latest()->limit(5),
            'latestMessage.sender',
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
            $chat->participants_info = $this->getParticipantsInfo($chat, $users, $drivers);
        }

        return view('Admin.chats.index', compact('chats', 'stats', 'users', 'drivers'));
    }

    /**
     * عرض تفاصيل محادثة معينة
     */
    public function show(Chat $chat)
    {
        // تحميل الرسائل مرة واحدة
        $chat->load([
            'messages' => fn($q) => $q->orderBy('created_at', 'desc'),
            'messages.sender',
        ]);

        /* ============================
       Participants (Optimized)
    ============================ */

        $participantIds = collect($chat->participants)->unique();

        $users = User::whereIn('id', $participantIds)->get()->keyBy('id');
        $drivers = Driver::whereIn('id', $participantIds)->get()->keyBy('id');

        $participantsInfo = collect($chat->participants)->map(function ($id) use ($users, $drivers) {

            if ($users->has($id)) {
                $u = $users[$id];
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'avatar' => $u->avatar,
                    'type' => 'user',
                    'is_online' => $u->last_seen && $u->last_seen->gt(now()->subMinutes(5)),
                ];
            }

            if ($drivers->has($id)) {
                $d = $drivers[$id];
                return [
                    'id' => $d->id,
                    'name' => $d->name,
                    'email' => $d->email,
                    'avatar' => $d->avatar,
                    'type' => 'driver',
                    'is_online' => $d->last_seen && $d->last_seen->gt(now()->subMinutes(5)),
                ];
            }

            return null;
        })->filter()->values()->toArray();

        /* ============================
       Chat Stats (No Extra Queries)
    ============================ */

        $messages = $chat->messages;

        $chatStats = [
            'total_messages' => $messages->count(),
            'unread_messages' => $messages->where('is_read', false)->count(),
            'voice_messages' => $messages->where('message_type', 'voice')->count(),
            'image_messages' => $messages->where('message_type', 'image')->count(),
            'file_messages' => $messages->where('message_type', 'file')->count(),
            'first_message_date' => $messages->last()?->created_at,
            'last_message_date' => $messages->first()?->created_at,
        ];

        /* ============================
       Mark as Read
    ============================ */

        Message::where('chat_id', $chat->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
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
        /* ============================
       Recent Active Chats
    ============================ */

        $recentChats = Chat::with(['latestMessage.sender'])
            ->where('last_message_at', '>=', now()->subMinutes(30))
            ->orderBy('last_message_at', 'desc')
            ->limit(20)
            ->get();

        /* ============================
       Unread Counts (One Query)
    ============================ */

        $unreadCounts = Message::whereIn('chat_id', $recentChats->pluck('id'))
            ->where('is_read', false)
            ->selectRaw('chat_id, COUNT(*) as count')
            ->groupBy('chat_id')
            ->pluck('count', 'chat_id');

        /* ============================
       Participants (Optimized)
    ============================ */

        $participantIds = $recentChats->pluck('participants')
            ->flatten()
            ->unique()
            ->values();

        $users = User::whereIn('id', $participantIds)->get()->keyBy('id');
        $drivers = Driver::whereIn('id', $participantIds)->get()->keyBy('id');

        foreach ($recentChats as $chat) {
            $chat->unread_count = $unreadCounts[$chat->id] ?? 0;

            $chat->participants_info = collect($chat->participants)->map(function ($id) use ($users, $drivers) {

                if ($users->has($id)) {
                    $u = $users[$id];
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'avatar' => $u->avatar,
                        'type' => 'user',
                        'is_online' => $u->last_seen && $u->last_seen->gt(now()->subMinutes(5)),
                    ];
                }

                if ($drivers->has($id)) {
                    $d = $drivers[$id];
                    return [
                        'id' => $d->id,
                        'name' => $d->name,
                        'avatar' => $d->avatar,
                        'type' => 'driver',
                        'is_online' => $d->last_seen && $d->last_seen->gt(now()->subMinutes(5)),
                    ];
                }

                return null;
            })->filter()->values();
        }

        /* ============================
       Recent Messages
    ============================ */

        $recentMessages = Message::with(['chat', 'sender'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('Admin.chats.live', compact('recentChats', 'recentMessages'));
    }

    /**
     * الحصول على معلومات المشاركين
     */
    private function getParticipantsInfo(Chat $chat, $users, $drivers)
    {
        return collect($chat->participants)->map(function ($id) use ($users, $drivers) {

            if ($users->has($id)) {
                $user = $users[$id];

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'type' => 'user',
                    'is_online' => $user->last_seen && $user->last_seen->diffInMinutes(now()) < 5,
                ];
            }

            if ($drivers->has($id)) {
                $driver = $drivers[$id];

                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'email' => $driver->email,
                    'avatar' => $driver->avatar,
                    'type' => 'driver',
                    'is_online' => $driver->last_seen && $driver->last_seen->diffInMinutes(now()) < 5,
                ];
            }

            return null;
        })->filter()->values()->toArray();
    }
    /**
     * إرسال رسالة كمشرف
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'message_type' => 'nullable|in:text,voice,image,file'
        ]);

        try {
            $message = $chat->messages()->create([
                'sender_id' => auth()->id(),
                'sender_type' => 'admin', // نوع خاص للمشرف
                'message' => $request->message,
                'message_type' => $request->message_type ?? 'text',
                'is_read' => false
            ]);

            // تحديث آخر رسالة في المحادثة
            $chat->update([
                'last_message' => Str::limit($request->message, 50),
                'last_message_at' => now()
            ]);

            // بث الحدث
            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => $message->load('sender')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة'
            ], 500);
        }
    }

    /**
     * تصدير المحادثات
     */
    public function export(Request $request)
    {
        // هنا يمكنك إضافة كود لتصدير المحادثات بصيغة CSV أو Excel
        // حسب احتياجاتك
    }

    /**
     * عرض صفحة إنشاء محادثة جديدة
     */
    public function create()
    {
        // جلب جميع المستخدمين والسائقين
        $users = User::select('id', 'name', 'phone_number', 'avatar')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $drivers = Driver::select('id', 'full_name', 'phone_number')
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('Admin.chats.create', compact('users', 'drivers'));
    }

    /**
     * إنشاء محادثة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|string',
            'participant_type' => 'required',
            'initial_message' => 'nullable|string|max:1000'
        ]);

        try {
            $currentUser = auth()->user();
            $participantId = $request->participant_id;

            // تحديد نوع المحادثة
            $type = $request->participant_type == 'user' ? 'admin_user' : 'admin_driver';

            // إنشاء مصفوفة المشاركين
            $participants = [
                (string)$currentUser->id,
                $participantId
            ];
            sort($participants);

            // التحقق إذا كانت المحادثة موجودة مسبقاً
            $chat = Chat::where('type', $type)
                ->whereJsonContains('participants', $participants[0])
                ->whereJsonContains('participants', $participants[1])
                ->first();

            if (!$chat) {
                $chat = Chat::create([
                    'chat_uuid' => Str::uuid(),
                    'type' => $type,
                    'participants' => $participants,
                    'last_message' => $request->initial_message ? Str::limit($request->initial_message, 50) : 'بداية المحادثة',
                    'last_message_at' => now()
                ]);
            }

            // إذا كان هناك رسالة أولية، إرسالها
            if ($request->filled('initial_message')) {
                $message = $chat->messages()->create([
                    'sender_id' => $currentUser->id,
                    'sender_type' => 'admin', // نوع خاص للمشرف
                    'message' => $request->initial_message,
                    'message_type' => 'text',
                    'is_read' => false
                ]);

                // تحديث آخر رسالة في المحادثة
                $chat->update([
                    'last_message' => Str::limit($request->initial_message, 50),
                    'last_message_at' => now()
                ]);

                // بث الحدث
                broadcast(new MessageSent($message))->toOthers();
            }

            return redirect()->route('admin.chats.show', $chat->id)
                ->with('success', 'تم إنشاء المحادثة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إنشاء المحادثة: ' . $e->getMessage());
        }
    }

    /**
     * إرسال رسالة مباشرة من الإدارة
     */
    public function sendAdminMessage(Request $request, Chat $chat)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'message_type' => 'nullable|in:text,voice,image,file'
        ]);

        try {
            $message = $chat->messages()->create([
                'sender_id' => auth()->id(),
                'sender_type' => 'admin',
                'message' => $request->message,
                'message_type' => $request->message_type ?? 'text',
                'is_read' => false
            ]);

            // تحديث آخر رسالة في المحادثة
            $chat->update([
                'last_message' => Str::limit($request->message, 50),
                'last_message_at' => now()
            ]);

            // بث الحدث
            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إرسال الرسالة'
            ], 500);
        }
    }

    /**
     * جلب المشارك بناءً على النوع
     */
    public function getParticipantInfo(Request $request)
    {
        $request->validate([
            'type' => 'required|in:user,driver',
            'id' => 'required'
        ]);

        try {
            if ($request->type == 'user') {
                $participant = User::findOrFail($request->id);
            } else {
                $participant = Driver::findOrFail($request->id);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $participant->id,
                    'name' => $participant->name,
                    // 'email' => $participant->email,
                    'phone' => $participant->phone_number,
                    // 'avatar' => $participant->avatar,
                    // 'is_online' => $participant->is_online ?? false,
                    'last_seen' => $participant->last_seen ? $participant->last_seen->diffForHumans() : 'غير متصل'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على المشارك'
            ], 404);
        }
    }

    /**
     * البحث عن مستخدمين أو سائقين
     */
    public function searchParticipants(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|min:2',
            'type' => 'nullable|in:all,user,driver'
        ]);

        $search = $request->search;
        $type = $request->type ?? 'all';

        $results = [];

        if ($type == 'all' || $type == 'user') {
            $users = User::where('status', 'active')
                ->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        // ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        // 'email' => $user->email,
                        'phone' => $user->phone_number,
                        // 'avatar' => $user->avatar,
                        'type' => 'user',
                        'type_label' => 'مستخدم'
                    ];
                });

            $results = array_merge($results, $users->toArray());
        }

        if ($type == 'all' || $type == 'driver') {
            $drivers = Driver::where('status', 'active')
                ->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        // ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get()
                ->map(function ($driver) {
                    return [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        // 'email' => $driver->email,
                        'phone_number' => $driver->phone_number,
                        // 'avatar' => $driver->avatar,
                        'type' => 'driver',
                        'type_label' => 'سائق',
                        // 'is_online' => $driver->is_online
                    ];
                });

            $results = array_merge($results, $drivers->toArray());
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * جلب قائمة المحادثات المباشرة (Admin Chats)
     */
    public function adminChats(Request $request)
    {

        $currentUser = auth()->user();

        // جلب المحادثات التي يكون المشرف طرفاً فيها
        $query = Chat::with(['latestMessage.sender'])
            // ->whereJsonContains('participants', (string)$currentUser->id)
            // ->where(function ($q) {
            //     $q->where('type', 'admin_user')
            //         ->orWhere('type', 'admin_driver');
            // })
        ;

        // فلترة حسب النوع
        if ($request->filled('chat_type')) {
            $query->where('type', $request->chat_type);
        }

        // فلترة حسب الرسائل غير المقروءة
        if ($request->has('unread_only')) {
            $query->whereHas('messages', function ($q) {
                $q->where('is_read', false);
            });
        }

        // فلترة حسب البحث
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

        // الترتيب من الأحدث
        $chats = $query->orderBy('last_message_at', 'desc')
            ->paginate(20);

        // حساب الرسائل غير المقروءة لكل محادثة
        foreach ($chats as $chat) {
            $chat->unread_count = $chat->messages()
                ->where('is_read', false)
                ->count();

            // جلب معلومات المشارك الآخر
            $chat->other_participant = $this->getOtherParticipantInfo($chat, $currentUser->id);
        }

        return view('Admin.chats.admin_chats', compact('chats'));
    }

    /**
     * جلب معلومات المشارك الآخر
     */
    private function getOtherParticipantInfo(Chat $chat, $currentUserId)
    {
        $participants = collect($chat->participants)
            ->filter(fn($id) => $id != $currentUserId);

        $otherId = $participants->first();

        if (!$otherId) {
            return null;
        }

        // محاولة العثور على مستخدم
        $user = User::find($otherId);
        if ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'type' => 'user',
                'is_online' => $user->last_seen && $user->last_seen->diffInMinutes(now()) < 5
            ];
        }

        // محاولة العثور على سائق
        $driver = Driver::find($otherId);
        if ($driver) {
            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'email' => $driver->email,
                'avatar' => $driver->avatar,
                'type' => 'driver',
                'is_online' => $driver->is_online
            ];
        }

        return null;
    }

    /**
     * تحديث حالة القراءة
     */
    public function markAsRead(Chat $chat)
    {
        try {
            $chat->messages()
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديد جميع الرسائل كمقروءة'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة القراءة'
            ], 500);
        }
    }
}
