<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class HrController extends Controller
{

    public function ta_da() {
        return view('hr.ta_da');
    }

    public function leaves() {
        return view('hr.leaves');
    }
}
