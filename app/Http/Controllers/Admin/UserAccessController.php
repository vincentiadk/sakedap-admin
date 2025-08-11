<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Role;
use App\Models\UserAccess;
use Illuminate\Http\Request;
use App\Models\UserCertainAccess;
use App\Http\Controllers\Controller;

class UserAccessController extends Controller
{
    public function index($id)
    {
        $data = [
            'title'   => 'Pengaturan Hak Akses',
            'role'    => Role::find($id),
            'menu'    => Menu::where('parent_id', 0)->oldest('name')->get(),
            'content' => 'admin.setting.user_access'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function checkboxPermission(Request $request)
    {
        if ($request->type == 'menu') {
            $access = UserAccess::where('role_id', $request->role_id)
                ->where('menu_id', $request->menu_id);

            if ($access->count() > 0) {
                $access->delete();
            } else {
                UserAccess::create([
                    'role_id' => $request->role_id,
                    'menu_id' => $request->menu_id
                ]);
            }
        } else {
            $access = UserCertainAccess::where('role_id', $request->role_id)
                ->where('access', $request->access);

            if ($access->count() > 0) {
                $access->delete();
            } else {
                UserCertainAccess::create([
                    'role_id' => $request->role_id,
                    'access'  => $request->access
                ]);
            }
        }

        return response()->json(200);
    }
}
