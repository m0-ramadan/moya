@extends('Admin.layout.master')

@section('title')
Package
@endsection

@section('css')
<!-- Plugins css start-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
<!-- Plugins css Ends-->
@endsection

@section('content')


<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>Packages</h5>

            <a class="btn btn-success" href="{{route('admin.package.create')}}">Add </a>
            @if($status->packagestatus==0)
             <a class="btn btn-success" href="{{route('admin.package.status')}}">Active </a>
             @else($status->packagestatus==1)
               <a class="btn btn-success" href="{{route('admin.package.status')}}">Disactive </a>
             @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Number</th>
                            <th>weight</th>
                            <th> description</th>
                             <th> Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $key=>$package)
                        <tr>
                            <td> {{++$key}}       </td>
                            <td>{{$package->name}} </td>
                            <td>{{$package->price}} </td>
                            <td>{{$package->number}}</td>
                            <td>{{$package->weight}}</td>
                            <td>@if($package->status == 1) <p class="text-success">Active </p>@else <p class="text-danger"> inactive</p>@endif</td>
                            <td>{{$package->description}}</td>

                            <td>
                               <button class="btn btn-success"> <a  href="{{route('admin.package.edit',[$package->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                                <form method="post" action="{{route('admin.package.destroy',$package->id)}}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-primary"  ><i class="fa fa-trash-o"></i></button>
                                </form>

                            </td>

                        </tr>


                            @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js')
<!-- Plugins JS start-->
<script src="{{asset('assets/js/datatable/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/js/datatable/datatables/datatable.custom.js')}}"></script>
<script src="{{asset('assets/js/tooltip-init.js')}}"></script>
<!-- Plugins JS Ends-->
@endsection
