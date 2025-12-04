<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Http\Requests\Admin\User\CreateUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use Flasher\Toastr\Laravel\Facade\Toastr;
class UserController extends Controller
{protected $userInterface;

    

    public function index(){
      
    }

    public function show($id){
       

    }

    public function create(){
       
    }

    public function store( CreateUserRequest $request){
        
    }

    public function edit($id){
       
    }

    public function update(UpdateUserRequest $request){
        
    }

    public function destroy($id){
         
    }

    public function sendNotify(Request $request){
         

    }

    public function verifyEmail($id){
          
    }

    public function verify($id){
       
    }

    public function reject(Request $request){
       
    }

    public function archive(){

         
     }

     public function restore($id){
     
     }

     public function forceDelete($id){
        
     }

     public function walletControl( Request $request){
        

     }

     public function packageControl( Request $request){
         

     }
}

