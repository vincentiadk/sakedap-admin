<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function templateEmail(Request $request)
    {
        if ($request->has('_token')) {
            Setting::updateOrCreate([
                'slug' => $request->slug
            ], [
                'content' => $request->content
            ]);

            return redirect()->back()->with(['success' => 'Tentang kami telah di update!']);
        } else {
            $data = [
                'title'   => 'Pengaturan Tentang Kami',
                'page'    => Setting::where('flag', 'template-email')->first(),
                'content' => 'admin.setting.about_me'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }

    public function termsCondition(Request $request)
    {
        if ($request->has('_token')) {
            Setting::updateOrCreate([
                'slug' => 'terms-condition'
            ], [
                'content' => $request->content
            ]);

            return redirect()->back()->with(['success' => 'Syarat & ketentuan telah di update!']);
        } else {
            $data = [
                'title'   => 'Pengaturan Syarat & Ketentuan',
                'page'    => Setting::where('slug', 'terms-condition')->first(),
                'content' => 'admin.setting.terms_condition'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }

    public function aboutMe(Request $request)
    {
        if ($request->has('_token')) {
            Setting::updateOrCreate([
                'slug' => 'about-me'
            ], [
                'content' => $request->content
            ]);

            return redirect()->back()->with(['success' => 'Tentang kami telah di update!']);
        } else {
            $data = [
                'title'   => 'Pengaturan Tentang Kami',
                'page'    => Setting::where('slug', 'about-me')->first(),
                'content' => 'admin.setting.about_me'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }
}
