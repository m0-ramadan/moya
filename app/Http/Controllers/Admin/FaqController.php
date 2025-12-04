<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Http\Requests\Admin\Faq\StoreFaqRequest;
use App\Http\Requests\Admin\Faq\UpdateFaqRequest;
use Flasher\Toastr\Laravel\Facade\Toastr;

class FaqController extends Controller
{
    

    
    public function index(){
        $faqs=Faq::orderBy('item_order')->get();
        return view('Admin.faq.index',compact('faqs'));
    }


    public function create(){
        return view('Admin.faq.create');
    }

    public function store( StoreFaqRequest $request){
        Faq::create([
            'question'=> $request->question,
            'answer'  => $request->answer,
            'item_order' => $request->item_order,
           ]);
           toastr::success('success','sucessfully added');
           return redirect()->route('admin.faq.index');
    }

    public function edit($id){
        $faq=Faq::find($id);
        return view('Admin.faq.edit',compact('faq'));
    }

    public function update(UpdateFaqRequest $request){
        $faq=Faq::find($request->id);
        $faq->update([
            'question'=> $request->question,
            'answer'  => $request->answer,
            'item_order' => $request->item_order,
           ]);
           toastr::success('success','sucessfully updated');
           return redirect()->route('admin.faq.index');
    }

    public function destroy($id){
        $faq=Faq::find($id);
        if($faq){
            $faq->delete();
            toastr::success('success','sucessfully updated');
            return redirect()->route('admin.faq.index');
        }
        toastr::error('error','not found');
            return redirect()->route('admin.faq.index');
    }
}
