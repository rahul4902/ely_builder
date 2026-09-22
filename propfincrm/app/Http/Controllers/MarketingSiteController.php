<?php

namespace App\Http\Controllers;

class MarketingSiteController extends Controller
{
    public function home()
    {
        return view('landing');
    }
}
