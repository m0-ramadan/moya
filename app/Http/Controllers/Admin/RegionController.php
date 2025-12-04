<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Country;
use App\Http\Requests\Admin\Region\CreateRegionRequest;
use App\Http\Requests\Admin\Region\UpdateRegionRequest;
use Flasher\Toastr\Prime\ToastrInterface;
use Flasher\Toastr\Laravel\Facade\Toastr;


class RegionController extends Controller
{


    public function index()
    {
        $regions = Region::get();
        return view('Admin.region.index', compact('regions'));
    }


    public function create()
    {
        $countries = Country::get();
        return view('Admin.region.create', compact('countries'));
    }

    public function store(Request $request)
    {

        Region::create([
            'region_ar' => $request->region_ar,
            'country_id' => $request->country,
            'key' => $request->key

        ]);
        toastr::success('success', 'sucessfully added');
        return redirect()->route('admin.region.index');

    }

    public function edit($id)
    {

        $countries = Country::get();
        $region = Region::find($id);
        return view('Admin.region.edit', compact('region', 'countries'));

    }

    public function update(Request $request)
    {

        $region = Region::find($request->id);
        $region->update([
            'region_ar' => $request->region_ar,
            'country_id' => $request->country,
            'key' => $request->key

        ]);

        toastr::success('success', 'sucessfully updated');
        return redirect()->route('admin.region.index');

    }

    public function destroy($id)
    {
        $region = Region::find($id);
        if ($region) {
            $region->delete();
            toastr::success('success', 'sucessfully updated');
            return redirect()->route('admin.region.index');
        }
        toastr::error('error', 'not found');
        return redirect()->route('admin.region.index');

    }

}