<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $socialMedia = SocialMedia::orderBy('id')->get();
        return view('Admin.social-media.index', compact('socialMedia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $social = SocialMedia::findOrFail($id);
        return view('Admin.social-media.edit', compact('social'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|string|max:500',
            'icon' => 'nullable|string|max:100'
        ]);

        $social = SocialMedia::findOrFail($id);
        
        $social->update([
            'value' => $request->value,
            'icon' => $request->icon
        ]);

        return redirect()->route('admin.social-media.index')
            ->with('success', 'تم تحديث إعدادات التواصل بنجاح');
    }

    /**
     * Update multiple social media settings at once.
     */
    public function bulkUpdate(Request $request)
    {
        $socialData = $request->except('_token');
        
        try {
            DB::beginTransaction();
            
            foreach ($socialData as $key => $value) {
                if (strpos($key, 'value_') === 0) {
                    $id = str_replace('value_', '', $key);
                    SocialMedia::where('id', $id)->update(['value' => $value]);
                }
                
                if (strpos($key, 'icon_') === 0) {
                    $id = str_replace('icon_', '', $key);
                    SocialMedia::where('id', $id)->update(['icon' => $value]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.social-media.index')
                ->with('success', 'تم تحديث جميع الإعدادات بنجاح');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }
}