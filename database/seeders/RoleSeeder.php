<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{

    public function run()
    {
        App\Models\Role::create([
            'id'   => 1,
            'name' => 'Super Admin'
        ]);

        App\Models\Role::create([
            'id'   => 2,
            'name' => 'Publisher'
        ]);
    }
}
