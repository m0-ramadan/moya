@extends('Admin.layout.master')

@section('title')
  طرق الدفع
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

            <a class="btn btn-success" href="{{route('admin.payments.create')}}">اضافة جديد</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th style="text-align: center;" class="center">#</th>
                            <th  class="center">المحتوى</th>
                            <th style="text-align:right !important;">العمليات</th>
                            

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listcontactinfo as $key=>$list)
                        <tr>
                            <td style="text-align: center;"> {{++$key }}</td>
                           <td class="text-center">{{$list->name}}</td>
                              <td style="text-align: center;"   class="d-flex gap-1">
                               <button class="btn btn-success"> <a  href="{{route('admin.payments.edit',[$list->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                                <form method="post" action="{{route('admin.payments.destroy',$list->id)}}">
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
