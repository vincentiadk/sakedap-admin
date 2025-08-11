<?php

use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{

    public function run()
    {
        $data = DB::connection('backup')
            ->table('news')
            ->get();

        foreach ($data as $d) {
            App\Models\News::create([
                'image'   => 'public/main/default.png',
                'title'   => $d->title,
                'slug'    => Str::slug($d->title, '-'),
                'content' => 'migrasi v2',
                'status'  => 1
            ]);
        }
    }
}
