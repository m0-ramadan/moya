@php
    use App\Models\Admin;
@endphp
@extends('Admin.layout.master')

@section('title')
    user
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>users</h5>

                <a class="btn btn-success" href="{{ route('admin.user.create') }}">Add </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>email</th>
                                <th>phone</th>
                                <th>phone2</th>
                                <th>created at</th>
                                <th>id_number</th>
                                <th>address</th>
                                <th>wallet</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td> {{ ++$key }} </td>
                                    <td>{{ $user->name }} </td>
                                    <td>{{ $user->email }} </td>
                                    <td>{{ $user->phone }} </td>
                                    <td>{{ $user->phone2 }} </td>
                                    <td>{{ $user->created_at }} </td>
                                    <td>{{ $user->id_number }} </td>
                                    <td>{{ $user->address }}</td>
                                    <td>{{ $user->wallet }}</td>

                                    <td>
                                        @admin(Admin::ROLE_SUPER_ADMIN . ',' . Admin::ROLE_ADMIN)
                                            <button class="btn btn-success"> <a
                                                    href="{{ route('admin.user.show', [$user->id]) }}"><i
                                                        class="fa fa-eye text-white"></i> </a> </button>
                                        @endadmin
                                        @admin(Admin::ROLE_SUPER_ADMIN)
                                            <button class="btn btn-success "> <a
                                                    href="{{ route('admin.user.edit', [$user->id]) }}"><i
                                                        class="fa fa-edit text-white"></i> </a> </button>


                                            <form method="post" action="{{ route('admin.user.destroy', $user->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-primary"><i
                                                        class="fa fa-trash-o"></i></button>
                                            </form>
                                            <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                                data-original-title="test" data-bs-target="#exampleModal"><i
                                                    class="fa fa-bell"></i> </button>
                                        @endadmin

                                    </td>

                                </tr>

                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form action="{{ route('admin.user.sendnotify') }}" method="post">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                                    <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <div class="mb-3">
                                                        <label class="col-form-label" for="Message-name">Message:</label>
                                                        <input class="form-control" type="text" name="message">
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-primary" type="button"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button class="btn btn-secondary" type="submit">Save changes</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('js')
    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 4, 5, 6, 7, 8]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 4, 5, 6, 7, 8]
                        }
                    },

                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 4, 5, 6, 7, 8]
                        }
                    },
                    'colvis'
                ],


            });
        });
    </script>
@endsection
