<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class OrangSeeder extends Seeder
{
    public function run()
    {
        // $data = [
        //     [
        //         'nama' => 'Rafli Al Rasyid',
        //         'alamat' => 'Jl. Merdeka No. 10',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ],
        //     [
        //         'nama' => 'Siti Nurhalimah',
        //         'alamat' => 'Jl. Pahlawan No. 5',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ],
        //     [
        //         'nama' => 'Rafli Al Rasyid',
        //         'alamat' => 'Karawang',
        //         'created_at' => Time::now(),
        //         'updated_at' => Time::now()
        //     ]
        // ];
        $faker = \Faker\Factory::create('id_ID');
        for ($i = 0; $i < 100; $i++) {
            # code...

            $data = [
                'nama' => $faker->name(),
                'alamat' => $faker->address(),
                'created_at' => Time::createFromTimestamp($faker->unixTime()),
                'updated_at' => Time::now()
            ];

            // Insert ke tabel 'orang'
            $this->db->table('orang')->insert($data);
        }
    }
}
