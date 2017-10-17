<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Serv_pen;
class ServPenController extends Controller
{
    public function getServPen(){
        $objeto = Serv_pen::all();
        return json_encode( $objeto );
    }
}
