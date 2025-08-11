<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provinces')->where('id', '11')->update(['code' => 'D.11']);
    }
}
