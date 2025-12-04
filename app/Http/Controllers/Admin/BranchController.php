<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use App\Models\Branchs;
use App\Models\Country;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Models\BranchssPricing;
use App\Traits\SyncClientToErp;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Flasher\Toastr\Laravel\Facade\Toastr;
use Flasher\Toastr\Prime\ToastrInterface;

class BranchController extends Controller
{
    use SyncClientToErp;
    // public function __construct()
    //     {
    //         // Ensure auth:admin middleware is applied
    //         $this->middleware('auth:admin');
    //         // Apply Spatie permission middleware
    //         $this->middleware('permission:عرض الفروع', ['only' => ['index', 'show']]);
    //         $this->middleware('permission:إضافة فرع', ['only' => ['create', 'store']]);
    //         $this->middleware('permission:تعديل الفروع', ['only' => ['edit', 'update']]);
    //         $this->middleware('permission:حذف الفروع', ['only' => ['destroy']]);
    //         $this->middleware('permission:عرض المخازن الصادرة', ['only' => ['outgoingWarehouses']]);
    //         $this->middleware('permission:عرض المخازن الواردة', ['only' => ['incomingWarehouses']]);
    //     }
    public function index()
    {
        $branchs = Branchs::get();
        return view('Admin.branchs.index', compact('branchs'));
    }

    public function prices()
    {
        $prices = BranchssPricing::get();
        return view('Admin.branchs.prices', compact('prices'));
    }


    public function create()
    {
        $countries = Country::get();
        $regions = Region::get();
        return view('Admin.branchs.create', compact('countries', 'regions'));
    }


    public function createpirce()
    {
        $countries = Country::get();
        $regions = Region::get();
        $branchs = Branchs::get();
        return view('Admin.branchs.createpirce', compact('branchs', 'countries', 'regions'));
    }

    public function pricestore(Request $request)
    {

        $regionId = Branchs::find($request->branchOne);

        BranchssPricing::create([
            'name' => $request->name,
            'branchss_id_1' => $request->branchOne,
            'city_id' => $request->city_id,
            'currency' => $request->currency,
            'city_id_from' => $regionId->region_id,
            'price' => $request->price,
        ]);
        Toastr::addSuccess('success', 'تمت الاضافة بنجاح');
        return redirect()->route('admin.branch.prices');
    }


    public function store(Request $request)
    {
        $branch = Branchs::create([
            'name' => $request->name_ar,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address' => $request->address,
            'link_address' => $request->map,
            'price' => $request->price,
            'country_id' => $request->country_id,
            'region_id' => $request->regions_id,
            'key' => $request->code,
        ]);

        $this->createBranch($branch);
        Toastr::addSuccess('success', 'تم الاضافة بنجاح');
        return redirect()->route('admin.branch.index');
    }

    public function edit($id)
    {

        $branch = Branchs::find($id);
        $countries = Country::get();
        $regions = Region::get();
        return view('Admin.branchs.edit', compact('countries', 'branch', 'regions'));
    }


    public function editprice($id)
    {
        $regions = Region::get();
        $pricing = BranchssPricing::find($id);
        $branchs = Branchs::get();
        return view('Admin.branchs.editprice', compact('regions', 'branchs', 'pricing'));
    }




    public function updateprice(Request $request)
    {
        $branchprice = BranchssPricing::find($request->id); // تأكد أنك جلبت الفرع الموجود
        $regionId = Branchs::find($request->branchOne);
        $branchprice->update([
            'name' => $request->name,
            'branchss_id_1' => $request->branchOne ?? $branchprice->branchss_id_1,
            'city_id' => $request->city_id,
            'currency' => $request->currency,
            'price' => $request->price,
            'city_id_from' => $regionId->region_id ?? $branchprice->city_id_from,
        ]);
        Toastr::addSuccess('Success', 'تم التحديث بنجاح');
        return redirect()->route('admin.branch.prices');
    }



    public function update(Request $request)
    {
        $branch = Branchs::find($request->id); // تأكد أنك جلبت الفرع الموجودئ
        $branch->update([
            'name' => $request->name_ar,
            'phone1' => $request->phone1,
            'phone2' => $request->phone2,
            'address' => $request->address,
            'link_address' => $request->map,
            'price' => $request->price,
            'country_id' => $request->country_id,
            'region_id' => $request->regions_id,
            'key' => $request->code,
        ]);

        Toastr::addSuccess('Success', 'تم التحديث بنجاح');
        return redirect()->route('admin.branch.index');
    }


    public function destroy($id)
    {
        $branchs = Branchs::find($id);
        if ($branchs) {
            $this->deleteErpBranch($id);
            $branchs->vaults()->delete(); // حذف الخزنات أولاً
            $branchs->delete();
            Toastr::addSuccess('تم التحديث بنجاح');
            return redirect()->route('admin.branch.index');
        }
        Toastr::adderror('غير موجود');
        return redirect()->route('admin.branch.index');
    }



    public function destroyprice($id)
    {
        $branchs = BranchssPricing::find($id);
        if ($branchs) {
            $branchs->delete();
            Toastr::addSuccess('تم التحديث بنجاح');
            return redirect()->route('admin.branch.prices');
        }
        Toastr::addSuccess('not found');
        return redirect()->route('admin.branch.prices');
    }




    public function getRegions($country_id)
    {
        $regions = Region::where('country_id', $country_id)->get();
        return response()->json($regions);
    }
    public function show($id)
    {
        $shipments = Shipment::with([
            'contents' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'person',
            'branchFrom',
            'branchTo'
        ])
            ->where('type', 1)
            ->where('is_priced', 2)
            ->where('branches_from', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $branchInfo = Branchs::find($id);

        $statistics = [
            'total_shipments' => $shipments->count(),
            'total_contents' => $shipments->sum(function ($shipment) {
                return $shipment->contents->count();
            }),
            'total_value' => $shipments->sum('price'),
            'active_shipments' => $shipments->where('status', 'active')->count()
        ];

        $path = 'Admin.branchs.show';

        return view($path, compact('shipments', 'branchInfo', 'statistics'));
    }

    public function outgoingWarehouses($id)
    {
        $shipments = Shipment::where('branches_from', $id)->whereHas('contents')->orderByDesc('created_at')->get();

        return view('Admin.stores.outgoing', compact('shipments'));
    }

    public function incomingWarehouses($id)
    {
        try {
            $userBranchId = $id;

            $shipments = Shipment::whereHas('warehouse.trip', function ($query) use ($userBranchId) {
                $query->where('branches_to', $userBranchId);
            })
                ->with([
                    'warehouse' => function ($query) {
                        $query->whereNotNull('trip_id')->where('finnished', 1)
                            ->with([
                                'shContent' => function ($query) {
                                    $query->select('id', 'shipment_id', 'code', 'name', 'price');
                                },
                                'trContent' => function ($query) {
                                    $query->select('id', 'trip_id', 'shipment_content_id', 'quantity', 'taken', 'status_id')
                                        ->with(['shipmentContent' => function ($query) {
                                            $query->select('id', 'shipment_id', 'code', 'name', 'price');
                                        }]);
                                },
                                'trip' => function ($query) {
                                    $query->select('id', 'branches_to');
                                }
                            ]);
                    },
                    'person' => function ($query) {
                        $query->select('id', 'name');
                    }
                ])->orderByDesc('created_at')
                ->get();
            return view('Admin.stores.incoming', compact('shipments'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء جلب الشحنات: ' . $e->getMessage());
        }
    }
}
