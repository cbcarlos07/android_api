<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Capaci_termica_ar;
use App\Marcas_ar;
use App\Modelos_ar;
use App\Nivel_econo_ar;
use App\Pec;
use App\Refrigeradores_cliente;
use App\Service;
use App\Serv_pen;
use App\Tencao_tomada_ar;
class GeralController extends Controller
{
    public function getBTU(){
        $objeto = Capaci_termica_ar::all();
        return json_encode( $objeto );
    }

    public function getMarcas(){
        $marcas = Marcas_ar::all();
        return json_encode( $marcas );
    }

    public function getImg( $img ){
        return view('img')->with( 'img',$img );
    }

    public function getModelos(){
        $objeto = Modelos_ar::all();
        return json_encode( $objeto );
    }

    public function getNvEcon(){
        $objeto = Nivel_econo_ar::all();
        return json_encode( $objeto );
    }

    public function getPecs(){
        $pecs = Pec::all();
        return json_encode( $pecs );
    }

    public function getAllAr(){
        $objeto = Refrigeradores_cliente::all();
        return json_encode( $objeto );
    }

    public function getServices(){
        $services = Service::all();
        return json_encode( $services );
    }

    public function getServPen(){
        $objeto = Serv_pen::all();
        return json_encode( $objeto );
    }

    public function getTencao(){
        $tencao = Tencao_tomada_ar::all();
        return json_encode( $tencao );
    }
}
