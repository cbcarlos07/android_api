<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Refrigeradores_cliente;
class RefrigeradoresController extends Controller
{
    public function getAllAr(){
        $objeto = Refrigeradores_cliente::all();
        return json_encode( $objeto );
    }
}
