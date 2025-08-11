<?php

use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{

    public function run()
    {
        $this->command->info('Migrating ' . DB::connection('backup')->table('publishers')->count() . " publisher from publishers table");
        $publishers =  DB::connection('backup')->table('publishers')->get();
        foreach ($publishers as $pubOld) {
            $user = DB::connection('backup')->table('users')->where('id', $pubOld->user_id)->first();
            $email = "";
            if (App\Models\Publisher::where('email', '=', $user->email)->count() < 1) {
                if ($user) {
                    $email = $user->email;
                }
            }

            App\Models\Publisher::insert([
                'id'                => $pubOld->id,
                'province_id'       => $pubOld->province_id,
                'city_id'           => $pubOld->city_id,
                'district_id'       => $pubOld->district_id,
                'village_id'        => $pubOld->village_id,
                'publisher_code'    => App\Helper\GeneralHelper::publisherCode($pubOld->created_at),
                'name'              => $pubOld->name,
                'contact'           => $pubOld->contact,
                'email'             => $email,
                'phone'             => $pubOld->phone,
                'fax'               => $pubOld->fax,
                'website'           => $pubOld->website,
                'address'           => $pubOld->address,
                'postal_code'       => $pubOld->postcode,
                'type'              => $pubOld->category,
                'code_system'       => $pubOld->isbn_id,
                'system_type'       => ($pubOld->isbn_id != "") ? "isbn" : null,
                'birth_certificate' => $pubOld->akta,
                'statement_letter'  => $pubOld->pernyataan,
                'status'            => 2,
                'created_at'        => $pubOld->created_at,
                'updated_at'        => $pubOld->updated_at,
            ]);
        }
        $this->command->info('Migrating publishers table finished. Total publishers validation = ' . App\Models\Publisher::count());

        $this->command->info('Migrating ' . DB::connection('backup')->table('publisher_reqs')->where('validation', null)->count() . " publisher from publisher_reqs table");
        $publisherReqs = DB::connection('backup')->table('publisher_reqs')->where('validation', null)->orWhere('validation', 'P')->get();
        foreach ($publisherReqs as $pubReqOld) {
            $email = "";
            if ($pubReqOld->email != "") {
                if (App\Models\Publisher::where('email', '=', $pubReqOld->email)->count() < 1) {
                    $email = $pubReqOld->email;
                }
            }

            $code = DateTime::createFromFormat('Y-m-d H:i:s', $pubReqOld->created_at)->format('Ymd');
            $code_max = App\Models\Publisher::select(DB::raw('MAX(publisher_code) as unique_code'))->where('publisher_code', 'LIKE', $code . "%")->first();
            if ($code_max) {
                $int = (int)substr($code_max->unique_code, 6, 6);
                $int++;
            } else {
                $int = 1;
            }

            $code_pub = $code . sprintf('%06s', $int);

            if ($pubReqOld->validation == null || $pubReqOld->validation == "") {
                App\Models\Publisher::insert([
                    'province_id'       => $pubReqOld->province_id,
                    'city_id'           => $pubReqOld->city_id,
                    'district_id'       => $pubReqOld->district_id,
                    'village_id'        => $pubReqOld->village_id,
                    'publisher_code'    => App\Helper\GeneralHelper::publisherCode($pubReqOld->created_at),
                    'name'              => $pubReqOld->publisher_name,
                    'contact'           => $pubReqOld->contact,
                    'email'             => $email,
                    'phone'             => $pubReqOld->phone,
                    'fax'               => $pubReqOld->fax,
                    'website'           => $pubReqOld->website,
                    'address'           => $pubReqOld->address1,
                    'postal_code'       => $pubReqOld->postcode,
                    'type'              => $pubReqOld->cat_pub,
                    'code_system'       => null,
                    'system_type'       => null,
                    'birth_certificate' => $pubReqOld->akta,
                    'statement_letter'  => $pubReqOld->pernyataan,
                    'status'            => 1,
                    'created_at'        => $pubReqOld->created_at,
                    'updated_at'        => $pubReqOld->updated_at,
                ]);
            }
        }
        $this->command->info('Migrating publisher_reqs table finished. Total publishers (all status) = ' . App\Models\Publisher::count());
    }
}
