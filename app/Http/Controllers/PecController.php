<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Pec;

class PecController extends Controller
{
    public function getPecs(){
        $pecs = Pec::all();
        return json_encode( $pecs );
    }
}
