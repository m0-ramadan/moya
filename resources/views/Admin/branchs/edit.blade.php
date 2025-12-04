@extends('Admin.layout.master')

@section('title','الفروع')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Edit Branch </h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.branch.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method ('PUT')
    <div class="row">
    <input type="hidden" name="id" value="{{ $branch->id }}">

                 
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الاسم</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="name_ar" id="exampleInputPassword2" type="text" placeholder="الأسم" value="{{old('name_ar', $branch->name ?? '')}}">
                      @error('region_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    
                    

                    

                    <div class="col-md-4 ">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="phone1" id="exampleInputPassword2" type="text" placeholder="رقم الهاتف" value="{{old('phone1', $branch->phone1 ?? '')}}">

                    </div>


                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">رقم الهاتف البديل</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="phone2" id="exampleInputPassword2" type="text" placeholder="رقم الهاتف البديل" value="{{old('phone2', $branch->phone2 ?? '')}}">

                    </div>

                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">عنوان الفرع</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="address" id="exampleInputPassword2" type="text" placeholder="عنوان الفرع" value="{{old('address', $branch->address ?? '')}}">

                    </div>
                    
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">العنوان على الخريطة</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="map" id="exampleInputPassword2" type="text" placeholder="عنوان على الخريطة" value="{{old('map', $branch->link_address ?? '')}}">

                    </div>
                  
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">كود الفرع</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="code" id="exampleInputPassword2" type="text" placeholder="كود الفرع" value="{{old('key', $branch->key ?? '')}}">

                    </div>



                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">السعر</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="price" id="exampleInputPassword2" type="text" placeholder="السعر" value="{{old('price', $branch->price ?? '')}}">
                      @error('region_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الدول</label>
                           <div class="input-group mb-3 ">
                                    <select class="form-select" aria-label="Default select example" id="country-select"  name="country_id">
                                        <option selected>Country</option>
                                       @foreach($countries as $country)
                                       <option value="{{ $country->id }}" {{ $branch->country_id == $country->id ? 'selected' : '' }}>
                                            {{ $country->country_ar}}
                                        </option>
                                         @endforeach
                                    </select>




                                    
                             </div>
                    </div>

                <div class="col-md-4">
                    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">المدن</label>
                        <div class="input-group mb-3 ">
                            <select class="form-select" name="regions_id" id="region-select">
                                  <option disabled selected value="">اختر المنطقة</option> 
                            </select>
                         </div>
                </div>

                  </div>


      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit"> حفظ الاعدادت</button>
        <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء </a>
      </div>
    </div>

  </form>
</div>
</div>
@endsection

@section('js')
 

<script>
    $(document).ready(function () {
        const selectedCountry = '{{ $branch->country_id }}';
        const selectedRegion = '{{ $branch->region_id }}';

        function loadRegions(countryId, selectedRegionId = null) {
            if (countryId) {
                $.ajax({
                    url: '../getRegions/' + countryId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $('#region-select').empty();
                        $('#region-select').append('<option disabled selected value="">اختر المنطقة</option>');
                        $.each(data, function (index, region) {
                            let selected = (region.id == selectedRegionId) ? 'selected' : '';
                            $('#region-select').append('<option value="' + region.id + '" ' + selected + '>' + region.region_ar + '</option>');
                        });
                    }
                });
            }
        }

        // عند تغيير الدولة
        $('#country-select').on('change', function () {
            let countryId = $(this).val();
            loadRegions(countryId);
        });

        // تحميل المناطق تلقائيًا عند فتح الصفحة مع تحديد المنطقة المحفوظة
        if (selectedCountry) {
            loadRegions(selectedCountry, selectedRegion);
        }
    });
</script>

@endsection
