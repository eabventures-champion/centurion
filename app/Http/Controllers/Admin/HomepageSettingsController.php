<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageSettingsController extends Controller
{
    public function edit()
    {
        $settings = HomepageSetting::first() ?? new HomepageSetting();
        return view('admin.homepage-settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hero_heading' => 'required|string|max:255',
            'hero_description' => 'nullable|string',
            'hero_subtext' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|max:5120',
            'objectives_title' => 'nullable|string|max:255',
            'objectives_subtitle' => 'nullable|string',
            'obj_1_title' => 'nullable|string|max:255',
            'obj_1_description' => 'nullable|string',
            'obj_2_title' => 'nullable|string|max:255',
            'obj_2_description' => 'nullable|string',
            'obj_3_title' => 'nullable|string|max:255',
            'obj_3_description' => 'nullable|string',
            'welcome_modal_message' => 'nullable|string',
            'welcome_modal_heading' => 'nullable|string|max:255',
            'show_welcome_modal' => 'nullable|boolean',
        ]);

        $data = $request->only(
            'hero_heading',
            'hero_description',
            'hero_subtext',
            'objectives_title',
            'objectives_subtitle',
            'obj_1_title',
            'obj_1_description',
            'obj_2_title',
            'obj_2_description',
            'obj_3_title',
            'obj_3_description',
            'welcome_modal_message',
            'welcome_modal_heading'
        );

        $data['show_welcome_modal'] = $request->has('show_welcome_modal');

        $settings = HomepageSetting::first() ?? new HomepageSetting();

        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($settings->background_image) {
                Storage::disk('public')->delete($settings->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('homepage', 'public');
        }

        HomepageSetting::updateOrCreate(['id' => 1], $data);

        return redirect()->back()->with('success', 'Homepage settings updated successfully!');
    }
}
