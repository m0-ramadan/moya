<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
use App\Models\Branchs;
use Flasher\Toastr\Laravel\Facade\Toastr;
use App\Models\ContactUs;

class ContactUsController extends Controller
{
   
    public function index(){
        $listcontactinfo= ContactUs::get();
        
        return view('Admin.contactus.index',compact('listcontactinfo'));
       
    }


    public function create(){
        $branchs = Branchs::all();         
        return view('Admin.contactus.create', compact('branchs'));
    }

    public function store(Request $request){
        ContactUs::create([
            'phone'                  => $request->phone,
             'email'                 =>$request->email,
            'address'                =>$request->address,
            'name'                   =>$request->branch_name,
       ]);
         toastr()->success('success','sucessfully added');
       return redirect()->route('admin.contactus.index');
    }

    public function edit($id){
        $branchs = Branchs::all();
        $contactus=ContactUs::find($id);
         return view('Admin.contactus.edit',compact('contactus','branchs'));
    }

    public function update(Request $request){
         

        $country= ContactUs::find($request->id);
        
        
        $country->update([
            'phone'                  => $request->phone,
             'email'                 =>$request->email,
            'address'                =>$request->address,
            'name'                   =>$request->branch_name,
           ]);
           toastr()->success('success','sucessfully updated');
       return redirect()->route('admin.contactus.index');


    }

    public function destroy($id){

        $country=ContactUs::find($id);
        if($country){
            $country->delete();
          Toastr::addSuccess('Deleted successfully');
            return redirect()->route('admin.contactus.index');
        }
            toastr()->success('not found');
            return redirect()->route('admin.contactus.index');
         
    }

}
