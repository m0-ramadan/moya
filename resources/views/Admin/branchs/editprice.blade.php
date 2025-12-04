@extends('Admin.layout.master')
@section('title', 'أسعار شحن الفروع')

@section('css')
<style>
  .form-select,
  .form-control {
    font-family: 'Cairo', sans-serif;
  }

  .invalid-feedback {
    display: none;
  }

  .is-invalid~.invalid-feedback {
    display: block;
  }
</style>
@endsection

@section('content')
<div class="card">
  <div class="card-header pb-0">
    <h5>أسعار الفروع</h5>
  </div>
  <div class="card-body">
    <form class="form theme-form" action="{{ route('admin.branch.updateprice') }}" method="POST"
      enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="row">
        <input type="hidden" name="id" value="{{ $pricing->id }}">

        <div class="col-md-3">
          <label class="mr-sm-2" for="city_id_from" style="font-family: 'Cairo', sans-serif;">من مدينة</label>
          <div class="input-group mb-3">
            <select class="form-select @error('city_id_from') is-invalid @enderror" id="city_id_from"
              name="city_id_from" aria-label="اختار المدينة">
              <option value="" disabled {{ old('city_id_from', $pricing->city_id_from) ? '' : 'selected' }}>حدد من مدينة
              </option>
              @foreach($regions as $region)
              <option value="{{ $region->id }}" {{ old('city_id_from', $pricing->city_id_from) == $region->id ?
                'selected' : '' }}>
                {{ $region->region_ar ?? 'غير متوفر' }}
              </option>
              @endforeach
            </select>
            @error('city_id_from')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              {{ $message }}
            </span>
            @enderror

          </div>
        </div>

        <div class="col-md-3">
          <label class="mr-sm-2" for="city_id" style="font-family: 'Cairo', sans-serif;">إلى مدينة</label>
          <div class="input-group mb-3">
            <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id"
              aria-label="اختار المدينة">
              <option value="" disabled {{ old('city_id', $pricing->city_id) ? '' : 'selected' }}>حدد إلى المدينة
              </option>
              @foreach($regions as $region)
              <option value="{{ $region->id }}" {{ old('city_id', $pricing->city_id) == $region->id ? 'selected' : ''
                }}>
                {{ $region->region_ar ?? 'غير متوفر' }}
              </option>
              @endforeach
            </select>
            @error('city_id')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              {{ $message }}
            </span>
            @enderror

          </div>
        </div>

        <div class="col-md-3">
          <label class="mr-sm-2" for="price" style="font-family: 'Cairo', sans-serif;">السعر</label>
          <input class="form-control @error('price') is-invalid @enderror" name="price" id="price" type="text"
            placeholder="السعر" value="{{ old('price', $pricing->price) }}">
          @error('price')
          <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
          </span>
          @enderror
        </div>
        <div class="col-md-3">
          <label for="currency" style="font-family: 'Cairo', sans-serif;">العملة</label>
          <select class="form-select @error('currency') is-invalid @enderror" name="currency" id="currency"
            aria-label="اختر العملة">
            <option value="" disabled {{ old('currency', $pricing->currency) ? '' : 'selected' }}>من فضلك حدد عملة
              الخزينة</option>
            <option value="LYD" {{ old('currency', $pricing->currency) == 'LYD' ? 'selected' : '' }}>دينار ليبي (LYD)
            </option>
            <option value="EGP" {{ old('currency', $pricing->currency) == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)
            </option>
            <option value="USD" {{ old('currency', $pricing->currency) == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)
            </option>
          </select>
          @error('currency')
          <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
            {{ $message }}
          </span>
          @enderror
        </div>
      </div>

      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">حفظ الإعدادات</button>
        <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء</a>
      </div>
    </form>
  </div>
</div>
@endsection