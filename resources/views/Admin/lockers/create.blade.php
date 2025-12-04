@extends('Admin.layout.master')

@section('title','الخزينة')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>الأضافة</h5>
  </div>
    <div class="card-body">

  <form class="form theme-form" action="{{route('admin.lockers.store')}}" method="post" enctype="multipart/form-data">
    @csrf
  
  <div class="row">
        <div class="col-12">
   <div class="row g-3  mb-3">
        
                    <div class="col-md-3  ">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">اسم الخزينة</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="name" id="exampleInputPassword2" type="text" placeholder="اسم الخزينة" value="{{ old('name') }}">
                      @error('name')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>
                    

        
                    <div class="col-md-3">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الرصيد الأفتتاحى</label>
                      <input class="form-control @error('image') is-invalid fparsley-error parsley-error @enderror" name="balance" id="exampleInputPassword2" step="0.01"  type="number" placeholder="الرصيد  الافتتاحى" value="{{old('balance')}}">
                      @error('price')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
                    </div>

                   
 
                    <div class="col-md-3">
                        <label for="currency" style="font-family: 'Cairo', sans-serif;">العملة</label>
                        <select class="form-select" name="currency" id="currency" aria-label="اختر العملة">
                            <option disabled selected>من فضلك حدد عملة الخزينة</option>

                            <option value="1" >LYD</option>
                            <option value="2" >EGP</option>
                            <option value="3" >$ (USD)</option>
                            <option value="4" >TRY</option>
                        </select>
                </div>

                    <div class="col-md-3">
                      <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">الفروع</label>
                      <select class="form-select" aria-label="Default select example" id="country-select"  name="branch_id">
                                        <option >من فضلك حدد الفروع</option>
                                       @foreach($branchs as $branch)
                                        <option value="{{ $branch->id }}"  >
                                            {{ $branch->name }}
                                        </option>
                                         @endforeach
                                    </select>
                    </div>
   
                    

                  </div>
              

                  </div>
 </div>

 </div>

      <div class="card-footer text-end">
        <button class="btn btn-primary" type="submit">حفظ الأعدادت</button>
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
