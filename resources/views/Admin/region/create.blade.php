@extends('Admin.layout.master')

@section('title','المدن')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>اضافة</h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.region.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  <div class="row">
        <div class="col-12">

      <div class="row  ">
                     


                 
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">  المدينة  </label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="region_ar" id="exampleInputPassword2" type="text" placeholder="المدينة" value="{{old('region_ar')}}">
                      @error('region_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    <div class="col-md-4">
                          <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">  الكود  </label>
                          <input class="form-control @error('key') is-invalid fparsley-error parsley-error @enderror" name="key"   type="text" placeholder="الكود" value="{{old('key')}}">
                          @error('key')
                          <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                            <p>{{ $message }}</p>
                          </span>
                          @enderror
                    </div>

 <div class="col-md-4">
                    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الدولة</label>
                        <div class="input-group mb-3 ">
            
                                    <select class="form-select" aria-label="Default select example"  name="country">
                                        <option selected value="">الدولة</option>
                                       @foreach($countries as $country)
                                            <option    value="{{$country->id}}"  >{{$country->country_ar}}</option>
                                         @endforeach
                                    </select>
                                </div>
                </div>



 </div>

      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">اضافة</button>
        <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء </a>
      </div>
    </div>

  </form>
</div>
</div></div>
@endsection

@section('js')

<script src="{{asset('admin/assets/js/tooltip-init.js')}}"></script>
@endsection
