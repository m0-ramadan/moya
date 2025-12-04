@extends('Admin.layout.master')

@section('title')
notifications
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
            <h5>notifications</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>At</th>
                            <th>Notification</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $key=>$notification)
                            <tr>
                                <td> {{++$key}}       </td>
                                <td>{{ $notification->created_at->diffForHumans()}}</td>
                                <td>{{ $notification->data['message']}}</td>
                                <td><a class='btn btn-success' href='{{ route('admin.notifications.mark_read', $notification) }}'>Make Read</a></td>
    
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
