<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Capaci_termica_ar;
class CapacidadeController extends Controller
{
    public function getBTU(){
       $objeto = Capaci_termica_ar::all();
       return json_encode( $objeto );
    }
}
