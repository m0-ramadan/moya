@extends('Admin.layout.master')

@section('title','package ')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Create package </h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.package.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  <div class="row">
        <div class="col-12">
        <div class="row g-3  mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">name</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="name" id="exampleInputPassword2" type="text" placeholder="name" value="{{old('name',$user->name ?? '')}}">
                      @error('name')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">phone</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="phone" id="exampleInputPassword2" type="number" placeholder="phone" value="{{old('phone',$user->phone ?? '')}}">
                      @error('phone')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
                  <div class="row g-3 mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">phone2</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="phone2" id="exampleInputPassword2" type="number" placeholder="phone2" value="{{old('phone2',$user->phone2 ?? '')}}">
                      @error('phone2')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
                  <div class="row g-3 mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">email</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="email" id="exampleInputPassword2" type="email" placeholder="email" value="{{old('email',$user->email ?? '')}}">
                      @error('email')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
                  <div class="row g-3  mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">address</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="address" id="exampleInputPassword2" type="text" placeholder="address" value="{{old('address' ,$user->address ?? '')}}">
                      @error('address')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>

                  </div>
                  <div class="row g-3  mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">id_number</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="id_number" id="exampleInputPassword2" type="text" placeholder="id_number" value="{{old('id_number' ,$user->id_number ?? '')}}">
                      @error('id_number')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
 </div>

      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">Add</button>
        <a class="btn btn-light" href="{{ URL::previous() }}">cancel </a>
      </div>
    </div>

  </form>
</div>
</div>
@endsection

@section('js')

<script src="{{asset('admin/assets/js/tooltip-init.js')}}"></script>
@endsection
