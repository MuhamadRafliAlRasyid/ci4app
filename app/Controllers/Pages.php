<?php

namespace App\Controllers;

use Kint\Value\FunctionValue;

class Pages extends BaseController
{
    public function Index()
    {
        $data = [
            'title' => 'Halaman Home Lat Ci',
            'tes' => ['satu', 'dua', 'tiga']
        ];
        return view('pages/home', $data);
    }
    public function about()
    {
        $data = [
            'title' => 'Halaman About Lat Ci'
        ];
        return view('pages/about', $data);
    }
    public function Contact()
    {
        $data = [
            'title' => 'Contact Us',
            'alamat' => [
                [
                    'tipe' => 'Rumah',
                    'alamat' => ' di bubulak dongs',
                    'kota' => 'Karawang'
                ],
                [
                    'tipe' => 'Kantor',
                    'alamat' => ' di Gs Battery',
                    'kota' => 'Karawang'
                ]

            ]
        ];
        return view('pages/contact', $data);
    }
}
