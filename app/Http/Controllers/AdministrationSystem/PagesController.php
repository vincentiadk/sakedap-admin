<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
    public function index()
    {
        $category = QueryAPI::get("
            select
                id,
                parent_id,
                name,
                pages,
                content,
                level,
                rpad(' ', (level - 1) * 2) || name as tree_view,
                ltrim(sys_connect_by_path(name, ' > '), ' > ') as tree_path
            from
                e_news_kategori
            where
                pages = 1
            start with
                parent_id is null
            connect by prior
                id = parent_id
            order siblings by
                name
        ");

        return view('layouts.index', [
            'data' => [
                'category' => $category,
                'content' => 'administration-system.pages',
                'plugins' => [
                    'select2',
                    'dragula',
                ]
            ]
        ]);
    }

    public function submitted(Request $request)
    {
        try {
            if ($request->category) {
                foreach ($request->category as $c) {
                    $categoryContent = isset($request->category_content[$c]) ? $request->category_content[$c] : [];

                    if ($categoryContent) {
                        QueryAPI::update('e_news_kategori', $c, [
                            'content' => implode(',', $categoryContent),
                            'updateby' => session('username'),
                            'updatedate' => date('Y-m-d H:i:s'),
                            'updateterminal' => $request->ip(),
                        ], false);
                    }
                }
            }

            return redirect('administration-system/pages')->with('success', 'Halaman berhasil disimpan');
        } catch (\Exception $e) {
            return redirect('administration-system/pages')->with('error', $e->getMessage());
        }
    }
}
