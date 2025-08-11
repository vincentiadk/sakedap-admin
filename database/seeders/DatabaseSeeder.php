<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(DistrictSeeder::class);
        $this->call(VillageSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(PublisherSeeder::class);
        $this->call(MasterSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(UserAccessSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(CollectionSeeder::class);
        $this->call(CollectionMediaSeeder::class);
        $this->call(DepositHeadSeeder::class);
    }
}
