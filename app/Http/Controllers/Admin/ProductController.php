<?php

namespace App\Http\Controllers\Admin;

use App\Models\Clients;
use App\Models\Product;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Traits\FirebaseNotificationTrait;
use Flasher\Toastr\Laravel\Facade\Toastr;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Branchs;

class ProductController extends Controller
{

    use ImageTrait, FirebaseNotificationTrait;

    public function index()
    {
        $products = Product::get();
        $clients = Clients::where('type', 2)->get();

        return view('Admin.product.index', compact('products', 'clients'));
    }

    public function show($id)
    {
        // $branches = Branchs::all();
        $client = Clients::find($id);
        return view('Admin.product.show', compact('client'));
    }

    public function create($id)
    {
        $branches = Branchs::all();
        $client = Clients::find($id);
        return view('Admin.product.create', compact('branches', 'client'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'sale_price' => 'required|numeric|min:0',
                'discounts' => 'nullable|numeric|min:0|max:100',
                'expected_delivery_time' => 'nullable|string|max:255',
                'person_id' => 'required|integer|exists:clients,id',
                'code' => 'required|string|max:255|unique:products,code',
                'category' => 'nullable|string|max:255',
                'brand' => 'nullable|string|max:255',
                'unit_type' => 'nullable|string|max:255',
                'in_stock_quantity' => 'required|integer|min:0',
                'reorder_limit' => 'nullable|integer|min:0',
                'minimum_stock' => 'nullable|integer|min:0',
                'location_in_stock' => 'nullable|string|max:255',
                'product_size' => 'nullable|string|max:255',
                'expiry_date' => 'nullable|date|after_or_equal:today',
                'description' => 'nullable|string',
                'details' => 'nullable|string|max:1000',
                'image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Handle image upload
            $image = "";
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $filename = time() . '.' . $request->file('image')->extension();
                $image = $this->uploadImg($request->file('image'), $filename, 'product');
                if (!$image) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'فشل في رفع الصورة الرئيسية');
                }
            }

            // Find the client
            $client = Clients::findOrFail($request->person_id);

            // Create the product
            $product = Product::create([
                'name' => $request->name,
                'price' => $request->sale_price, // Map sale_price to price
                'description' => $request->details,
                'product_details' => $request->details,
                'image' => $image,
                'code' => $request->code,
                'category' => $request->category,
                'brand' => $request->brand,
                'in_stock_quantity' => $request->in_stock_quantity,
                'reorder_limit' => $request->reorder_limit,
                'minimum_stock' => $request->minimum_stock,
                'location_in_stock' => $request->location_in_stock,
                'purchase_price' => $request->purchase_price ?? 0, // Default to 0 if not provided
                'sale_price' => $request->sale_price,
                'discounts' => $request->discounts,
                'unit_type' => $request->unit_type,
                'product_size' => $request->product_size,
                'person_type' => 'App\Models\Clients',
                'supplier_name' => $client->name,
                'status' => 'In Stock',
                'person_id' => $request->person_id,
                'expiry_date' => $request->expiry_date,
                'date_added_to_stock' => date('Y-m-d'),
                'expected_profit_margin' => $request->expected_profit_margin ?? 0,
                'supplier_contact_information' => $client->phone,
                'expected_delivery_time' => $request->expected_delivery_time,
                'branch_id' => $client->branch_id,
            ]);

            $notificationSent = false;
            if ($client) {
                $deviceTokens = $client->deviceToken()->pluck('device_token')->toArray();
                foreach ($deviceTokens as $deviceToken) {
                    $notificationRequest = new Request([
                        'device_token' => $deviceToken,
                        'title' => 'إضافة منتج جديد',
                        'body' => "تم إضافة منتج جديد بكود {$product->code} إلى المخزون الخاص بك.",
                        'icon' => '',
                    ]);

                    $response = $this->sendFirebaseNotification($notificationRequest);
                    if ($response && $response->getStatusCode() === 200) {
                        $notificationSent = true;
                    } else {
                        \Log::error('Failed to send product creation notification to client', [
                            'product_id' => $product->id,
                            'client_id' => $client->id,
                            'device_token' => $deviceToken,
                            'error' => $response ? ($response->getData()->error ?? 'Unknown error') : 'No response',
                        ]);
                    }
                }
            }

            DB::commit();

