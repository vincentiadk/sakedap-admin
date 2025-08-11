<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class TemplateEmailController extends Controller
{
    public function activation()
    {
        $data = [
            'title'   => 'Template Email Aktivasi',
            'data'    => Setting::where('slug', 'template-email-activation')->first(),
            'content' => 'admin.template_email.activation'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function changePassword()
    {
        $data = [
            'title'   => 'Template Email Ganti Password',
            'data'    => Setting::where('slug', 'template-email-change-password')->first(),
            'content' => 'admin.template_email.change_password'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function collectionProblem()
    {
        $data = [
            'title'   => 'Template Email Koleksi Bermasalah',
            'data'    => Setting::where('slug', 'template-email-collection-problem')->first(),
            'content' => 'admin.template_email.collection_problem'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function collectionSubmitted()
    {
        $data = [
            'title'   => 'Template Email Koleksi Diserahkan',
            'data'    => Setting::where('slug', 'template-email-collection-submitted')->first(),
            'content' => 'admin.template_email.collection_submitted'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function collectionSuccess()
    {
        $data = [
            'title'   => 'Template Email Koleksi Tervalidasi',
            'data'    => Setting::where('slug', 'template-email-collection-success')->first(),
            'content' => 'admin.template_email.collection_success'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function publisherRejected()
    {
        $data = [
            'title'   => 'Template Email Penerbit Ditolak',
            'data'    => Setting::where('slug', 'template-email-publisher-rejected')->first(),
            'content' => 'admin.template_email.publisher_rejected'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function publisherSubmission()
    {
        $data = [
            'title'   => 'Template Email Penerbit Pengajuan',
            'data'    => Setting::where('slug', 'template-email-publisher-submission')->first(),
            'content' => 'admin.template_email.publisher_submission'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function publisherSuccess()
    {
        $data = [
            'title'   => 'Template Email Penerbit Tervalidasi',
            'data'    => Setting::where('slug', 'template-email-publisher-success')->first(),
            'content' => 'admin.template_email.publisher_success'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function collectionBulk()
    {
        $data = [
            'title'   => 'Template Email Collection Bulk',
            'data'    => Setting::where('slug', 'template-email-collection-submitted-bulk')->first(),
            'content' => 'admin.template_email.collection_bulk'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function resetPassword()
    {
        $data = [
            'title'   => 'Template Email Reset Password',
            'data'    => Setting::where('slug', 'template-link-password-reset')->first(),
            'content' => 'admin.template_email.reset_password'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function header()
    {
        $data = [
            'title'   => 'Template Email Header',
            'data'    => Setting::where('slug', 'template-email-header')->first(),
            'content' => 'admin.template_email.header'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function footer()
    {
        $data = [
            'title'   => 'Template Email Footer',
            'data'    => Setting::where('slug', 'template-email-footer')->first(),
            'content' => 'admin.template_email.footer'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function deliveryReceipt()
    {
        $data = [
            'title'   => 'Template Bukti Penerimaan',
            'data'    => Setting::where('slug', 'template-email-delivery-receipt')->first(),
            'content' => 'admin.template_email.delivery_receipt'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function createUpdate(Request $request)
    {
        $data = Setting::where('slug', $request->slug)->first();

        if ($request->slug == 'template-email-header' || $request->slug == 'template-email-footer') {
            $file = $data ? $data->content : null;
            if (Storage::disk('local')->exists($file)) {
                Storage::disk('local')->delete($file);
            }

            $content = Storage::disk('local')->put('public/director', $request->file('content'));
        } else {
            $content = $request->content;
        }

        Setting::updateOrCreate([
            'slug' => $request->slug
        ], [
            'content' => $content
        ]);

        return redirect()->back()->with(['success' => 'Telah di update!']);
    }
}
