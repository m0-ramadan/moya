@extends('Admin.layout.master')

@section('title','بيانات التواصل')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Create country </h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.contactus.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  <div class="row">
        <div class="col-12">

      <div class="row g-3  mb-3">
                    <div class="col-md-3">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
                      <input class="form-control @error('phone') is-invalid fparsley-error parsley-error @enderror" name="phone" id="exampleInputPassword2" type="text" placeholder="رقم  الهاتف" value="{{old('phone')}}">
                      @error('country')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>
                    <div class="col-md-3">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;"> الايميل</label>
                      <input class="form-control @error('email') is-invalid fparsley-error parsley-error @enderror" name="email" id="exampleInputPassword2" type="text" placeholder="البريد الألكترونى" value="{{old('email')}}">
                      @error('country_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    <div class="col-md-3">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">العنوان</label>
                      <input class="form-control @error('address') is-invalid fparsley-error parsley-error @enderror" name="address" id="exampleInputPassword2" type="text" placeholder="   " value="{{old('address')}}">
                      @error('collection_commission')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    
                    <div class="col-md-3">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الفروع</label>
                      <select class="form-select" aria-label="Default select example" id="country-select"  name="branch_name">
                                        <option >من فضلك حدد الفروع</option>
                                       @foreach($branchs as $branch)
                                        <option value="{{ $branch->name }}"  >
                                            {{ $branch->name }}
                                        </option>
                                         @endforeach
                                    </select>
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
