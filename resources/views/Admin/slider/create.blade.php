@extends('Admin.layout.master')

@section('title','slider')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Create slider </h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.slider.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  <div class="row">
        <div class="col-12">

                  <div class="row g-3 mb-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">image</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="image" id="exampleInputPassword2" type="file" placeholder="image" value="{{old('image')}}">
                      @error('image')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>
                    
                    


                  </div>
                  
                  <div class="row">
                        <div class="col">
                          <div class="mb-3">
                            <label class="form-label" for="exampleFormControlSelect9">Example select</label>
                            <select class="form-select digits" id="exampleFormControlSelect9" name="type">
                              <option selected>-- show in --</option>
                              <option value="0">Desktop</option>
                              <option value="1">Mobile</option>
                             
                            </select>
                          
                               @error('type')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                      </div>
                        </div>
                      </div>
                      
                       <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="item_order" style="font-family: 'Cairo', sans-serif;">item order</label>
                      <input class="form-control @error('item_order') is-invalid fparsley-error parsley-error @enderror" name="item_order"  id="item_order" type="number" placeholder="Item Order" value="{{old('item_order') }}">
                      @error('item_order')
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
