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



<div class="row g-3 mb-5">
              <div class="col-md-12">
                  <label class="mr-sm-2" for="validationCustom04" class="form-label" style="font-family: 'Cairo', sans-serif;">privacy policy</label>
                    <textarea class="form-control ckeditor @error('privacy_policy') is-invalid fparsley-error parsley-error @enderror" name="privacy_policy" id="exampleFormControlTextarea4" rows="3">{{$setting->privacy_policy}}</textarea>
                    @error('privacy_policy')
                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                      <p>{{ $message }}</p>
                    </span>
                    @enderror
                </div>
 

                <div class="col-md-12">
                  <label class="mr-sm-2" for="validationCustom04" class="form-label" style="font-family: 'Cairo', sans-serif;">terms and conditions</label>
                    <textarea class="form-control ckeditor @error('about_description') is-invalid fparsley-error parsley-error @enderror" name="terms_conditions" id="exampleFormControlTextarea4" rows="3">{{$setting->terms_conditions}}</textarea>
                    @error('terms_conditions')
                    <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                      <p>{{ $message }}</p>
                    </span>
                    @enderror
                  </div>
        </div>
      <div class="row g-3 mb-3 mt-5">
          <div class="col-md-12">
              <label class="mr-sm-2" for="validationCustom03" style="font-family: 'Cairo', sans-serif;"> about title</label>
              <input class="form-control @error('work_time') is-invalid fparsley-error parsley-error @enderror" name="about_title" id="exampleInputPassword2" type="text" placeholder="about title" value="{{$setting->about_title}}">
                      @error('about_title')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
            </div>
            <div class="col-md-12">
                              <label class="mr-sm-2" for="validationCustom04" class="form-label" style="font-family: 'Cairo', sans-serif;">about description</label>
                                <textarea class="form-control ckeditor @error('about_description') is-invalid fparsley-error parsley-error @enderror" name="about_description" id="exampleFormControlTextarea4" rows="3">{{$setting->about_description}}</textarea>
                                @error('about_description')
                                <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                                  <p>{{ $message }}</p>
                                </span>
                                @enderror
            </div>
        </div>
        


        <div class="row g-3 mb-3 mt-5">
          <div class="col-md-12">
              <label class="mr-sm-2" for="validationCustom03" style="font-family: 'Cairo', sans-serif;">عن اويا خاص بالتطبيق</label>
              <input class="form-control @error('work_time') is-invalid fparsley-error parsley-error @enderror" name="about_apptitle" id="exampleInputPassword2" type="text" placeholder="عن اويا فى التطبيق" value="{{$info_app->title}}">
                      @error('about_title')
                      <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                        <p>{{ $message }}</p>
                      </span>
                      @enderror
            </div>
            <div class="col-md-12">
                              <label class="mr-sm-2" for="validationCustom04" class="form-label" style="font-family: 'Cairo', sans-serif;">تفاصيل عن اويا التطبيق</label>
                                <textarea class="form-control ckeditor @error('about_description') is-invalid fparsley-error parsley-error @enderror" name="about_appdesc" id="exampleFormControlTextarea4" rows="3">{{$info_app->desc}}</textarea>
                                @error('about_description')
                                <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
                                  <p>{{ $message }}</p>
                                </span>
                                @enderror
            </div>
        </div>


    <div class="row g-3 mb-3">
  <div class="col-md-8">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">About image</label>
    <input class="form-control @error('logo') is-invalid fparsley-error parsley-error @enderror" name="about_image"  type="file" placeholder="home_image" value="{{$setting->about_image  ??  ''}}">
            @error('home_image')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>
  <div class="col-md-4">
  <img style="width:100px;" src="{{asset($setting->about_image)}}" />
  </div>


</div>



<div class="row g-3 mb-3">
  <div class="col-md-8">
    <label class="mr-sm-2" for="validationCustom02" style="font-family: 'Cairo', sans-serif;">About app image</label>
    <input class="form-control @error('logo') is-invalid fparsley-error parsley-error @enderror" name="about_appimage"  type="file" placeholder="home_image" value="{{$setting->about_image  ??  ''}}">
            @error('home_image')
            <span class="invalid-feedback text-black font-weight-bold text-capitalize mt-2" role="alert">
              <p>{{ $message }}</p>
            </span>
            @enderror
  </div>
  <div class="col-md-4">
  <img style="width:100px;" src="{{asset($info_app->image)}}" />
  </div>


</div>



</div>


<div class="card-footer text-end">
  <button class="btn btn-primary" type="submit">حفظ الأعدادات </button>
  <a class="btn btn-light" href="{{ URL::previous() }}">إلغاء </a>
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
