<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
use App\Models\PaymentType;
use Flasher\Toastr\Laravel\Facade\Toastr;

class PaymentController extends Controller
{
   
    public function index(){
        $listcontactinfo= PaymentType::get();
       
        return view('Admin.payments.index',compact('listcontactinfo'));
       
    }


    public function create(){
         return view('Admin.payments.create');
    }

    public function store(Request $request){
       
        PaymentType::create([
            'name'=> $request->name,
       ]);
       
         toastr()->success('success','sucessfully added');
       return redirect()->route('admin.payments.index');
    }

    public function edit($id){
         $note=PaymentType::find($id);
         return view('Admin.payments.edit',compact('note'));
    }

    public function update(Request $request){
        $country= PaymentType::find($request->id);
        $country->update([
            'name'=> $request->name,
           ]);
           toastr()->success('success','sucessfully updated');
       return redirect()->route('admin.payments.index');


    }

    public function destroy($id){

        $country=PaymentType::find($id);
        if($country){
            $country->delete();
          Toastr::addSuccess('Deleted successfully');
            return redirect()->route('admin.payments.index');
        }
            toastr()->success('not found');
            return redirect()->route('admin.payments.index');
         
    }

}