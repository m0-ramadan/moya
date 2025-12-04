@extends('Admin.layout.master')

@section('title')
subscribe
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
            <h5>subscribes</h5>


        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>

                            <th>Package</th>
                            <th>Price</th>

                            <th>Weight</th>
                            <th>Added Weight</th>
                            <th>Total Weight</th>

                            <th>Number of shipment</th>
                            <th>Number of Done shipment</th>
                            <th>Remain shipment</th>

                            <th>Status</th>
                            <th>Created at</th>



                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscribes as $key=>$subscribe)
                        <tr>
                            <td> {{++$key}} </td>
                            <td>{{$subscribe->user->name}}</td>
                            <td>{{ $subscribe->package->name }}</td>
                            <td>{{ $subscribe->package->price }}</td>

                            <td>{{ $subscribe->weight - $subscribe->added_weight }}</td>
                            <td>{{ $subscribe->added_weight }}</td>
                            <td>{{ $subscribe->weight  }}</td>

                            <td>{{ $subscribe->number_of_shipments }}</td>
                            <td>{{ $subscribe->used_times }} </td>
                            <td>{{ $subscribe->number_of_shipments - $subscribe->used_times }}
                            </td>

                            <td>{{ $subscribe->status == 1 ? 'عملية ناجحة' : 'مرفوضة' }} </td>
                            {{-- <td>{{ $subscribe->trans_code }}</td> --}}
                            <td>{{ $subscribe->created_at }}</td>
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
