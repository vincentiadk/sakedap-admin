<?php

use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{

    public function run()
    {
        $data = DB::connection('backup')
            ->table('cities')
            ->get();

        foreach ($data as $d) {
            App\Models\City::create([
                'id'          => $d->id,
                'province_id' => $d->province_id,
                'name'        => $d->name,
                'latitude'    => $d->latitude,
                'longitude'   => $d->longitude
            ]);
        }
    }
}
