@extends('Admin.layout.master')

@section('title','country ')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Create country </h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.country.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  <div class="row">
        <div class="col-12">

      <div class="row g-3  mb-3">
                    <div class="col-md-6">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الدولة</label>
                      <input class="form-control @error('country_ar') is-invalid fparsley-error parsley-error @enderror" name="country_ar" id="exampleInputPassword2" type="text" placeholder="الدولة" value="{{old('country_ar')}}">
                      @error('country')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>
                    <div class="col-md-6">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">   الرسالة  </label>
                      <input class="form-control @error('message') is-invalid fparsley-error parsley-error @enderror" name="message" id="exampleInputPassword2" type="text" placeholder="الرسالة" value="{{old('message')}}">
                      @error('country_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    <div class="col-md-6">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">عمولة التجميع</label>
                      <input class="form-control @error('message') is-invalid fparsley-error parsley-error @enderror" name="collection_commission" id="exampleInputPassword2" type="text" placeholder="عمولة التجميع" value="{{old('collection_commission')}}">
                      @error('collection_commission')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    

                  </div>
                  </div>


 </div>

      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">حفظ المطلوب</button>
        <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء </a>
      </div>
    </div>

  </form>
</div>
</div>
@endsection

@section('js')

<script src="{{asset('admin/assets/js/tooltip-init.js')}}"></script>
@endsection
