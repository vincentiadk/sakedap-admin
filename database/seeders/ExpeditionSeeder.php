<?php

use Illuminate\Database\Seeder;

class ExpeditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Models\Expedition::create([
            'id'   => 1,
            'name' => 'DATANG LANGSUNG'
        ]);

        App\Models\Expedition::create([
            'id'   => 2,
            'name' => 'POS INDONESIA'
        ]);

        App\Models\Expedition::create([
            'id'   => 3,
            'name' => 'JNE'
        ]);

        App\Models\Expedition::create([
            'id'   => 4,
            'name' => 'J&T'
        ]);

        App\Models\Expedition::create([
            'id'   => 5,
            'name' => 'ANTERAJA'
        ]);

        App\Models\Expedition::create([
            'id'   => 6,
            'name' => 'SICEPAT'
        ]);

        App\Models\Expedition::create([
            'id'   => 7,
            'name' => 'LAINNYA'
        ]);
    }
}
