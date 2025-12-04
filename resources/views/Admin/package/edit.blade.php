@extends('Admin.layout.master')

@section('title','package ')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Edit package </h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.package.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method ('PUT')
  <div class="row">
        <div class="col-12">
  <input type="hidden" name="id" value="{{$package->id}}">
  <div class="row g-3  mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">name</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="name" id="exampleInputPassword2" type="text" placeholder="name" value="{{old('name',$package->name ?? '')}}">
                      @error('name')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">price</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="price" id="exampleInputPassword2" type="number" placeholder="price" value="{{old('price',$package->price ?? '')}}">
                      @error('price')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
                  <div class="row g-3 mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">number</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="number" id="exampleInputPassword2" type="number" placeholder="number" value="{{old('number',$package->number ?? '')}}">
                      @error('number')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
                  <div class="row g-3  mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">weight</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="weight" id="exampleInputPassword2" type="text" placeholder="weight" value="{{old('weight' ,$package->weight ?? '')}}">
                      @error('weight')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>

                  <div class="row g-3  mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">description</label>
                      <textarea class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="description" id="exampleInputPassword2" type="text" placeholder="description" >{{old('description' ,$package->description ?? '')}} </textarea>
                      @error('description')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
                  
                                    <div class="row g-3  mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">active</label>
                      <input {{($package->status == 1 ? 'checked' : '')}} name="status" type="checkbox" class="form-check-input" id="exampleCheck1" >
                      @error('status')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
 </div>

      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit"> Update</button>
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
