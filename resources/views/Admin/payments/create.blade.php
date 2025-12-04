@extends('Admin.layout.master')

@section('title','طرق الدفع')
@section('css')

@endsection

@section('content')

<div class="card">

    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.payments.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  <div class="row">
        <div class="col-12">

      <div class="row g-3  mb-3">
                    <div class="col-md-12">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">المحتوى</label>
                      <input class="form-control @error('name') is-invalid fparsley-error parsley-error @enderror" name="name" id="exampleInputPassword2" type="text" placeholder="طرق الدفع" value="{{old('name')}}">
                      @error('country')
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
