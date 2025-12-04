@extends('Admin.layout.master')

@section('title')
due
@endsection

 

@section('content')


<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>dues</h5>


        </div>
        <div class="card-body">
            <div class="table-responsive">
                 <table class="display" id="example">
                <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>amount</th>
                            <th>referance number</th>
                            <th>image</th>
                            <th>status</th>
                            <th>created at</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dues  as $key=>$due)
                        <tr>
                            <td> {{++$key }}    </td>
                            <td> <a href="{{route('admin.user.show',$due->user->id)}}">{{$due->user->name}} </a></td>
                            <td>{{$due->amount}}</td>
                            <td>{{$due->reference_number}} </td>
                            <td>@if($due->image)<img style="width:80px;" src={{asset($due->image)}}> @endif</td>
                           <td> @if($due->status == 0)
                                    Waiting
                                 @elseif($due->status == 1)
                                    Transfered
                                 @elseif($due->status == 2)
                                    Rejected
                                 @endif
                            </td>
                            <td>{{ $due->created_at }}</td>
                            <td>
                            @if($due->status == 0)
                                <button class="btn btn-danger"><a href="{{route('admin.due.reject',$due->id)}}" class='text-white'>reject</a> </button>
                                <button class="btn btn-success"> <a href="{{route('admin.due.edit',$due->id)}}" ><i class="fa fa-edit text-white"></i>  </a> </button>
                            @endif
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
 
<script>
$(document).ready(function() {

        var table = $('#example').DataTable({
             pagingType: 'full_numbers',
             dom: 'lBfrtip',
          buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                      columns: [0, 1, 2,3,5,6]
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                      columns: [0, 1, 2,3,5,6]
                }
            },
          
            {
                extend: 'print',
                exportOptions: {
                   columns: [0, 1, 2,3,5,6]
                }
            },
            'colvis'
        ],
         });
         
} );
</script>
@endsection
