@extends('Admin.layout.master')
 @section('title')
Faq
@endsection
 @section('css')
<!-- Plugins css start-->
 <!-- Plugins css Ends-->
@endsection

@section('content')


<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>Faq</h5>

            <a class="btn btn-success" href="{{route('admin.faq.create')}}">Add </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="example">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($faqs as $key=>$faq)
                        <tr>
                            <td> {{++$key}}       </td>
                            <td>{{$faq->question}} </td>
                            <td>{{mb_substr($faq->answer,0,50)}} </td>


                            <td class="d-flex gap-1">
                               <button class="btn btn-success"> <a  href="{{route('admin.faq.edit',[$faq->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                                <form method="post" action="{{route('admin.faq.destroy',$faq->id)}}">
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
 
<script>
$(document).ready(function() {

        var table = $('#example').DataTable({
             pagingType: 'full_numbers',
             dom: 'lBfrtip',
          buttons: [
            {
                extend: 'copyHtml5',
                exportOptions: {
                      columns: [0, 1, 2]
                }
            },
            {
                extend: 'excelHtml5',
                exportOptions: {
                      columns: [0, 1, 2]
                }
            },
          
            {
                extend: 'print',
                exportOptions: {
                    columns: [0, 1, 2]
                }
            },
            'colvis'
        ],
         });
         
} );
 

</script>
@endsection
