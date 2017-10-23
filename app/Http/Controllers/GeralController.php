<?php

namespace App\Http\Controllers;




use App\Http\Requests;
use Request;
use App\Capaci_termica_ar;
use App\Marcas_ar;
use App\Modelos_ar;
use App\Nivel_econo_ar;
use App\Pec;
use App\Refrigeradores_cliente;
use App\Service;
use App\Serv_pen;
use App\Tencao_tomada_ar;
use App\User_func;
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

    public function getUserByEmailAndPasswordAndMatricula(  ){

        $email     = Request::input('email');
        $pwd       = Request::input('password');
        $matricula = Request::input('matricula');
       // echo $matricula;
        $user_func = User_func::where('email', $email)
                            ->orWhere('matricula', $matricula)
                            ->get();

        $response = array();
        if( sizeof( $user_func ) > 0 ){
            //$salt = $user_func->salt;
            echo $user_func->salt;
            /*$encrypted_password = $user_func->encrypted_password;
            $hash = $this->checkhashSSHA( $salt, $encrypted_password );

            if( $encrypted_password = $hash ){
                $response = array(
                    'error' => false,
                    'uid'   => $user_func->unique_id,
                    'user'  => array(
                        'name'       => $user_func->name,
                        'matricula'  => $user_func->email,
                        'created_at' => $user_func->created_at,
                        'updated_at' => $user_func->updated_at
                    ),
                );

            }*/

        }else{
            $response = array(
                'error' => true,
                'error_msg' => 'Login ou Senha incorretos!'

            );



        }
        echo json_encode( $response );
    }

    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }
}
