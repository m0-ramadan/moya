@extends('Admin.layout.master')

@section('title')
Countries
@endsection

@section('css')
<!-- Plugins css start-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/datatables.css')}}">
<!-- Plugins css Ends-->
@endsection

@section('content')


<div class="col-sm-12">
    <div class="card">
         
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th style="text-align: center;">#</th>
                            <th  style="text-align: center;">من عملة </th>
                            <th style="text-align: center;">الى عملة </th>
                            <th style="text-align: center;">معامل التحويل</th>
                            <th style="text-align: center;">العمليات</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach($currencies as $key=>$currency)
                        <tr>
                            <td style="text-align: center;"> {{++$key}}       </td>
                            <td style="text-align: center;">{{ $currency->from_currency_name  }}</td>
                            <td style="text-align: center;">{{ $currency->to_currency_name  }}</td>
                            <td style="text-align: center;">{{$currency->conversion_rate}} </td>
                            <td style="text-align: center;"   class="d-flex gap-1">
                
                               <button class="btn btn-success"> <a  href="{{route('admin.currency.edit',[$currency->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                               
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
