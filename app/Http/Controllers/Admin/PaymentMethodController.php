<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::latest()->paginate(10);
        return view('Admin.payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('Admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'key' => 'required|string|max:255|unique:payment_methods,key',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_payment' => 'boolean',
        ]);

        $validated['key'] = Str::slug($validated['key']);

        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'تم إضافة وسيلة الدفع بنجاح.');
    }

    public function show(PaymentMethod $paymentMethod)
    {
        return view('Admin.payment-methods.show', compact('paymentMethod'));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('Admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
            'key' => 'required|string|max:255|unique:payment_methods,key,' . $paymentMethod->id,
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_payment' => 'boolean',
        ]);

        $validated['key'] = Str::slug($validated['key']);

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'تم تحديث وسيلة الدفع بنجاح.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'تم حذف وسيلة الدفع بنجاح.');
    }

    public function toggleStatus(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);

        return redirect()->back()->with('success', 'تم تغيير الحالة بنجاح');
    }
}
