<?php

use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{

    public function run()
    {
        $data = DB::connection('backup')
            ->table('districts')
            ->get();

        foreach ($data as $d) {
            App\Models\District::create([
                'id'      => $d->id,
                'city_id' => $d->city_id,
                'name'    => $d->name
            ]);
        }
    }
}
