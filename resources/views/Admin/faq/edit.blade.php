@extends('Admin.layout.master')

@section('title','faq ')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Edit faq </h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.faq.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    @method ('PUT')
  <div class="row">
        <div class="col-12">

      <div class="row g-3  mb-3">
        <input type="hidden" name="id" value="{{$faq->id}}">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">question</label>
                      <textarea class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="question" id="exampleInputPassword2" type="text" placeholder="question" >{{old('question', $faq->question ?? '')}} </textarea>
                      @error('question')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>

                  <div class="row g-3">
                    <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">answer</label>
                      <textarea class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="answer" id="exampleInputPassword2" type="text" placeholder="answer" >{{old('answer',$faq->answer ?? '')}} </textarea>
                      @error('answer')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>


                  </div>
                  
                  <div class="col-md-8 m-2">
                      <label class="mr-sm-2" for="item_order" style="font-family: 'Cairo', sans-serif;">item order</label>
                      <input class="form-control @error('item_order') is-invalid fparsley-error parsley-error @enderror" name="item_order"  id="item_order" type="number" placeholder="Item Order" value="{{old('item_order') ?? $faq->item_order }}">
                      @error('item_order')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
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
