<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Flasher\Toastr\Laravel\Facade\Toastr;
use App\Models\Slider;
use App\Http\Requests\Admin\Slider\CreateSliderRequest;
use App\Http\Requests\Admin\Slider\UpdateSliderRequest;
use Illuminate\Support\Facades\File;
use App\Traits\ImageTrait;

class SliderController extends Controller
{
    use ImageTrait ;

    public function index()
    {
        $sliders = Slider::orderBy('item_order')->get();
        return view('Admin.slider.index', compact('sliders'));
    }

    public function create()
    {
        return view('Admin.slider.create');
    }

    public function store(CreateSliderRequest $request)
    {
        $image = null;

        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $image = $this->uploadImg($request->image, $filename, 'slider');
        //    $image = 'images/slider/' . $image;
        }
       
        

        Slider::create([
            'type' => $request->type,
            'image' => $image,
            'item_order' => $request->item_order,
        ]);
        //dd( $image);
        Toastr::success('Successfully added', 'Success');
        return redirect()->route('admin.slider.index');
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('Admin.slider.edit', compact('slider'));
    }

    public function update(UpdateSliderRequest $request )
    {
        $slider = Slider::findOrFail($request->id);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($slider->image && File::exists(public_path($slider->image))) {
                File::delete(public_path($slider->image));
            }

            $filename = time() . '.' . $request->image->extension();
            $image = $this->uploadImg($request->image, $filename, 'slider');
            $slider->image = $image;
        }

        $slider->type = $request->type;
        $slider->item_order = $request->item_order;
        $slider->save();

        Toastr::success('Successfully updated', 'Success');
        return redirect()->route('admin.slider.index');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);

        // Delete image
        if ($slider->image && File::exists(public_path($slider->image))) {
            File::delete(public_path($slider->image));
        }

        $slider->delete();

        Toastr::success('Successfully deleted', 'Success');
        return redirect()->back();
    }

    private function uploadImage($image, $filename, $folder)
    {
        $path = public_path('images/' . $folder);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $image->move($path, $filename);
        return $filename;
    }
}
