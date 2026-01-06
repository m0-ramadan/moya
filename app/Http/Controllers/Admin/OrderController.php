<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product'])->latest();

        // البحث
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // فلترة حسب طريقة الدفع
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // فلترة حسب المبلغ
        if ($request->filled('amount_from')) {
            $query->where('total_amount', '>=', $request->get('amount_from'));
        }

        if ($request->filled('amount_to')) {
            $query->where('total_amount', '<=', $request->get('amount_to'));
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $orders = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
        ];

        return view('Admin.orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        $users = User::latest()->get();
        $products = Product::where('stock', '>', 0)->where('status_id', 1)->latest()->get();

        return view('Admin.orders.create', compact('users', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|max:255',
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price_per_unit' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // إنشاء رقم الطلب
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::count() + 1, 6, '0', STR_PAD_LEFT);

            $validated['order_number'] = $orderNumber;
            $validated['address_id'] = null; // يمكنك ربطها بعنوان المستخدم لاحقاً

            // إنشاء الطلب
            $order = Order::create($validated);

            // إضافة العناصر
            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_per_unit' => $item['price_per_unit'],
                    'total_price' => $item['quantity'] * $item['price_per_unit'],
                    // يمكنك إضافة المزيد من الحقول حسب الحاجة
                ]);

                // تحديث المخزون
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage());
        }
    }

    public function show($order)
    {
        $order = Order::find($order);
        $order->load(['user', 'items.product', 'items.color', 'items.size', 'items.printingMethod']);

        return view('Admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items.product']);
        $users = User::latest()->get();
        $products = Product::where('stock', '>', 0)->where('status_id', 1)->latest()->get();

        return view('Admin.orders.edit', compact('order', 'users', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|max:255',
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'shipping_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // إذا تغيرت الحالة إلى "تم الشحن"
        if ($validated['status'] == 'shipped' && $order->status != 'shipped') {
            $validated['shipped_at'] = now();
        }

        // إذا تغيرت الحالة إلى "تم التسليم"
        if ($validated['status'] == 'delivered' && $order->status != 'delivered') {
            $validated['delivered_at'] = now();
        }

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'تم تحديث الطلب بنجاح.');
    }

    public function destroy(Order $order)
    {
        // إعادة المخزون في حالة الحذف
        if (in_array($order->status, ['pending', 'processing'])) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'تم حذف الطلب بنجاح.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // تحديث الحالة
        $order->status = $newStatus;

        // تحديث التواريخ
        if ($newStatus == 'shipped' && $oldStatus != 'shipped') {
            $order->shipped_at = now();
        }

        if ($newStatus == 'delivered' && $oldStatus != 'delivered') {
            $order->delivered_at = now();
        }

        // إذا تم إلغاء الطلب، إعادة المخزون
        if ($newStatus == 'cancelled' && !in_array($oldStatus, ['cancelled', 'delivered'])) {
            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        $order->save();

        // إضافة ملاحظة إذا وجدت
        if ($request->filled('notes')) {
            // يمكنك إضافة سجل للملاحظات هنا
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'status' => $order->status,
            'status_label' => $order->status_label,
        ]);
    }

    public function print(Order $order)
    {
        $order->load(['user', 'items.product', 'items.color', 'items.size']);

        return view('Admin.orders.print', compact('order'));
    }

    public function export(Request $request)
    {
        // يمكنك إضافة تصدير الطلبات بصيغة Excel أو PDF هنا
        return response()->json(['message' => 'سيتم إضافة التصدير لاحقاً']);
    }

    public function statistics()
    {


        // إحصائيات متقدمة
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total_amount'),
            'average_order_value' => Order::where('status', '!=', 'cancelled')->avg('total_amount') ?? 0,

            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount'),

            'weekly_orders' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'weekly_revenue' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->where('status', '!=', 'cancelled')->sum('total_amount'),

            'monthly_orders' => Order::whereMonth('created_at', now()->month)->count(),
            'monthly_revenue' => Order::whereMonth('created_at', now()->month)
                ->where('status', '!=', 'cancelled')->sum('total_amount'),

            'status_counts' => Order::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),

            'top_products' => DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
                ->groupBy('order_items.product_id', 'products.name')
                ->orderBy('total_quantity', 'desc')
                ->limit(10)
                ->get(),
        ];
        return view('Admin.orders.statistics', compact('stats'));
    }
}
