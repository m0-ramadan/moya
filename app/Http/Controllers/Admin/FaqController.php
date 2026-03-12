<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faqs = Faq::orderBy('sort_order', 'asc')->get();
        return view('Admin.faqs.index', compact('faqs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'boolean'
        ]);

        // تعيين الترتيب التلقائي إذا لم يتم تحديده
        if (empty($validated['sort_order'])) {
            $maxOrder = Faq::max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        $validated['status'] = $request->has('status');

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم إضافة السؤال بنجاح');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'required|integer|min:0',
            'status' => 'boolean'
        ]);

        $validated['status'] = $request->has('status')?'active':'inactive';

        $faq->update($validated);

        return redirect()->route('admin.faqs.index')
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json(['success' => 'تم حذف السؤال بنجاح']);
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Faq $faq)
    {
        $newStatus = $faq->status === 'active' ? 'inactive' : 'active';
        $faq->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير حالة السؤال بنجاح',
            'status' => $faq->status,
            'is_active' => $faq->status === 'active'
        ]);
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $ids = $request->ids;
        
        foreach ($ids as $index => $id) {
            Faq::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
