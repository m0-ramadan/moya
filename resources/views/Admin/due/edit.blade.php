@extends('Admin.layout.master')

@section('title','User due')
@section('css')

@endsection

@section('content') 

<div class="card">
  <div class="card-header pb-0">
    <h5>Edit due</h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.due.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method ('PUT')
  <div class="row">
        <div class="col-12">
  <input type="hidden" name="id" value="{{$due->id}}">


                  <div class="row g-3 mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">referance number</label>
                      <input class="form-control @error('reference_number') is-invalid fparsley-error parsley-error @enderror" name="reference_number" id="exampleInputPassword2" type="number" placeholder="referance number" value="{{old('reference_number',$due->reference_number ?? '')}}">
                      @error('reference_number')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror

                        

                    </div>


                  </div>






                  <div class="row g-3">
  <div class="col-md-8">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">image</label>
    <input class="form-control @error('logo') is-invalid fparsley-error parsley-error @enderror" name="image"  type="file" placeholder="image" value="{{$due->image}}">
            @error('image')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>
  @if(!is_null($due->image))
  <div class="col-md-4">
  @if(!empty($due->image ))<img style="width:100px;" src="{{asset($due->image)}}" />@endif
  </div>
@endif

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
