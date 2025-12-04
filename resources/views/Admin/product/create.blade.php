@extends('Admin.layout.master')

@section('title', 'إضافة منتج')

@section('css')
@endsection

@section('content')
    <div class="card">
        <div class="card-header pb-0">
            <h5>إضافة منتج</h5>
        </div>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-body">
            <form class="form theme-form" action="{{ route('admin.product.store') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="mr-sm-2" for="name"
                                    style="font-family: 'Cairo', sans-serif;">المنتج</label>
                                <input class="form-control @error('name') is-invalid @enderror" name="name"
                                    id="name" type="text" placeholder="المنتج" value="{{ old('name') }}">
                                @error('name')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="sale_price" style="font-family: 'Cairo', sans-serif;">سعر
                                    البيع</label>
                                <input class="form-control @error('sale_price') is-invalid @enderror" name="sale_price"
                                    id="sale_price" step="0.01" type="number" placeholder="سعر البيع"
                                    value="{{ old('sale_price') }}">
                                @error('sale_price')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="discounts" style="font-family: 'Cairo', sans-serif;">نسبة
                                    الخصم</label>
                                <input class="form-control @error('discounts') is-invalid @enderror" name="discounts"
                                    id="discounts" type="number" placeholder="نسبة الخصم" value="{{ old('discounts') }}">
                                @error('discounts')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="expected_delivery_time"
                                    style="font-family: 'Cairo', sans-serif;">مدة التوصيل
                                    المتوقع</label>
                                <input class="form-control @error('expected_delivery_time') is-invalid @enderror"
                                    name="expected_delivery_time" id="expected_delivery_time" type="text"
                                    placeholder="مدة التوصيل المتوقع" value="{{ old('expected_delivery_time') }}">
                                @error('expected_delivery_time')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" style="font-family: 'Cairo', sans-serif;">التاجر</label>
                                <input type="text" class="form-control" value="{{ $client->name }}" readonly>
                                <input type="hidden" name="person_id" value="{{ $client->id }}">
                            </div>



                            {{-- <div class="col-md-3">
                                <label class="mr-sm-2" for="branch_id"
                                    style="font-family: 'Cairo', sans-serif;">الفروع</label>
                                <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                                    name="branch_id">
                                    <option value="" selected>من فضلك حدد الفرع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div> --}}

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="code" style="font-family: 'Cairo', sans-serif;">كود
                                    المنتج</label>
                                <input class="form-control @error('code') is-invalid @enderror" name="code"
                                    id="code" type="text" placeholder="كود المنتج" value="{{ old('code') }}">
                                @error('code')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="category"
                                    style="font-family: 'Cairo', sans-serif;">الفئة</label>
                                <input class="form-control @error('category') is-invalid @enderror" name="category"
                                    id="category" type="text" placeholder="الفئة" value="{{ old('category') }}">
                                @error('category')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="brand"
                                    style="font-family: 'Cairo', sans-serif;">البرند</label>
                                <input class="form-control @error('brand') is-invalid @enderror" name="brand"
                                    id="brand" type="text" placeholder="البرند" value="{{ old('brand') }}">
                                @error('brand')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="unit_type" style="font-family: 'Cairo', sans-serif;">وحدة
                                    القياس</label>
                                <input class="form-control @error('unit_type') is-invalid @enderror" name="unit_type"
                                    id="unit_type" type="text" placeholder="وحدة القياس"
                                    value="{{ old('unit_type') }}">
                                @error('unit_type')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="in_stock_quantity"
                                    style="font-family: 'Cairo', sans-serif;">الكمية في
                                    المخزن</label>
                                <input class="form-control @error('in_stock_quantity') is-invalid @enderror"
                                    name="in_stock_quantity" id="in_stock_quantity" type="number"
                                    placeholder="الكمية في المخزن" value="{{ old('in_stock_quantity') }}">
                                @error('in_stock_quantity')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="reorder_limit" style="font-family: 'Cairo', sans-serif;">أقصى
                                    حد للطلب</label>
                                <input class="form-control @error('reorder_limit') is-invalid @enderror"
                                    name="reorder_limit" id="reorder_limit" type="number" placeholder="أقصى حد للطلب"
                                    value="{{ old('reorder_limit') }}">
                                @error('reorder_limit')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="minimum_stock" style="font-family: 'Cairo', sans-serif;">أقل
                                    كمية في
                                    المخزن</label>
                                <input class="form-control @error('minimum_stock') is-invalid @enderror"
                                    name="minimum_stock" id="minimum_stock" type="number"
                                    placeholder="أقل كمية في المخزن" value="{{ old('minimum_stock') }}">
                                @error('minimum_stock')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="location_in_stock"
                                    style="font-family: 'Cairo', sans-serif;">الموقع في
                                    المخزن</label>
                                <input class="form-control @error('location_in_stock') is-invalid @enderror"
                                    name="location_in_stock" id="location_in_stock" type="text"
                                    placeholder="الموقع في المخزن" value="{{ old('location_in_stock') }}">
                                @error('location_in_stock')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="product_size" style="font-family: 'Cairo', sans-serif;">مقاس
                                    المنتج</label>
                                <input class="form-control @error('product_size') is-invalid @enderror"
                                    name="product_size" id="product_size" type="text" placeholder="مقاس المنتج"
                                    value="{{ old('product_size') }}">
                                @error('product_size')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="mr-sm-2" for="expiry_date" style="font-family: 'Cairo', sans-serif;">تاريخ
                                    الصلاحية</label>
                                <input class="form-control @error('expiry_date') is-invalid @enderror" name="expiry_date"
                                    id="expiry_date" type="date" placeholder="تاريخ الصلاحية"
                                    value="{{ old('expiry_date') }}">
                                @error('expiry_date')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-12">
                                <label class="mr-sm-2" for="details" style="font-family: 'Cairo', sans-serif;">تفاصيل
                                    المنتج</label>
                                <textarea name="details" id="details" class="form-control @error('details') is-invalid @enderror" rows="4">{{ old('details') }}</textarea>
                                @error('details')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-8 m-2">
                                <label class="mr-sm-2" for="image" style="font-family: 'Cairo', sans-serif;">صورة
                                    المنتج</label>
                                <input class="form-control @error('image') is-invalid @enderror" name="image"
                                    id="image" type="file" placeholder="صورة المنتج">
                                @error('image')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button class="btn btn-primary" type="submit">حفظ المطلوب</button>
                    <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection
