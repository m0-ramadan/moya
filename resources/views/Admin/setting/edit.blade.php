@extends('Admin.layout.master')

@section('title','setting')
@section('css')

@endsection

@section('content')

<div class="card">
  <div class="card-header pb-0">
    <h5>Setting</h5>
  </div>
    <div class="card-body">
     <div class="row">
        <div class="col-12">
  <form class="form theme-form" action="{{route('admin.setting.update')}}" method="post" enctype="multipart/form-data">
    @csrf
    

<div class="row g-3 mb-3">
  <div class="col-md-8">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">Logo</label>
    <input class="form-control @error('logo') is-invalid fparsley-error parsley-error @enderror" name="logo"  type="file" placeholder="logo" value="{{$setting->logo}}">
            @error('logo')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>
  <div class="col-md-4">
  <img style="width:100px;" src="{{asset($setting->logo)}}" />
  </div>


  </div>
<div class="row g- mb-3">
  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">Email</label>
    <input class="form-control @error('email') is-invalid fparsley-error parsley-error @enderror" name="email" id="exampleInputPassword2" type="email" placeholder="Email" value="{{$setting->email}}">
            @error('email')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom03" style="font-family: 'Cairo', sans-serif;"> phone</label>
    <input class="form-control @error('phone') is-invalid fparsley-error parsley-error @enderror" name="phone" id="exampleInputPassword2" type="text" placeholder="phone" value="{{$setting->phone}}">
            @error('phone')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>


 

  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">whatsapp</label>
    <input class="form-control @error('whatsapp') is-invalid fparsley-error parsley-error @enderror" name="whatsapp" id="exampleInputPassword2" type="whatsapp" placeholder="whatsapp" value="{{$setting->whatsapp}}">
            @error('whatsapp')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">Snapchat</label>
    <input class="form-control @error('whatsapp') is-invalid fparsley-error parsley-error @enderror" name="snapchat" id="exampleInputPassword2" type="text" placeholder="Snapchat" value="{{$setting->snapchat}}">
            @error('snapchat')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">Tiktok</label>
    <input class="form-control @error('tiktok') is-invalid fparsley-error parsley-error @enderror" name="tiktok" id="exampleInputPassword2" type="text" placeholder="Tiktok" value="{{$setting->tiktok}}">
            @error('tiktok')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>



  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">Instagram</label>
    <input class="form-control @error('instagram') is-invalid fparsley-error parsley-error @enderror" name="instagram" id="exampleInputPassword2" type="text" placeholder="Instagram" value="{{$setting->instagram}}">
            @error('instagram')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $instagram }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">facebook</label>
    <input class="form-control @error('facebook') is-invalid fparsley-error parsley-error @enderror" name="facebook" id="exampleInputPassword2" type="facebook" placeholder="facebook" value="{{$setting->facebook}}">
            @error('facebook')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-3 g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom03" style="font-family: 'Cairo', sans-serif;"> linkedin</label>
    <input class="form-control @error('linkedin') is-invalid fparsley-error parsley-error @enderror" name="linkedin" id="exampleInputPassword2" type="text" placeholder="linkedin" value="{{$setting->linkedin}}">
            @error('linkedin')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>


  <div class="col-md-3  g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">twitter</label>
    <input class="form-control @error('twitter') is-invalid fparsley-error parsley-error @enderror" name="twitter" id="exampleInputPassword2" type="twitter" placeholder="twitter" value="{{$setting->twitter}}">
            @error('twitter')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>
  <div class="col-md-3  g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">عمولة التجميع</label>
    <input class="form-control @error('twitter') is-invalid fparsley-error parsley-error @enderror" name="commission" id="exampleInputPassword2" type="text" placeholder="عمولة التجميع" value="{{$setting->commission}}">
            @error('commission')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  
  <div class="col-md-3  g-3 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">no of ways of payment</label>
    <input class="form-control @error('home_cost') is-invalid fparsley-error parsley-error @enderror" name="no_payment" id="exampleInputPassword2" type="number" placeholder="" value="{{$setting->no_payment}}">
            @error('no_payment')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="row g-3 mb-3">

  <div class="col-md-6">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">home cost</label>
    <input class="form-control @error('home_cost') is-invalid fparsley-error parsley-error @enderror" name="home_cost" id="exampleInputPassword2" type="text" placeholder="home cost" value="{{$setting->home_cost}}">
            @error('home_cost')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-6">
    <label class="mr-sm-2" for="validationCustom03" style="font-family: 'Cairo', sans-serif;"> home work</label>
    <input class="form-control @error('work_time') is-invalid fparsley-error parsley-error @enderror" name="home_work" id="exampleInputPassword2" type="text" placeholder="worktime" value="{{$setting->home_work}}">
            @error('home_work')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>


  <div class="row g-3 mb-3">
  <div class="col-md-6">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">home speed</label>
    <input class="form-control @error('home_speed') is-invalid fparsley-error parsley-error @enderror" name="home_speed" id="exampleInputPassword2" type="home_speed" placeholder="home_speed" value="{{$setting->home_speed}}">
            @error('home_speed')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-6">
    <label class="mr-sm-2" for="validationCustom03" style="font-family: 'Cairo', sans-serif;"> home pay</label>
    <input class="form-control @error('pay_time') is-invalid fparsley-error parsley-error @enderror" name="home_pay" id="exampleInputPassword2" type="text" placeholder="home pay" value="{{$setting->home_pay}}">
            @error('home_pay')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

