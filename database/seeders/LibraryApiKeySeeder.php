<?php

use App\Models\Library;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LibraryApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = DB::connection('mysql')
            ->table('libraries')
            ->get();

        if (sizeof($data) > 0) {
            foreach ($data as $d) {
                Library::where('id', $d->id)->update(['api_key' => Str::random(64)]);
            }
        }
    }
}
