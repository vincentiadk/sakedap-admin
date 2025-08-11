<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{

    public function run()
    {
        $data = DB::connection('backup')
            ->table('users')
            ->get();

        $this->command->info('Migrating ' .  DB::connection('backup')->table('users')->count() . " users");
        foreach ($data as $userOld) {
            $user_publisher = DB::connection('backup')->table('publishers')->where('user_id', $userOld->id)->first();
            $userable_type = $user_publisher ? "publishers" : "admins";
            $userable_id = $user_publisher ? $user_publisher->id : $userOld->id;

            if ($userOld->username != "") {
                $count_username = App\Models\User::where(DB::raw('substring(username,1,' . strlen($userOld->username) . ')'), '=', $userOld->username)->count();
                $username = ($count_username > 0 ? $userOld->username . "_" . $count_username : $userOld->username);
            } else {
                $username = $userOld->username;
            }

            if ($userable_type == "admins") {
                $userable_id = App\Models\Admin::insertGetId([
                    'fullname'      => $userOld->fullname == "" ? $userOld->username : $userOld->fullname,
                    'email'         => $userOld->email,
                    'created_at'    => $userOld->created_at,
                    'updated_at'    => $userOld->updated_at,
                ]);
            }

            App\Models\User::insert([
                'id'            => $userOld->id,
                'userable_type' => $userable_type,
                'userable_id'   => $userable_id,
                'username'      => $username,
                'password'      => $userOld->password,
                'role_id'       => $userable_type == "admins" ? 1 : 2,
                'lang'          => $userOld->lang,
                'last_login'    => $userOld->last_login,
                'created_at'    => $userOld->created_at,
                'updated_at'    => $userOld->updated_at,
            ]);
        }
        $this->command->info('Migrating users finished. Total users = ' . App\Models\User::count());

        $admin = App\Models\Admin::create([
            'fullname' => 'Perpustakaan Nasional',
            'email'    => 'info@perpusnas.go.id'
        ]);

        App\Models\User::create([
            'userable_type' => 'admins',
            'userable_id'   => $admin->id,
            'username'      => 'perpusnas2019',
            'password'      => Hash::make('perpusnas@2019'),
            'role_id'       => 1,
            'lang'          => 'id',
            'last_login'    => date('Y-m-d H:i:s')
        ]);
    }
}
