<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Marcas_ar;
class MarcaController extends Controller
{
    public function getMarcas(){
       $marcas = Marcas_ar::all();
       return json_encode( $marcas );
    }
}
