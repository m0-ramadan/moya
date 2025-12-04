@php
    use App\Models\Admin;
@endphp
@extends('Admin.layout.master')

@section('title')
    userdetails
@endsection

@section('css')
@endsection

@section('content')
    @admin(Admin::ROLE_SUPER_ADMIN)
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="pro-group pt-0 border-0">
                        <div class="product-page-details mt-0">
                            <h3>Id Informstion</h3>
                        </div>
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{!! \Session::get('success') !!}</li>
                                </ul>
                            </div>
                        @endif
                        <div class="product-price">
                            <ul>
                                @if (!is_null($user->id_number))
                                    <li class="mb-3">ID Number: {{ $user->id_number }}</li>
                                    <li class="mb-3">ID Image: <img style="width:200px" src="{{ asset($user->id_image) }}">
                                    </li>
                                    @if ($user->verified == 0 || $user->verified == 3)
                                        <li class="mb-3">
                                            <a class="btn btn-success" href="{{ route('admin.user.verify', $user->id) }}">Verify
                                                User</a>
                                            <a class="btn btn-danger" onclick="showRejectTextArea()">Reject
                                                Data</a>
                                        </li>
                                        <div id="rejectTextArea" style="display: none;">
                                            <form action="{{ route('admin.user.reject', $user->id) }}" method="post">
                                                @csrf
                                                <div class="form-group">
                                                    <textarea class="form-control" id="rejectMessage" name="rejected_message" placeholder="Reject Message"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-danger">Send</button>
                                            </form>
                                        </div>
                                    @elseif($user->verified == 1)
                                        <div class="">
                                            <p class="text-success">Verifed</p>
                                        </div>
                                    @elseif($user->verified == 2)
                                        <div class="">
                                            <p class="text-danger">Rejected</p>
                                        </div>
                                    @endif
                                @else
                                    <li class="mb-3">No Data To Verify</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="pro-group pt-0 border-0">
                        <div class="product-page-details mt-0">
                            <h3>Email Verification</h3>
                        </div>
                        <div class="product-price">
                            <ul>
                                @if (is_null($user->email_verified_at))
                                    <a class="btn btn-success" href="{{ route('admin.user.verify-email', $user->id) }}">Verify
                                        User Email</a>
                                @else
                                    <li class="mb-3">Email Verified</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endadmin

    @admin(Admin::ROLE_SUPER_ADMIN . ',' . Admin::ROLE_ADMIN)
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="pro-group pt-0 border-0">
                        <div class="product-page-details mt-0">
                            <h3>Wallet control</h3>
                        </div>
                        <div class="product-price mb-5">
                            Wallet: {{ $user->wallet }} SAR
                        </div>
                        <div class="product-price">
                            <ul>
                                <li class="mb-3">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                        data-original-title="test" data-bs-target="#exampleModal"><i class="fa fa-edit"></i>
                                    </button>
                                </li>
                            </ul>
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('admin.user.walletcontrol') }}" method="post">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Edit Wallet</h5>
                                                <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <div class="mb-3">
                                                    <label class="col-form-label" for="Message-name">Amount</label>
                                                    <input class="form-control" type="text" name="amount">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="exampleFormControlSelect1">Type</label>
                                                    <select class="form-control" id="exampleFormControlSelect1" name="type">
                                                        <option value="">Select type</option>
                                                        <option value="withdraw">Withdraw</option>
                                                        <option value="deposit">Deposit</option>
                                                    </select>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endadmin

    @admin(Admin::ROLE_SUPER_ADMIN)
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="pro-group pt-0 border-0">
                        <div class="product-page-details mt-0">
                            <h3>Package control</h3>
                        </div>

                        <div class="product-price">
                            <ul>
                                <li class="mb-3">
                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                        data-original-title="test" data-bs-target="#package"><i class="fa fa-edit"></i>
                                    </button>
                                </li>
                            </ul>
                            <div class="modal fade" id="package" tabindex="-1" role="dialog"
                                aria-labelledby="packageLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('admin.user.package-control') }}" method="post">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="packageLabel">Add Package</h5>
                                                <button class="btn-close" type="button" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                                <div class="mb-3">
                                                    <label for="exampleFormControlSelect1">Package</label>
                                                    <select class="form-control" id="exampleFormControlSelect1"
                                                        name="package_id">
                                                        <option value="">Select Package</option>
                                                        @foreach ($packages as $package)
                                                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                                                        @endforeach
                                                    </select>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($user->userBank))
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="pro-group pt-0 border-0">
                            <div class="product-page-details mt-0">
                                <h3>Bank Information</h3>
                            </div>
                            <div class="product-price">
                                <ul>
                                    <li class="mb-3"> Bank Name : {{ $user->userBank->bank_name }}</li>
                                    <li class="mb-3"> Full Name : {{ $user->userBank->full_name }}</li>
                                    <li class="mb-3"> Bank Account Number : {{ $user->userBank->bank_account_number }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5>Due Requests </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>amount</th>
                                    <th>referance number</th>
                                    <th>image</th>

                                    <th>status</th>
                                    <th>created at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->userDue as $key => $due)
                                    <tr>
                                        <td> {{ ++$key }} </td>
                                        <td>{{ $due->amount }}</td>
                                        <td>{{ $due->reference_number }} </td>
                                        <td>
                                            @if ($due->image)
                                                <img style="width:80px;" src={{ asset($due->image) }}>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($due->status == 0)
                                                Waiting
                                            @elseif($due->status == 1)
                                                Transfered
                                            @endif
                                        </td>
                                        <td>{{ $due->created_at }}</td>
                                        <td>
                                            @if ($due->status == 0)
                                                <button class="btn btn-success"> <a
                                                        href="{{ route('admin.due.edit', $due->id) }}"><i
                                                            class="fa fa-edit"></i> </a> </button>
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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> subscribes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="example1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>package</th>
                                    <th>amount</th>
                                    <th>remain</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->subscribes as $key => $subscribe)
                                    <tr>
                                        <td> {{ ++$key }} </td>
                                        <td>{{ $subscribe->package->name }}</td>
                                        <td>{{ $subscribe->package->number }} {{ $subscribe->package->unit }}</td>
                                        <td>{{ $subscribe->remain }} </td>
                                        <td>
                                            @if ($subscribe->status == 0)
                                                <p class="text-danger"> uncompleted </p>
                                            @elseif($subscribe->status == 1)
                                                <p class="text-success"> paid </p>
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
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> Shipments </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="example2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>code</th>
                                    <th>sender name</th>
                                    <th>sender phone</th>
                                    <th>client name</th>
                                    <th>client phone</th>
                                    <th>created at</th>
                                    <th>status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->shipments as $key => $shipment)
                                    <tr>
                                        <td> {{ ++$key }} </td>
                                        <td> {{ $shipment->code }} </td>
                                        <td> {{ $shipment->sender_name }} </td>
                                        <td> {{ $shipment->sender_email }} </td>
                                        <td> {{ $shipment->client_name }} </td>
                                        <td> {{ $shipment->client_phone }} </td>
                                        <td> {{ $shipment->created_at }}</td>
                                        <td>
                                            <span class="badge badge-success">
                                                @if ($shipment->status == 0)
                                                    shipment error
                                                @elseif($shipment->status == 1)
                                                    shipment in progress
                                                @elseif($shipment->status == 2)
                                                    shipment being delivered
                                                @elseif($shipment->status == 3)
                                                    shipment delivered
                                                @else
                                                    shipment cancelled
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endadmin

    @admin(Admin::ROLE_SUPER_ADMIN . ',' . Admin::ROLE_ADMIN)
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> wallet </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="example3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Code</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->WalletDetails as $key => $wallet)
                                    <tr>
                                        <td> {{ ++$key }} </td>
                                        <td>{{ $wallet->amount }}</td>
                                        <td>{{ $wallet->payment->tran_ref ?? '' }}</td>
                                        <td>{{ $wallet->payment->created_at ?? '' }}</td>
                                        <td>
                                            <span class="badge badge-success">
                                                @if ($wallet->status == 1)
                                                    success
                                                @else
                                                    failed
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5> admin wallet </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="display" id="example4">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->adminWallets as $key => $wallet)
                                    <tr>
                                        <td> {{ ++$key }} </td>
                                        <td>{{ $wallet->amount }}</td>
                                        <td>{{ $wallet->type }}</td>
                                        <td>{{ $wallet->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endadmin
@endsection

@section('js')
    <!-- Plugins JS start-->

    <script>
        $(document).ready(function() {
            $('#example').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },

                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    'colvis'
                ],


            });

            $('#example2').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },

                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    'colvis'
                ],


            });


            $('#example3').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },

                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    'colvis'
                ],


            });

            $('#example4').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },

                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3]
                        }
                    },
                    'colvis'
                ],


            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                pagingType: 'full_numbers',
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copyHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },

                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    'colvis'
                ],


            });
        });
    </script>

    <script>
        function showRejectTextArea() {
            var rejectTextArea = document.getElementById("rejectTextArea");
            rejectTextArea.style.display = "block";
        }
    </script>
    <!-- Plugins JS Ends-->
@endsection
