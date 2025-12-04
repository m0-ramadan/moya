@extends('Admin.layout.master')

@section('title', 'الرئيسية')

@section('css')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 box-col-12 des-xl-100">
            <div class="row">
                <!-- قسم المستخدمين -->
                <div class="col-xl-3 col-md-4 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="#">
                        <div class="card income-card card-primary">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="user"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Clients::where(['type' => 1, 'status' => 0])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branch_id', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">عدد المستخدمين</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- قسم التجار -->
                <div class="col-xl-3 col-md-4 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="#">
                        <div class="card income-card card-primary">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="briefcase"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Clients::where(['type' => 2, 'status' => 0])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branch_id', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">عدد التجار</p>
                            </div>
                        </div>
                    </a>
                </div>

                @if (auth()->user()->hasRole('super_admin'))
                    <!-- قسم المندوبين -->
                    <div class="col-xl-3 col-md-4 col-sm-6 box-col-3 des-xl-25 rate-sec">
                        <a href="#">
                            <div class="card income-card card-primary">
                                <div class="card-body text-center">
                                    <div class="round-box">
                                        <i data-feather="user-check"></i>
                                    </div>
                                    <h5>{{ \App\Models\Representative::where('status', 0)->count() }}</h5>
                                    <p style="font-family: 'Cairo', sans-serif;">عدد المندوبين</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                <!-- قسم الرحلات -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="#">
                        <div class="card income-card card-primary">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="map"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Trip::when(auth()->user()->branch_id, function ($query) {
                                        return $query->where(function ($q) {
                                            $q->where('branches_from', auth()->user()->branch_id)->orWhere('branches_to', auth()->user()->branch_id);
                                        });
                                    })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">عدد الرحلات</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- شحنات بدون رحلات -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="#">
                        <div class="card income-card card-danger">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="truck"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::doesntHave('trips')->where('status_id', '!=', 5)->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branches_from', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">شحنات بدون رحلات</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- شحنات الزبائن منتظرة التسعير -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="{{ route('admin.shipment.n_priced') }}">
                        <div class="card income-card card-danger">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="truck"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::where(['type' => 1, 'is_priced' => 0])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branches_from', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">شحنات الزبائن منتظرة التسعير</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- شحنات الزبائن منتظرة التاكيد -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="{{ route('admin.shipment.priced') }}">
                        <div class="card income-card card-danger">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="truck"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::where(['type' => 1, 'is_priced' => 1, 'active' => 0])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where(function ($q) {
                                                $q->where('branches_from', auth()->user()->branch_id)->orWhere('branches_to', auth()->user()->branch_id);
                                            });
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">شحنات الزبائن منتظرة التاكيد</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- شحنات الزبائن منتظرة تاكيد الإدارة -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="{{ route('admin.shipment.n_active') }}">
                        <div class="card income-card card-danger">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="truck"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::where(['type' => 1, 'is_priced' => 2, 'active' => 0])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where(function ($q) {
                                                $q->where('branches_from', auth()->user()->branch_id)->orWhere('branches_to', auth()->user()->branch_id);
                                            });
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">شحنات الزبائن منتظرة تاكيد الإدارة</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- شحنات الزبائن -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="{{ route('admin.shipment.client') }}">
                        <div class="card income-card card-danger">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="truck"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::where(['type' => 1, 'active' => 1])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branches_from', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">شحنات الزبائن</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- شحنات التجار منتظرة تأكيد -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="{{ route('admin.shipment.n_activeMerch') }}">
                        <div class="card income-card card-danger">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="truck"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::where(['type' => 2, 'is_priced' => 2, 'active' => 0])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branches_from', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">شحنات التجار منتظرة تأكيد</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- شحنات التجار -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="{{ route('admin.shipment.index') }}">
                        <div class="card income-card card-danger">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="truck"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::where(['type' => 2, 'active' => 1])->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branches_from', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">شحنات التجار</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- الشحنات الملغاة -->
                <div class="col-xl-3 col-md-3 col-sm-6 box-col-3 des-xl-25 rate-sec">
                    <a href="#">
                        <div class="card income-card card-primary">
                            <div class="card-body text-center">
                                <div class="round-box">
                                    <i data-feather="x-circle"></i>
                                </div>
                                <h5>
                                    {{ \App\Models\Shipment::where('status_id', 5)->when(auth()->user()->branch_id, function ($query) {
                                            return $query->where('branches_from', auth()->user()->branch_id);
                                        })->count() }}
                                </h5>
                                <p style="font-family: 'Cairo', sans-serif;">عدد الشحنات الملغاة</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