            Toastr::success('تم إضافة المنتج بنجاح' . ($notificationSent ? ' وتم إرسال الإشعار' : ''), 'نجاح');
            return redirect()->route('admin.product.show', [$client->id]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Toastr::error('العملاء غير موجود', 'خطأ');
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('حدث خطأ: ' . $e->getMessage(), 'خطأ');
            return redirect()->back()->withInput();
        }
    }
    public function edit($id)
    {
        $product = Product::find($id);
        $clients = Clients::where('type', 2)->get();
        $branches = Branchs::all();

        return view('Admin.product.edit', compact('product', 'clients', 'branches'));
    }

    public function update(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:products,id',
                'name' => 'required|string|max:255',
                'sale_price' => 'required|numeric|min:0',
                'discounts' => 'nullable|numeric|min:0|max:100',
                'expected_delivery_time' => 'nullable|string|max:255',
                'person_id' => 'required|integer|exists:clients,id',
                'code' => 'required|string|max:255|unique:products,code,' . $request->id,
                'category' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'unit_type' => 'nullable|string|max:255',
                'in_stock_quantity' => 'required|integer|min:0',
                'reorder_limit' => 'nullable|integer|min:0',
                'minimum_stock' => 'nullable|integer|min:0',
                'location_in_stock' => 'nullable|string|max:255',
                'product_size' => 'nullable|string|max:255',
                'expiry_date' => 'nullable|date|after_or_equal:today',
                'description' => 'nullable|string',
                'details' => 'nullable|string|max:1000',
                'image' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
                'purchase_price' => 'nullable|numeric|min:0',
                'expected_profit_margin' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            // Find the product and client
            $product = Product::findOrFail($request->id);
            $client = Clients::findOrFail($request->person_id);

            // Handle image upload
            $image = $product->image; // Preserve existing image if no new image
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image if it exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                $filename = time() . '.' . $request->file('image')->extension();
                $image = $this->uploadImg($request->file('image'), $filename, 'product');
                if (!$image) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'فشل في رفع الصورة الرئيسية');
                }
            }

            // Update the product
            $product->update([
                'name' => $request->name,
                'price' => $request->sale_price, // Map sale_price to price
                'description' => $request->description,
                'product_details' => $request->details,
                'image' => $image,
                'code' => $request->code,
                'category' => $request->category,
                'brand' => $request->brand,
                'in_stock_quantity' => $request->in_stock_quantity,
                'reorder_limit' => $request->reorder_limit,
                'minimum_stock' => $request->minimum_stock,
                'location_in_stock' => $request->location_in_stock,
                'purchase_price' => $request->purchase_price ?? 0,
                'sale_price' => $request->sale_price,
                'discounts' => $request->discounts,
                'unit_type' => $request->unit_type,
                'product_size' => $request->product_size,
                'person_type' => 'App\Models\Clients',
                'supplier_name' => $client->name,
                'status' => 'In Stock',
                'person_id' => $request->person_id,
                'expiry_date' => $request->expiry_date,
                'date_added_to_stock' => date('Y-m-d'),
                'expected_profit_margin' => $request->expected_profit_margin ?? 0,
                'supplier_contact_information' => $client->phone,
                'branch_id' => $request->branch_id ?? $product->branch_id,

            ]);

            // Send notification to the client
            $notificationSent = false;
            if ($client) {
                $deviceTokens = $client->deviceToken()->pluck('device_token')->toArray();
                foreach ($deviceTokens as $deviceToken) {
                    $notificationRequest = new Request([
                        'device_token' => $deviceToken,
                        'title' => 'تحديث منتج',
                        'body' => "تم تحديث المنتج بكود {$product->code} في المخزون الخاص بك.",
                        'icon' => '',
                    ]);

                    $response = $this->sendFirebaseNotification($notificationRequest);
                    if ($response && $response->getStatusCode() === 200) {
                        $notificationSent = true;
                    } else {
                        \Log::error('Failed to send product update notification to client', [
                            'product_id' => $product->id,
                            'client_id' => $client->id,
                            'device_token' => $deviceToken,
                            'error' => $response ? ($response->getData()->error ?? 'Unknown error') : 'No response',
                        ]);
                    }
                }
            }

            DB::commit();

            Toastr::success('تم تحديث المنتج بنجاح' . ($notificationSent ? ' وتم إرسال الإشعار' : ''), 'نجاح');
            return redirect()->route('admin.product.index');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Toastr::error('المنتج أو العميل غير موجود', 'خطأ');
            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('حدث خطأ: ' . $e->getMessage(), 'خطأ');
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            Toastr::success('success', 'sucessfully updated');
            return redirect()->route('admin.product.index');
        }
        Toastr::error('error', 'not found');
        return redirect()->route('admin.product.index');
    }
}
