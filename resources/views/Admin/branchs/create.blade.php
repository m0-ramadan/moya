@extends('Admin.layout.master')

@section('title','الفروع')
@section('css')

@endsection

@section('content')

<div class="card">
 
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.branch.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  <div class="row">
 

                 
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">اسم الفرع</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="name_ar" id="exampleInputPassword2" type="text" placeholder="اسم الفرع" value="{{old('name_ar')}}">
                      @error('region_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    
                   

                    

                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="phone1" id="exampleInputPassword2" type="text" placeholder="رقم الهاتف " value="{{old('phone1')}}">

                    </div>


                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">رقم الهاتف البديل</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="phone2" id="exampleInputPassword2" type="text" placeholder="رقم الهاتف البديل" value="{{old('phone2')}}">

                    </div>

                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">العنوان بالتفاصيل</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="address" id="exampleInputPassword2" type="text" placeholder="العنوان بالتافصيل" value="{{old('address')}}">

                    </div>
                    
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">العنوان على الخريطة</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="map" id="exampleInputPassword2" type="text" placeholder="العنوان على الخريطة" value="{{old('map')}}">

                    </div>
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الكود الفرع</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="code" id="exampleInputPassword2" type="text" placeholder="كود الفرع" value="{{old('key')}}">

                    </div>

                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">السعر</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="price" id="exampleInputPassword2" type="text" placeholder="السعر" value="{{old('price')}}">
                      @error('region_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                    <div class="col-md-4">
                        <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الدولة</label>
                           <div class="input-group mb-3 ">
                                    <select class="form-select" aria-label="Default select example" id="country-select"  name="country_id">
                                        <option selected>الدولة</option>
                                       @foreach($countries as $country)
                                            <option    value="{{$country->id}}"  >{{$country->country_ar}}</option>
                                         @endforeach
                                    </select>
                             </div>
                    </div>

                <div class="col-md-4">
                    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">المدينة</label>
                        <div class="input-group mb-3 ">
                            <select class="form-select" name="regions_id" id="region-select">
                                    <option disabled selected value="">اختر المنطقة</option>
                            </select>
                         </div>
                </div>

            </div>



      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">حفظ الاعدادت</button>
        <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء </a>
      </div>
    </div>

  </form>
</div>
</div>
@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('#country-select').on('change', function () {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: 'getRegions/' + countryId,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#region-select').empty();
                        $('#region-select').append('<option disabled selected value="">اختر المنطقة</option>');
                        $.each(data, function (key, region) {
                            $('#region-select').append('<option value="' + region.id + '">' + region.region_ar + '</option>');
                        });
                    }
                });
            } else {
                $('#region-select').empty();
                $('#region-select').append('<option disabled selected value="">اختر المنطقة</option>');
            }
        });
    });
</script>
@endsection

