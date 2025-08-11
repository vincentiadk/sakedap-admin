<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DepositHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('deposit_head')->delete();
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('deposit_head')->insert([
            [
                'id' => 1,
                'shape' => 'Buku Elektronik',
                'code' => 'RDB',
                'category' => 'KRD',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 2,
                'shape' => 'Partitur Musik',
                'code' => 'XXX',
                'category' => 'KRD',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 3,
                'shape' => 'Bahan Kartografi Elektronik',
                'code' => 'RDK',
                'category' => 'KRD',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 4,
                'shape' => 'Terbitan Berkala / Serial Elektronik',
                'code' => 'RDS',
                'category' => 'KRD',
                'is_serial' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 5,
                'shape' => 'Musik Digital',
                'code' => 'RDM',
                'category' => 'KRD',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 6,
                'shape' => 'Film Digital',
                'code' => 'RDF',
                'category' => 'KRD',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 7,
                'shape' => 'Buku',
                'code' => 'CB',
                'category' => 'KC',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 8,
                'shape' => 'Terbitan Pemerintah Non Komersial',
                'code' => 'CB[G]',
                'category' => 'KC',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 9,
                'shape' => 'Terbitan Internasional',
                'code' => 'CB[TI]',
                'category' => 'KC',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 10,
                'shape' => 'Buletin, Jurnal, Majalah',
                'code' => 'CM',
                'category' => 'KC',
                'is_serial' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 11,
                'shape' => 'Surat Kabar, Tabloid',
                'code' => 'CSK',
                'category' => 'KC',
                'is_serial' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 12,
                'shape' => 'Bahan Kartografi',
                'code' => 'CK',
                'category' => 'KC',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 13,
                'shape' => 'Piringan Hitam, Kaset Audio, Compact Disc, Open Reel, Digital Audio Tape',
                'code' => 'RS',
                'category' => 'KRA',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 14,
                'shape' => 'Kaset Video, DVD, VCD, Blu-ray Disc, Laser Disc',
                'code' => 'RF',
                'category' => 'KRA',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'id' => 15,
                'shape' => 'Microfilm, Microfis',
                'code' => 'RM',
                'category' => 'KRA',
                'is_serial' => '0',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}