</div>



<div class="row mb-3 ">
  <div class="col-md-8 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;"> Calcluation shipment description</label>
    <input class="form-control @error('home_title') is-invalid fparsley-error parsley-error @enderror" name="calc_description" id="exampleInputPassword2" type="text" placeholder="Calcluation shipment description" value="{{$setting->calc_description}}">
            @error('calc_description')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>
  <div class="col-md-8 mb-3">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;"> track description</label>
    <input class="form-control @error('home_title') is-invalid fparsley-error parsley-error @enderror" name="track_description" id="exampleInputPassword2" type="text" placeholder="track description" value="{{$setting->track_description}}">
            @error('track_description')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

  <div class="col-md-12 mb-3">
    <label class="mr-sm-2" for="validationCustom03" style="font-family: 'Cairo', sans-serif;"> support description</label>
    <textarea class="form-control @error('descrption_time') is-invalid fparsley-error parsley-error @enderror" name="support_description" id="exampleInputPassword2" type="text" placeholder="support descrption" >{{$setting->support_description}}</textarea>
            @error('support_description')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>

</div>





<div class="row g-3 mb-5 mt-5">



<div class="row g-3 mb-5 mt-5">
    <div class="col-md-12">
        <label class="mr-sm-2" for="validationCustom04" class="form-label" style="font-family: 'Cairo', sans-serif;">meta description</label>
        <textarea class="form-control  @error('meta_description') is-invalid fparsley-error parsley-error @enderror"
            name="meta_description" id="exampleFormControlTextarea4" rows="3">{{ $setting->meta_description }}</textarea>
        @error('meta_description')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                <p>{{ $message }}</p>
            </span>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="mr-sm-2" for="validationCustom04" class="form-label" style="font-family: 'Cairo', sans-serif;">meta keyword</label>
        <textarea class="form-control  @error('meta_keyword') is-invalid fparsley-error parsley-error @enderror"
            name="meta_keyword" id="exampleFormControlTextarea4" rows="3">{{ $setting->meta_keyword }}</textarea>
        @error('meta_keyword')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                <p>{{ $message }}</p>
            </span>
        @enderror
    </div>

</div>
 
</div>


<div class="card-footer text-end">
  <button class="btn btn-primary" type="submit">update</button>
  <a class="btn btn-light" href="{{ URL::previous() }}">cancel </a>
</div>

</form>
        </div>
     </div>
    </div>
</div>
</div>
@endsection

@section('js')
<script src="{{asset('admin/assets/js/tooltip-init.js')}}"></script>
<script src="{{ asset('assets/js/editor/ckeditor/ckeditor.js')}}"></script>
    <script src="{{ asset('assets/js/editor/ckeditor/adapters/jquery.js')}}"></script>
    <script src="{{ asset('assets/js/editor/ckeditor/styles.js')}}"></script>
    <script src="{{ asset('assets/js/editor/ckeditor/ckeditor.custom.js')}}"></script>
@endsection
