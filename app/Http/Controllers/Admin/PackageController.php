<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Interfaces\Admin\PackageInterface;
use App\Http\Requests\Admin\Package\StorePackageRequest;
use App\Http\Requests\Admin\Package\UpdatePackageRequest;

class PackageController extends Controller
{
    protected $packageInterface;

    public function __construct(PackageInterface $packageInterface)
    {
      $this->packageInterface = $packageInterface;
    }

    public function index(){
       return  $this->packageInterface->index();
    }


    public function create(){
        return  $this->packageInterface->create();
    }

    public function store( StorePackageRequest $request){
        return  $this->packageInterface->store($request);
    }

    public function edit($id){
        return  $this->packageInterface->edit($id);
    }

    public function status(){
        return  $this->packageInterface->status();
    }


    public function update(UpdatePackageRequest $request){
        return  $this->packageInterface->update($request);
    }

    public function destroy($id){
        return  $this->packageInterface->destroy($id);
    }
}
