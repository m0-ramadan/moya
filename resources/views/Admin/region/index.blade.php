@extends('Admin.layout.master')

@section('title', 'المدن')
 
@section('css')
<!-- Plugins css start-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
<!-- Plugins css Ends-->
@endsection

@section('content')


<div class="col-sm-12">
    <div class="card">
        <div class="card-header">

            <a class="btn btn-success" href="{{route('admin.region.create')}}">اضافة </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th  class="center">#</th>
                            <th class="center">الدولة</th>
                             <th class="center">المدينة</th>
                             <th class="center">الكود</th>
                            <th class="right" style="text-align: right !important;">العمليات</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regions as $key=>$region)
                        <tr>
                            <td class="center"> {{++$key}}</td>
                            <td class="center">{{$region->country->country_ar}}</td>
                            <td class="center">{{$region->region_ar}} </td>
                            <td class="center">{{$region->key}} </td>


                            <td class="d-flex gap-1 right">
                               <button class="btn btn-success"> <a  href="{{route('admin.region.edit',[$region->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                                <form method="post" action="{{route('admin.region.destroy',$region->id)}}">
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
