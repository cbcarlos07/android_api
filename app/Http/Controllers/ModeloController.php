<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Modelos_ar;
class ModeloController extends Controller
{
    public function getModelos(){
        $objeto = Modelos_ar::all();
        return json_encode( $objeto );
    }
}
