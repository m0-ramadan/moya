@extends('Admin.layout.master')

@section('title','عمولات التحويل')
@section('css')
@endsection

@section('content')

<div class="card">
    <div class="card-body">
  <form class="form theme-form" action="{{route('admin.currency.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method ('PUT')
    <div class="row">
    <input type="hidden" name="id" value="{{ $currency->id }}">

                 
        

                     

                    <div class="col-md-4 ">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">من عملة  </label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" readonly  value="{{ $currency->from_currency_name  }}">

                    </div>


                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الى عملة </label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror"  readonly value="{{ $currency->to_currency_name  }}">

                    </div>
 
                    <div class="col-md-4">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">عمولة التحويل</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="conversion_rate" id="exampleInputPassword2" type="text" placeholder="عمولة التحويل" value="{{old('conversion_rate', $currency->conversion_rate ?? '')}}">
                      @error('region_ar')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>
 

                  </div>


      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">حفظ الاعدادات</button>
        <a class="btn btn-light" href="{{ URL::previous() }}">إاغل </a>
      </div>
    </div>

  </form>
</div>
</div>
@endsection
