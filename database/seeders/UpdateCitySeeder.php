<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->where('id', '1101')->update(['code' => 'D.11.01']);
    }
}
