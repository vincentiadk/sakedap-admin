<?php

use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = DB::connection('mysql')
            ->table('organizations')
            ->insert([
                    [
                        'name'  => 'IKAPI'
                    ],
                    [
                        'name'  => 'ASIRI'
                    ],
                ]);
    }
}
