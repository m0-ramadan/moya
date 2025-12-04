@extends('Admin.layout.master')
@section('title')
    محتوي المخزن
@endsection
@section('css')
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
@endsection

@section('content')
@endsection


@section('js')
    <!-- Plugins JS start-->

    <script>
        $(document).ready(function() {

            var table = $('#example').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
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

        });
    </script>
@endsection
