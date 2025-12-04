<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Country\CreateCountryRequest;
use App\Http\Requests\Admin\Country\UpdateCountryRequest;
use Flasher\Toastr\Prime\ToastrInterface;
use Flasher\Toastr\Laravel\Facade\Toastr;




use App\Models\Country;

class CountryController extends Controller
{
    protected $countryInterface;

    // public function __construct()
    // {
    //     // Ensure auth:admin middleware is applied
    //     //  $this->middleware('auth:admin');
    //     // Apply Spatie permission middleware
    //     $this->middleware('permission:عرض الدول', ['only' => ['index']]);
    //     $this->middleware('permission:إضافة دولة', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:تعديل الدول', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:حذف الدول', ['only' => ['destroy']]);
    // }

    public function index()
    {
        $countries = Country::orderBy('item_order')->get();
        return view('Admin.country.index', compact('countries'));
    }


    public function create()
    {

        return view('Admin.country.create');
    }

    public function store(Request $request)
    {
        Country::create([
            'country'                => $request->country,
            'country_ar'            => $request->country_ar,
            'message'                => $request->message,
            'collection_commission'  => $request->collection_commission,
        ]);
        toastr()->success('success', 'sucessfully added');
        return redirect()->route('admin.country.index');
    }

    public function edit($id)
    {
        $country = Country::find($id);
        return view('Admin.country.edit', compact('country'));
    }

    public function update(Request $request)
    {


        $country = Country::find($request->id);


        $country->update([
            'country'                 => $request->country,
            'country_ar'              => $request->country_ar,
            'message'                => $request->message,
            'collection_commission'  => $request->collection_commission,

        ]);
        toastr()->success('success', 'sucessfully updated');
        return redirect()->route('admin.country.index');
    }

    public function destroy($id)
    {

        $country = Country::find($id);
        if ($country) {
            $country->delete();
            Toastr::addSuccess('Deleted successfully');
            return redirect()->route('admin.country.index');
        }
        toastr()->success('not found');
        return redirect()->route('admin.country.index');
    }
}
