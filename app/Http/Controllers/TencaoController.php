<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Tencao_tomada_ar;

class TencaoController extends Controller
{
    public function getTencao(){
        $tencao = Tencao_tomada_ar::all();
        return json_encode( $tencao );
    }
}
