@extends('Admin.layout.master')

@section('title')
slider
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
            <h5>sliders</h5>

            <a class="btn btn-success" href="{{route('admin.slider.create')}}">Add </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="display" id="basic-1">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>  الصورة</th>
                            <th> مكان الصورة</th>
                             <th>العمليات</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sliders as $key=>$slider)
                        <tr>
                            <td> {{++$key}}       </td>
                            <td> <img style= "width:200px;"src= "{{asset($slider->image)}}"> </td>
                            <td> {{ $slider->type == 1 ? "على الموبايل" : "الموقع" }}</td>
                             <td  class="d-flex gap-1">
                               <button class="btn btn-success"> <a  href="{{route('admin.slider.edit',[$slider->id])}}"><i class="fa fa-edit text-white"></i>  </a> </button>
                                <form method="post" action="{{route('admin.slider.destroy',$slider->id)}}">
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
