<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\About_us;
use App\Http\Requests\Admin\Setting\SettingRequest;
use Flasher\Toastr\Prime\ToastrInterface;
use Flasher\Toastr\Laravel\Facade\Toastr;
use App\Traits\ImageTrait;
class SettingController extends Controller
{

 
  use ImageTrait ;
 

    public function edit(){
      $setting = Setting::first();
      $info_app = About_us::first();
      return view('Admin.setting.edit',compact('setting','info_app'));
    }
    public function pages(){
        $setting = Setting::first();
        $info_app = About_us::first();
        return view('Admin.setting.pages',compact('setting','info_app'));
      }

 
    public function update(Request  $request){
       

      $setting=Setting::first();
       
  $setting->update(
         [
          'phone'                       =>  $request->phone,
          'email'                       =>  $request->email,
          'facebook'                    =>  $request->facebook,
          'linkedin'                    =>  $request->linkedin,
          'whatsapp'                    =>  $request->whatsapp,
          'instagram'                   =>  $request->instagram,
          'snapchat'                    =>  $request->snapchat,
          'twitter'                     =>  $request->twitter,
          'home_cost'                   =>  $request->home_cost,
          'home_speed'                  =>  $request->home_speed,
          'home_pay'                    =>  $request->home_pay,
          'home_work'                   =>  $request->home_work,
          'meta_description'            =>  $request->meta_description,
          'meta_keyword'                =>  $request->meta_keyword,
          'commission'                  =>  $request->commission
      ]);

    toastr()->success('success','sucessfully updated');
        return redirect()->back();
    }




    public function updatepages(Request  $request){
       

        $setting=Setting::first();
        $appinfo=About_us::first();
        
        if ($request->hasFile('logo')) {
            $filename = time() . '.' . $request->logo->extension();
            $logoname =  $this->uploadImg($request->logo, $filename, 'setting');
            $logo=$logoname;
  
        }
   
 if ($request->hasFile('about_image')) {
          $filename = time() . '.' . $request->about_image->extension();
          $logoname =  $this->uploadImg($request->about_image, $filename, 'setting');
          $about_image=$logoname;
      }
  
      if ($request->hasFile('about_appimage')) {
          $filename = time() . '.' . $request->about_appimage->extension();
          $logoname =  $this->uploadImg($request->about_appimage, $filename, 'setting');
          $about_appimage=$logoname;
      }
  
          $setting->update(
           ['logo'=>  $logo   ?? $setting->logo,
            'privacy_policy'              => $request->privacy_policy,
            'terms_conditions'            => $request->terms_conditions,
            'about_title'                 => $request->about_title,
            'about_description'           => $request->about_description,
            'about_image'                 => $about_image ?? $setting->about_image,
  
        ]);
        $appinfo->update(
          ['image'=>  $about_appimage   ?? $appinfo->app,
           'title'                       =>  $request->about_apptitle,
           'desc'                       =>  $request->about_appdesc,
           
        ]);
        toastr()->success('success','sucessfully updated');
          return redirect()->back();
      }



}
