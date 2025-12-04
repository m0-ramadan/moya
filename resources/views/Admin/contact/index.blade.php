@extends('Admin.layout.master')

@section('title')
Contact us -تواصل معنا
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
            <h5>Contact Us</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Read</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $key=>$contact)
                        <tr>
                            <td> {{++$key}}       </td>
                            <td>{{$contact->name}} </td>
                            <td>{{$contact->email}} </td>
                            <td>{{$contact->subject}}</td>
                            <td>{{$contact->message}}</td>
                            <td>
                         
                                @if($contact->status == 1 )
                                <p class="text-success"> <i class="fa fa-check"></i>  </p>
                                @else
                                <a href="{{route('admin.contact.read',$contact->id)}}"> read</a></td>

@endif
                            <td>
                                <form method="post" action="{{route('admin.contact.destroy',$contact->id)}}">
                                    @csrf
                                    @method('PUT')
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
