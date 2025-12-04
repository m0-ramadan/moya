@extends('Admin.layout.master')
 @section('title')
 الخزينة
@endsection
 @section('css')
<!-- Plugins css start-->
 <!-- Plugins css Ends-->
@endsection

@section('content')


<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
              <a class="btn btn-success" href="{{route('admin.lockers.create')}}">اضافة </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="example">
                    <thead>
                        <tr>
                            <th style="text-align: center;">#</th>
                            <th style="text-align: center;">الخزينة</th>
                            <th style="text-align: center;">رصيد افتتاحى</th>
                            <th style="text-align: center;">الفرع</th>
                            <th style="text-align: center;">العملة</th>
                            <th style="text-align: center;">العمليات</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lockers as $key=>$locker)
                        <tr>
                            <td style="text-align: center;"> {{++$key}}       </td>
                            <td style="text-align: center;">{{$locker->name}} </td>
                            <td style="text-align: center;">{{mb_substr($locker->balance,0,50)}} </td>
                            <td style="text-align: center;">{{mb_substr($locker->branch->name,0,50)}} </td>
                            <td style="text-align: center;">{{mb_substr($locker->currency_name,0,50)}} </td>

                            <td class="d-flex gap-1"  style="text-align: center;">
                               <button class="btn btn-success"> <a  href="{{route('admin.lockers.edit',[$locker->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                                <form method="post" action="{{route('admin.lockers.destroy',$locker->id)}}">
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
