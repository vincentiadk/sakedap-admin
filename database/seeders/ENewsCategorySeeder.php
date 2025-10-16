<?php

namespace Database\Seeders;

use App\Helpers\QueryAPI;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ENewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $pages = [1, null];

        for ($i = 1; $i <= 20; $i++) {
            $parentId = QueryAPI::get("select * from e_news_kategori order by dbms_random.value", true);

            QueryAPI::create('e_news_kategori', [
                'name' => $faker->word,
                'pages' => $pages[rand(0, 1)],
                'parent_id' => $parentId->ID ?? null,
                'createby' => 'Seeder',
                'createdate' => date('Y-m-d H:i:s'),
                'createterminal' => gethostbyname(gethostname()),
                'updateby' => 'Seeder',
                'updatedate' => date('Y-m-d H:i:s'),
                'updateterminal' => gethostbyname(gethostname()),
            ], false);
        }
    }
}
