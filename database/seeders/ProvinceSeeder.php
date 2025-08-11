<?php

use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{

    public function run()
    {
        $data = DB::connection('backup')
            ->table('provinces')
            ->get();

        foreach ($data as $d) {
            App\Models\Province::create([
                'id'        => $d->id,
                'name'      => $d->name,
                'latitude'  => $d->latitude,
                'longitude' => $d->longitude
            ]);
        }
    }
}
