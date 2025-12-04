@extends('Admin.layout.master')

@section('title', 'مدن')
@section('css')

@endsection

@section('content')

    <div class="card">

        <div class="card-body">

            <form class="form theme-form" action="{{ route('admin.region.update') }}" method="post"
                enctype="multipart/form-data">
                @csrf
                @method ('PUT')
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" name="id" value="{{ $region->id }}">
                        <div class="row g-3  mb-3">




                            <div class="col-md-4">
                                <label class="mr-sm-2" for="validationCustom02"
                                    style="font-family: 'Cairo', sans-serif;">المدينة</label>
                                <input
                                    class="form-control @error('region_ar') is-invalid fparsley-error parsley-error @enderror"
                                    name="region_ar" id="exampleInputPassword2" type="text" placeholder="region in"
                                    value="{{ old('region_ar', $region->region_ar ?? '') }}">
                                @error('region_ar')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">
                                    الكود </label>
                                <input class="form-control @error('key') is-invalid fparsley-error parsley-error @enderror"
                                    name="key" type="text" placeholder="الكود"
                                    value="{{ old('key', $region->key ?? '') }}">
                                @error('key')
                                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2"
                                        role="alert">
                                        <p>{{ $message }}</p>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="mr-sm-2" for="validationCustom02"
                                    style="font-family: 'Cairo', sans-serif;">الدولة</label>
                                <div class="input-group mb-3 ">

                                    <select class="form-select" aria-label="Default select example" name="country">
                                        <option selected>Countries</option>
                                        @foreach ($countries as $country)
                                            <option @if (old('country') || $region->country_id == $country->id) selected @endif
                                                value="{{ $country->id }}">{{ $country->country_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>





                    </div>

                    <div class="card-footer text-end">
                        <button class="btn btn-primary" type="submit"> حفظ الأعدادت</button>
                        <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء </a>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('js')

    <script src="{{ asset('admin/assets/js/tooltip-init.js') }}"></script>
@endsection
