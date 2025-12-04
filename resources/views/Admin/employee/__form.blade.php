@php
use App\Models\Transfer;
use App\Models\Branchs;
use App\Models\Region;
@endphp

@if (session('success'))
<div class="col-md-8 m-2">
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
</div>
@endif

<div class="col-md-3">
    <label class="mr-sm-2" for="name" style="font-family: 'Cairo', sans-serif;">الأسم</label>
    <input class="form-control @error('name') is-invalid fparsley-error parsley-error @enderror" name="name" id="name"
        type="text" placeholder="الأسم بالكامل" value="{{ old('name', isset($transfer) ? $transfer->name : '') }}">
    @error('name')
    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
        <p>{{ $message }}</p>
    </span>
    @enderror
</div>





<div class="col-md-3">
    <label class="mr-sm-2" for="password" style="font-family: 'Cairo', sans-serif;">كلمة السر{{ isset($transfer) ? '
        (Leave blank to keep unchanged)' : '' }}</label>
    <input class="form-control @error('password') is-invalid fparsley-error parsley-error @enderror" name="password"
        id="password" type="password" placeholder="كلمة السر" value="">
    @error('password')
    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
        <p>{{ $message }}</p>
    </span>
    @enderror
</div>

<div class="col-md-3">
    <label class="mr-sm-2" for="password_confirmation" style="font-family: 'Cairo', sans-serif;">تأكيد كلمة السر</label>
    <input class="form-control @error('password_confirmation') is-invalid fparsley-error parsley-error @enderror"
        name="password_confirmation" id="password_confirmation" type="password" placeholder="تأكيد كلمة السر"
        value="">
    @error('password_confirmation')
    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
        <p>{{ $message }}</p>
    </span>
    @enderror
</div>

<div class="col-md-3">
    <label class="mr-sm-2" for="code" style="font-family: 'Cairo', sans-serif;">كود الموظف</label>
    <input class="form-control @error('code') is-invalid fparsley-error parsley-error @enderror" name="code" id="code"
        type="text" placeholder="كود الموظف" value="{{ old('code', isset($transfer) ? $transfer->code : '') }}">
    @error('code')
    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
        <p>{{ $message }}</p>
    </span>
    @enderror
</div>

<div class="col-md-3">
    <label class="mr-sm-2" for="phone" style="font-family: 'Cairo', sans-serif;">رقم الهاتف</label>
    <input class="form-control @error('phone') is-invalid fparsley-error parsley-error @enderror" name="phone"
        id="phone" type="text" placeholder="رقم الهاتف" value="{{ old('phone', isset($transfer) ? $transfer->phone : '') }}">
    @error('phone')
    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
        <p>{{ $message }}</p>
    </span>
    @enderror
</div>


<div class="col-md-3">
    <label class="mr-sm-2" for="email" style="font-family: 'Cairo', sans-serif;">البريد الألكترونى</label>
    <input class="form-control @error('email') is-invalid fparsley-error parsley-error @enderror" name="email"
        id="email" type="email" placeholder="البريد الألكترونى"
        value="{{ old('email', isset($transfer) ? $transfer->email : '') }}">
    @error('email')
    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
        <p>{{ $message }}</p>
    </span>
    @enderror
</div>

<input type="hidden" name="type" value="2">








<div class="col-md-3">
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
                    


                <div class="col-md-3">
                    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">المدينة</label>
                        <div class="input-group mb-3 ">
                            <select class="form-select" name="regions_id" id="region-select">
                                    <option disabled selected value="">اختر المنطقة</option>
                            </select>
                         </div>
                </div>
                
                
                <div class="col-md-3">
    <label class="mr-sm-2" for="branch_id" style="font-family: 'Cairo', sans-serif;">الفروع</label>
    <select class="form-control @error('branch_id') is-invalid fparsley-error parsley-error @enderror" name="branch_id"
        id="branch_id">
        <option value="">حدد الفرع</option>
        @foreach ($branches as $branch)
        <option value="{{ $branch->id }}" @if (old('branch_id', isset($transfer) ? $transfer->branch_id : '') ==
            $branch->id) selected @endif>
            {{ $branch->name }}
        </option>
        @endforeach
    </select>
    @error('branch_id')
    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
        <p>{{ $message }}</p>
    </span>
    @enderror
</div>


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