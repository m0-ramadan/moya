@extends('Admin.layout.master')

@section('title')
    Product
@endsection

@section('css')
    <!-- Plugins css start-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/datatables.css') }}">
    <!-- Plugins css Ends-->
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>مخازن التجار</h5>

                {{-- <a class="btn btn-success" href="{{ route('admin.product.create') }}">Add </a> --}}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="basic-1">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم التاجر</th>
                                <th>الكود</th>
                                <th>الفرع</th>
                                <!-- <th>سعر الشراء</th> -->
                                {{-- <th>السعر</th>
                                <th>التاجر</th>
                                <th>كود المنتج</th>
                                <th>الفئة</th>
                                <th>العلامة التجارية </th>
                                <th>الحد الادنى</th>
                                <th>الخصم</th> --}}
                                <!-- <th>هامش الربح</th> -->
                                <th>العمليات</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $key => $client)
                                <tr>
                                    <td> {{ ++$key }} </td>
                                    <td>{{ $client->name }} </td>
                                    {{-- <td> <img style="width:50px;" src="{{ asset($product->image) }}"> </td> --}}
                                    <td>{{ $client->code ?? '--' }} </td>
                                    <td>{{ $client->branch->name ?? '--' }}</td>
                                    {{-- <td>{{ $product->sale_price ?? '--' }}</td>
                                    <td>{{ $product->person->name ?? '--' }}</td>
                                    <td>{{ $product->code ?? '--' }}</td>
                                    <td>{{ $product->category ?? '--' }}</td>
                                    <td>{{ $product->brand ?? '--' }}</td>
                                    <td>{{ $product->minimum_stock ?? '--' }}</td>
                                    <td>{{ $product->discounts ?? '--' }}</td>
                                    <!-- <td>{{ $product->expected_profit_margin }}</td> --> --}}


                                    <td class="d-flex gap-1">
                                        {{-- <!-- زر التعديل -->
                                        <a href="{{ route('admin.product.edit', [$clients->id]) }}" class="btn btn-success"
                                            title="تعديل">
                                            <i class="fa fa-edit text-white"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.product.destroy', $clients->id) }}"
                                            onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المنتج؟');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger" title="حذف">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        </form>
                                        <!-- زر الحذف --> --}}

                                        {{-- <a href="" class="btn btn-success" title="تعديل">
                                            <i class="fa fa-edit text-white"></i>
                                        </a> --}}
                                        <a class="btn btn-success" href="{{ route('admin.product.show', [$client->id]) }}">
                                            <i class="fa fa-eye text-white"></i>
                                        </a>
                                        <form method="POST" action=""
                                            onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المنتج؟');">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger" title="حذف">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
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
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/tooltip-init.js') }}"></script>
    <!-- Plugins JS Ends-->
@endsection
