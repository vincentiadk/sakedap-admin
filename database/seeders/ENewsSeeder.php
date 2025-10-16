<?php

namespace Database\Seeders;

use App\Helpers\QueryAPI;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ENewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $status = ['PUBLISH', 'HIDE'];
        $flag = ['BERITA', 'EVENT', 'TUTORIAL'];

        for ($i = 1; $i <= 100; $i++) {
            $categoryId = QueryAPI::get("select * from e_news_kategori order by dbms_random.value", true);
            $title = $faker->sentence;

            QueryAPI::create('e_news', [
                'title' => $title,
                'slug' => Str::slug($title, '-'),
                'content' => $faker->text(),
                'status' => $status[rand(0, 1)],
                'lampiran_link' => $faker->url(),
                'kategori_id' => $categoryId->ID ?? null,
                'flag' => $flag[rand(0, 2)],
            ], false);
        }
    }
}
