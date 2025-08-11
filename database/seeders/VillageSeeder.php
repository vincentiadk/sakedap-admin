<?php

use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{

    public function run()
    {
        $data = DB::connection('backup')
            ->table('villages')
            ->get();

        foreach ($data as $d) {
            App\Models\Village::create([
                'id'          => $d->id,
                'district_id' => $d->district_id,
                'name'        => $d->name
            ]);
        }
    }
}
