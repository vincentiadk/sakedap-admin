<?php

use Illuminate\Database\Seeder;

class UserAccessSeeder extends Seeder {

    public function run()
    {
        

        \DB::table('user_accesses')->delete();
        
        \DB::table('user_accesses')->insert(array (
            0 => 
            array (
                'id' => 101,
                'role_id' => 1,
                'menu_id' => 62,
                'created_at' => '2021-03-10 15:52:47',
                'updated_at' => '2021-03-10 15:52:47',
            ),
            1 => 
            array (
                'id' => 102,
                'role_id' => 1,
                'menu_id' => 36,
                'created_at' => '2021-03-10 15:52:47',
                'updated_at' => '2021-03-10 15:52:47',
            ),
            2 => 
            array (
                'id' => 103,
                'role_id' => 1,
                'menu_id' => 37,
                'created_at' => '2021-03-10 15:52:48',
                'updated_at' => '2021-03-10 15:52:48',
            ),
            3 => 
            array (
                'id' => 104,
                'role_id' => 1,
                'menu_id' => 38,
                'created_at' => '2021-03-10 15:52:48',
                'updated_at' => '2021-03-10 15:52:48',
            ),
            4 => 
            array (
                'id' => 105,
                'role_id' => 1,
                'menu_id' => 58,
                'created_at' => '2021-03-10 15:52:49',
                'updated_at' => '2021-03-10 15:52:49',
            ),
            5 => 
            array (
                'id' => 106,
                'role_id' => 1,
                'menu_id' => 11,
                'created_at' => '2021-03-10 15:52:51',
                'updated_at' => '2021-03-10 15:52:51',
            ),
            6 => 
            array (
                'id' => 107,
                'role_id' => 1,
                'menu_id' => 12,
                'created_at' => '2021-03-10 15:52:51',
                'updated_at' => '2021-03-10 15:52:51',
            ),
            7 => 
            array (
                'id' => 108,
                'role_id' => 1,
                'menu_id' => 13,
                'created_at' => '2021-03-10 15:52:52',
                'updated_at' => '2021-03-10 15:52:52',
            ),
            8 => 
            array (
                'id' => 111,
                'role_id' => 1,
                'menu_id' => 24,
                'created_at' => '2021-03-10 15:55:07',
                'updated_at' => '2021-03-10 15:55:07',
            ),
            9 => 
            array (
                'id' => 112,
                'role_id' => 1,
                'menu_id' => 63,
                'created_at' => '2021-03-10 15:55:15',
                'updated_at' => '2021-03-10 15:55:15',
            ),
            10 => 
            array (
                'id' => 113,
                'role_id' => 1,
                'menu_id' => 39,
                'created_at' => '2021-03-10 15:55:16',
                'updated_at' => '2021-03-10 15:55:16',
            ),
            11 => 
            array (
                'id' => 114,
                'role_id' => 1,
                'menu_id' => 40,
                'created_at' => '2021-03-10 15:55:16',
                'updated_at' => '2021-03-10 15:55:16',
            ),
            12 => 
            array (
                'id' => 115,
                'role_id' => 1,
                'menu_id' => 41,
                'created_at' => '2021-03-10 15:55:17',
                'updated_at' => '2021-03-10 15:55:17',
            ),
            13 => 
            array (
                'id' => 116,
                'role_id' => 1,
                'menu_id' => 68,
                'created_at' => '2021-03-10 15:55:23',
                'updated_at' => '2021-03-10 15:55:23',
            ),
            14 => 
            array (
                'id' => 117,
                'role_id' => 1,
                'menu_id' => 42,
                'created_at' => '2021-03-10 15:55:24',
                'updated_at' => '2021-03-10 15:55:24',
            ),
            15 => 
            array (
                'id' => 118,
                'role_id' => 1,
                'menu_id' => 46,
                'created_at' => '2021-03-10 15:55:24',
                'updated_at' => '2021-03-10 15:55:24',
            ),
            16 => 
            array (
                'id' => 119,
                'role_id' => 1,
                'menu_id' => 45,
                'created_at' => '2021-03-10 15:55:25',
                'updated_at' => '2021-03-10 15:55:25',
            ),
            17 => 
            array (
                'id' => 120,
                'role_id' => 1,
                'menu_id' => 57,
                'created_at' => '2021-03-10 15:55:26',
                'updated_at' => '2021-03-10 15:55:26',
            ),
            18 => 
            array (
                'id' => 121,
                'role_id' => 1,
                'menu_id' => 3,
                'created_at' => '2021-03-10 15:55:27',
                'updated_at' => '2021-03-10 15:55:27',
            ),
            19 => 
            array (
                'id' => 122,
                'role_id' => 1,
                'menu_id' => 8,
                'created_at' => '2021-03-10 15:55:28',
                'updated_at' => '2021-03-10 15:55:28',
            ),
            20 => 
            array (
                'id' => 123,
                'role_id' => 1,
                'menu_id' => 9,
                'created_at' => '2021-03-10 15:55:28',
                'updated_at' => '2021-03-10 15:55:28',
            ),
            21 => 
            array (
                'id' => 124,
                'role_id' => 1,
                'menu_id' => 2,
                'created_at' => '2021-03-10 15:55:29',
                'updated_at' => '2021-03-10 15:55:29',
            ),
            22 => 
            array (
                'id' => 125,
                'role_id' => 1,
                'menu_id' => 7,
                'created_at' => '2021-03-10 15:55:29',
                'updated_at' => '2021-03-10 15:55:29',
            ),
            23 => 
            array (
                'id' => 126,
                'role_id' => 1,
                'menu_id' => 5,
                'created_at' => '2021-03-10 15:55:30',
                'updated_at' => '2021-03-10 15:55:30',
            ),
            24 => 
            array (
                'id' => 127,
                'role_id' => 1,
                'menu_id' => 69,
                'created_at' => '2021-03-10 15:55:30',
                'updated_at' => '2021-03-10 15:55:30',
            ),
            25 => 
            array (
                'id' => 128,
                'role_id' => 1,
                'menu_id' => 6,
                'created_at' => '2021-03-10 15:55:31',
                'updated_at' => '2021-03-10 15:55:31',
            ),
            26 => 
            array (
                'id' => 129,
                'role_id' => 1,
                'menu_id' => 4,
                'created_at' => '2021-03-10 15:55:32',
                'updated_at' => '2021-03-10 15:55:32',
            ),
            27 => 
            array (
                'id' => 130,
                'role_id' => 1,
                'menu_id' => 59,
                'created_at' => '2021-03-10 15:55:34',
                'updated_at' => '2021-03-10 15:55:34',
            ),
            28 => 
            array (
                'id' => 131,
                'role_id' => 1,
                'menu_id' => 27,
                'created_at' => '2021-03-10 15:55:35',
                'updated_at' => '2021-03-10 15:55:35',
            ),
            29 => 
            array (
                'id' => 132,
                'role_id' => 1,
                'menu_id' => 28,
                'created_at' => '2021-03-10 15:55:35',
                'updated_at' => '2021-03-10 15:55:35',
            ),
            30 => 
            array (
                'id' => 133,
                'role_id' => 1,
                'menu_id' => 29,
                'created_at' => '2021-03-10 15:55:36',
                'updated_at' => '2021-03-10 15:55:36',
            ),
            31 => 
            array (
                'id' => 134,
                'role_id' => 1,
                'menu_id' => 64,
                'created_at' => '2021-03-10 15:55:45',
                'updated_at' => '2021-03-10 15:55:45',
            ),
            32 => 
            array (
                'id' => 135,
                'role_id' => 1,
                'menu_id' => 55,
                'created_at' => '2021-03-10 15:55:45',
                'updated_at' => '2021-03-10 15:55:45',
            ),
            33 => 
            array (
                'id' => 136,
                'role_id' => 1,
                'menu_id' => 56,
                'created_at' => '2021-03-10 15:55:46',
                'updated_at' => '2021-03-10 15:55:46',
            ),
            34 => 
            array (
                'id' => 137,
                'role_id' => 1,
                'menu_id' => 49,
                'created_at' => '2021-03-10 15:55:48',
                'updated_at' => '2021-03-10 15:55:48',
            ),
            35 => 
            array (
                'id' => 138,
                'role_id' => 1,
                'menu_id' => 48,
                'created_at' => '2021-03-10 15:55:49',
                'updated_at' => '2021-03-10 15:55:49',
            ),
            36 => 
            array (
                'id' => 139,
                'role_id' => 1,
                'menu_id' => 50,
                'created_at' => '2021-03-10 15:55:49',
                'updated_at' => '2021-03-10 15:55:49',
            ),
            37 => 
            array (
                'id' => 140,
                'role_id' => 1,
                'menu_id' => 65,
                'created_at' => '2021-03-10 15:55:49',
                'updated_at' => '2021-03-10 15:55:49',
            ),
            38 => 
            array (
                'id' => 141,
                'role_id' => 1,
                'menu_id' => 51,
                'created_at' => '2021-03-10 15:55:50',
                'updated_at' => '2021-03-10 15:55:50',
            ),
            39 => 
            array (
                'id' => 142,
                'role_id' => 1,
                'menu_id' => 47,
                'created_at' => '2021-03-10 15:55:50',
                'updated_at' => '2021-03-10 15:55:50',
            ),
            40 => 
            array (
                'id' => 143,
                'role_id' => 1,
                'menu_id' => 60,
                'created_at' => '2021-03-10 15:55:53',
                'updated_at' => '2021-03-10 15:55:53',
            ),
            41 => 
            array (
                'id' => 144,
                'role_id' => 1,
                'menu_id' => 30,
                'created_at' => '2021-03-10 15:55:53',
                'updated_at' => '2021-03-10 15:55:53',
            ),
            42 => 
            array (
                'id' => 145,
                'role_id' => 1,
                'menu_id' => 31,
                'created_at' => '2021-03-10 15:55:54',
                'updated_at' => '2021-03-10 15:55:54',
            ),
            43 => 
            array (
                'id' => 146,
                'role_id' => 1,
                'menu_id' => 32,
                'created_at' => '2021-03-10 15:55:54',
                'updated_at' => '2021-03-10 15:55:54',
            ),
            44 => 
            array (
                'id' => 147,
                'role_id' => 1,
                'menu_id' => 109,
                'created_at' => '2021-03-10 15:55:55',
                'updated_at' => '2021-03-10 15:55:55',
            ),
            45 => 
            array (
                'id' => 149,
                'role_id' => 1,
                'menu_id' => 61,
                'created_at' => '2021-03-10 15:56:10',
                'updated_at' => '2021-03-10 15:56:10',
            ),
            46 => 
            array (
                'id' => 150,
                'role_id' => 1,
                'menu_id' => 33,
                'created_at' => '2021-03-10 15:56:11',
                'updated_at' => '2021-03-10 15:56:11',
            ),
            47 => 
            array (
                'id' => 151,
                'role_id' => 1,
                'menu_id' => 34,
                'created_at' => '2021-03-10 15:56:11',
                'updated_at' => '2021-03-10 15:56:11',
            ),
            48 => 
            array (
                'id' => 152,
                'role_id' => 1,
                'menu_id' => 35,
                'created_at' => '2021-03-10 15:56:11',
                'updated_at' => '2021-03-10 15:56:11',
            ),
            49 => 
            array (
                'id' => 153,
                'role_id' => 1,
                'menu_id' => 23,
                'created_at' => '2021-03-10 15:56:12',
                'updated_at' => '2021-03-10 15:56:12',
            ),
            50 => 
            array (
                'id' => 154,
                'role_id' => 1,
                'menu_id' => 71,
                'created_at' => '2021-03-10 15:56:14',
                'updated_at' => '2021-03-10 15:56:14',
            ),
            51 => 
            array (
                'id' => 155,
                'role_id' => 1,
                'menu_id' => 72,
                'created_at' => '2021-03-10 15:56:15',
                'updated_at' => '2021-03-10 15:56:15',
            ),
            52 => 
            array (
                'id' => 156,
                'role_id' => 1,
                'menu_id' => 75,
                'created_at' => '2021-03-10 15:56:15',
                'updated_at' => '2021-03-10 15:56:15',
            ),
            53 => 
            array (
                'id' => 157,
                'role_id' => 1,
                'menu_id' => 76,
                'created_at' => '2021-03-10 15:56:15',
                'updated_at' => '2021-03-10 15:56:15',
            ),
            54 => 
            array (
                'id' => 158,
                'role_id' => 1,
                'menu_id' => 74,
                'created_at' => '2021-03-10 15:56:17',
                'updated_at' => '2021-03-10 15:56:17',
            ),
            55 => 
            array (
                'id' => 159,
                'role_id' => 1,
                'menu_id' => 78,
                'created_at' => '2021-03-10 15:56:17',
                'updated_at' => '2021-03-10 15:56:17',
            ),
            56 => 
            array (
                'id' => 160,
                'role_id' => 1,
                'menu_id' => 73,
                'created_at' => '2021-03-10 15:56:18',
                'updated_at' => '2021-03-10 15:56:18',
            ),
            57 => 
            array (
                'id' => 161,
                'role_id' => 1,
                'menu_id' => 77,
                'created_at' => '2021-03-10 15:56:19',
                'updated_at' => '2021-03-10 15:56:19',
            ),
            58 => 
            array (
                'id' => 162,
                'role_id' => 2,
                'menu_id' => 117,
                'created_at' => '2021-03-10 15:56:54',
                'updated_at' => '2021-03-10 15:56:54',
            ),
            59 => 
            array (
                'id' => 163,
                'role_id' => 2,
                'menu_id' => 108,
                'created_at' => '2021-03-10 15:56:56',
                'updated_at' => '2021-03-10 15:56:56',
            ),
            60 => 
            array (
                'id' => 164,
                'role_id' => 2,
                'menu_id' => 103,
                'created_at' => '2021-03-10 15:56:57',
                'updated_at' => '2021-03-10 15:56:57',
            ),
            61 => 
            array (
                'id' => 165,
                'role_id' => 2,
                'menu_id' => 96,
                'created_at' => '2021-03-10 15:57:01',
                'updated_at' => '2021-03-10 15:57:01',
            ),
            62 => 
            array (
                'id' => 166,
                'role_id' => 2,
                'menu_id' => 97,
                'created_at' => '2021-03-10 15:57:03',
                'updated_at' => '2021-03-10 15:57:03',
            ),
            63 => 
            array (
                'id' => 167,
                'role_id' => 2,
                'menu_id' => 98,
                'created_at' => '2021-03-10 15:57:03',
                'updated_at' => '2021-03-10 15:57:03',
            ),
            64 => 
            array (
                'id' => 168,
                'role_id' => 2,
                'menu_id' => 83,
                'created_at' => '2021-03-10 15:57:06',
                'updated_at' => '2021-03-10 15:57:06',
            ),
            65 => 
            array (
                'id' => 169,
                'role_id' => 2,
                'menu_id' => 84,
                'created_at' => '2021-03-10 15:57:06',
                'updated_at' => '2021-03-10 15:57:06',
            ),
            66 => 
            array (
                'id' => 170,
                'role_id' => 2,
                'menu_id' => 85,
                'created_at' => '2021-03-10 15:57:06',
                'updated_at' => '2021-03-10 15:57:06',
            ),
            67 => 
            array (
                'id' => 171,
                'role_id' => 2,
                'menu_id' => 100,
                'created_at' => '2021-03-10 15:57:07',
                'updated_at' => '2021-03-10 15:57:07',
            ),
            68 => 
            array (
                'id' => 172,
                'role_id' => 2,
                'menu_id' => 101,
                'created_at' => '2021-03-10 15:57:08',
                'updated_at' => '2021-03-10 15:57:08',
            ),
            69 => 
            array (
                'id' => 173,
                'role_id' => 2,
                'menu_id' => 102,
                'created_at' => '2021-03-10 15:57:09',
                'updated_at' => '2021-03-10 15:57:09',
            ),
            70 => 
            array (
                'id' => 174,
                'role_id' => 2,
                'menu_id' => 87,
                'created_at' => '2021-03-10 15:57:11',
                'updated_at' => '2021-03-10 15:57:11',
            ),
            71 => 
            array (
                'id' => 175,
                'role_id' => 2,
                'menu_id' => 88,
                'created_at' => '2021-03-10 15:57:12',
                'updated_at' => '2021-03-10 15:57:12',
            ),
            72 => 
            array (
                'id' => 178,
                'role_id' => 2,
                'menu_id' => 91,
                'created_at' => '2021-03-10 15:57:33',
                'updated_at' => '2021-03-10 15:57:33',
            ),
            73 => 
            array (
                'id' => 179,
                'role_id' => 2,
                'menu_id' => 89,
                'created_at' => '2021-03-10 15:57:34',
                'updated_at' => '2021-03-10 15:57:34',
            ),
            74 => 
            array (
                'id' => 180,
                'role_id' => 2,
                'menu_id' => 92,
                'created_at' => '2021-03-10 15:57:35',
                'updated_at' => '2021-03-10 15:57:35',
            ),
            75 => 
            array (
                'id' => 181,
                'role_id' => 2,
                'menu_id' => 93,
                'created_at' => '2021-03-10 15:57:36',
                'updated_at' => '2021-03-10 15:57:36',
            ),
            76 => 
            array (
                'id' => 185,
                'role_id' => 2,
                'menu_id' => 105,
                'created_at' => '2021-03-10 15:57:44',
                'updated_at' => '2021-03-10 15:57:44',
            ),
            77 => 
            array (
                'id' => 186,
                'role_id' => 2,
                'menu_id' => 106,
                'created_at' => '2021-03-10 15:57:45',
                'updated_at' => '2021-03-10 15:57:45',
            ),
            78 => 
            array (
                'id' => 190,
                'role_id' => 2,
                'menu_id' => 80,
                'created_at' => '2021-03-10 16:00:57',
                'updated_at' => '2021-03-10 16:00:57',
            ),
            79 => 
            array (
                'id' => 191,
                'role_id' => 2,
                'menu_id' => 81,
                'created_at' => '2021-03-10 16:00:57',
                'updated_at' => '2021-03-10 16:00:57',
            ),
            80 => 
            array (
                'id' => 192,
                'role_id' => 2,
                'menu_id' => 15,
                'created_at' => '2021-03-10 16:01:04',
                'updated_at' => '2021-03-10 16:01:04',
            ),
            81 => 
            array (
                'id' => 193,
                'role_id' => 2,
                'menu_id' => 16,
                'created_at' => '2021-03-10 16:01:04',
                'updated_at' => '2021-03-10 16:01:04',
            ),
            82 => 
            array (
                'id' => 194,
                'role_id' => 2,
                'menu_id' => 43,
                'created_at' => '2021-03-10 16:01:04',
                'updated_at' => '2021-03-10 16:01:04',
            ),
        ));
    }

}
