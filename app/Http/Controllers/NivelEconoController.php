<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Nivel_econo_ar;
class NivelEconoController extends Controller
{
    public function getNvEcon(){
        $objeto = Nivel_econo_ar::all();
        return json_encode( $objeto );
    }
}
