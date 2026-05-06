<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Flasher\Toastr\Laravel\Facade\Toastr;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        // جلب جميع الرسائل مع إمكانية الفلترة
        $query = ContactUs::query();

        // فلترة بالحالة
        if ($request->filled('status')) {
            if ($request->status == 'read') {
                $query->where('is_read', true);
            } elseif ($request->status == 'unread') {
                $query->where('is_read', false);
            }
        }

        // بحث في الاسم، البريد، الموضوع، والرسالة
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%");
            });
        }

        // فلترة بالتاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // الترتيب
        $sort = $request->sort ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $contacts = $query->paginate(15)->withQueryString();

        // حساب الإحصائيات
        $stats = [
            'total' => ContactUs::count(),
            'unread' => ContactUs::where('is_read', false)->count(),
            'read' => ContactUs::where('is_read', true)->count(),
            'today' => ContactUs::whereDate('created_at', today())->count(),
        ];

        return view('Admin.contact.index', compact('contacts', 'stats'));
    }

    public function show($id)
    {
        $contact = ContactUs::findOrFail($id);
        
        // تحديد الرسالة كمقروءة تلقائياً عند العرض
        if (!$contact->is_read) {
            $contact->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json($contact);
    }

    public function read($id)
    {
        $contact = ContactUs::find($id);
        
        if ($contact) {
            $contact->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
            
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديد الرسالة كمقروءة بنجاح'
                ]);
            }
            
            toastr()->success('تم تحديد الرسالة كمقروءة بنجاح');
        }

        return redirect()->route('admin.contact.index');
    }

    public function markAllRead()
    {
        ContactUs::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديد جميع الرسائل كمقروءة بنجاح'
            ]);
        }

        toastr()->success('تم تحديد جميع الرسائل كمقروءة بنجاح');
        return redirect()->route('admin.contact.index');
    }

    public function destroy($id)
    {
        $contact = ContactUs::find($id);

        if ($contact) {
            $contact->delete();

            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف الرسالة بنجاح'
                ]);
            }

            toastr()->success('تم حذف الرسالة بنجاح');
        } else {
            if (request()->ajax() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الرسالة غير موجودة'
                ], 404);
            }

            toastr()->error('الرسالة غير موجودة');
        }

        return redirect()->route('admin.contact.index');
    }
}