<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Interfaces\Admin\SubscribeInterface;

class SubscribeController extends Controller
{
    protected $subscribeInterface;

    public function __construct(SubscribeInterface $subscribeInterface)
    {
      $this->subscribeInterface = $subscribeInterface;
    }

    public function index(){
       return  $this->subscribeInterface->index();
    }
}
