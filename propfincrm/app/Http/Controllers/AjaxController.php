<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use Auth;
use DB;
use File;
use Session;
use Carbon\Carbon;
use Redirect;

class AjaxController extends Controller
{
    
    public function state($id)
    {
        $country = DB::table("countries")->where("name", '=',$id)
            ->pluck("id");
     $state = DB::table("states")->where("country_id",$country)
          ->pluck("name","name");
     return json_encode($state);
   
    }

}
