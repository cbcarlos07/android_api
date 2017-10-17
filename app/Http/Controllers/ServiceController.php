<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Service;

class ServiceController extends Controller
{
    public function getServices(){
        $services = Service::all();
        return json_encode( $services );
    }
}
