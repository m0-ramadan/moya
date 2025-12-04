<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use Flasher\Toastr\Laravel\Facade\Toastr;

class ContactController extends Controller
{

    

  
    public function index(){
        $contacts= Contact::get();
        return view('Admin.contact.index',compact('contacts'));
    }

    public function read($id){
        $contact=Contact::find($id);
        $contact->update([
            'status'=> 1
        ]);
        toastr::success('success', 'contact deleted successfully');
        return redirect()->route('admin.contact.index');
    }

    public function destroy($id){
        $about= Contact::find($id);

        if($about){

            $about->update([
                'status'=>1
            ]);

        }
        Alert::success('success', 'contact deleted successfully');
          return redirect()->route('admin.contact.index');

    }
}
