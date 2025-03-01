<?php

namespace App\Controllers;

use Kint\Value\FunctionValue;

class Home extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function coba()
    {
        echo "Hello world saya $this->nama.";
    }
}
