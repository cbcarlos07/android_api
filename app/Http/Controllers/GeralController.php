<?php

namespace App\Http\Controllers;




use App\Http\Requests;
use App\OrdemService;
use App\PecsProbServFunc;
use App\PosiFunc;
use App\ServicesFunc;
use Request;
use App\CapaciTermicaAr;
use App\MarcasAr;
use App\ModelosAr;
use App\NivelEconoAr;
use App\Pec;
use App\RefrigeradoresCliente;
use App\Service;
use App\ServPen;
use App\TencaoTomadaAr;
use App\UserFunc;
use Validator;
class GeralController extends Controller
{
    public function getBTU(){
        $objeto = CapaciTermicaAr::all();
        return json_encode( $objeto );
    }

    public function getMarcas(){
        $marcas = MarcasAr::all();
        return json_encode( $marcas );
    }

    public function getImg( $img ){
        return view('img')->with( 'img',$img );
    }

    public function getModelos(){
        $objeto = ModelosAr::all();
        return json_encode( $objeto );
    }

    public function getNvEcon(){
        $objeto = NivelEconoAr::all();
        return json_encode( $objeto );
    }

    public function getPecs(){
        $pecs = Pec::all();
        return json_encode( $pecs );
    }

    public function getAllAr(){
        $objeto = RefrigeradoresCliente::all();
        return json_encode( $objeto );
    }

    public function getServices(){
        $services = Service::all();
        return json_encode( $services );
    }

    public function getServPen(){
        $objeto = ServPen::all();
        return json_encode( $objeto );
    }

    public function getTencao(){
        $tencao = TencaoTomadaAr::all();
        return json_encode( $tencao );
    }

    public function getUserByEmailAndPasswordAndMatricula(  ){

        $email     = Request::input('email');
        $pwd       = Request::input('password');
        $matricula = Request::input('matricula');
       // echo $matricula;
        $user_func = UserFunc::where('email', $email)
                            ->orWhere('matricula', $matricula)
                            ->get();

        $tetorno = array();
        if( sizeof( $user_func ) > 0 ){
            $salt = "";
            $encrypted_password = "";
            $id         = 0;
            $nome       = "";
            $email      = "";
            $created_at = "";
            $updated_at = "";

            foreach ( $user_func as $user ){
                $salt               = $user->salt;
                $encrypted_password = $user->encrypted_password;
                $id                 = $user->unique_id;
                $nome               = $user->name;
                $email              = $user->email;
                $matricula          = $user->matricula;
                $created_at         = $user->created_at;
                $updated_at         = $user->updated_at;

            }

            $hash = $this->checkhashSSHA( $salt, $pwd );

            if( $encrypted_password == $hash ){
                $tetorno = array(
                    'error' => false,
                    'uid'   => $id,
                    'user'  => array(
                        'name'       => $nome,
                        'matricula'  => $matricula,
                        'email'      => $email,
                        'created_at' => $created_at,
                        'updated_at' => $updated_at
                    ),
                );

            }

        }else{
            $tetorno = array(
                'error' => true,
                'error_msg' => 'Login ou Senha incorretos!'

            );



        }
        echo json_encode( $tetorno );
    }

    public function getArCli( ){

        $id = Request::input('id_ar');

        $cliente = RefrigeradoresCliente::find( $id );
        //echo "Variavel: ".typeOf( $refrigeradores );
        $retorno = array();
        $dados   = array();
        $validator = Validator::make(
            [
                'id'    => $id

            ],
            [
                'id' => 'required'
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_list" => "Parametro do Status Incorreto!"
            );

            echo json_encode( $retorno );
        }else {

            if (sizeof($cliente) > 0) {

                $dados = array(
                    "id_refri" => $id,
                    "peso" => $cliente->peso,
                    "has_control" => $cliente->has_control,
                    "has_exaustor" => $cliente->has_exaustor,
                    "saida_ar" => $cliente->saida_ar,
                    "nivel_econo" => $cliente->nivel_econo,
                    "tamanho" => $cliente->tamanho,
                    "capaci_termica" => $cliente->capaci_termica,
                    "tencao_tomada" => $cliente->tencao_tomada,
                    "has_timer" => $cliente->has_timer,
                    "tipo_modelo" => $cliente->tipo_modelo,
                    "marca" => $cliente->marca,
                    "temp_uso" => $cliente->temp_uso,
                    "foto1" => $cliente->foto1,
                    "foto2" => $cliente->foto2,
                    "foto3" => $cliente->foto3,
                    "id_cliente" => $cliente->id_cliente
                );


                $retorno = array(
                    "error" => false,
                    "data" => $dados
                );
            } else {
                $retorno = array(
                    "error" => true,
                    "error_list" => "Ar condicionado não encontrado!"
                );

            }

            echo json_encode($retorno);
        }

    }
    
    public function storeUser(){
        $name      = Request::input('name');
        $email     = Request::input('email');
        $password  = Request::input('password');
        $matricula = Request::input('matricula');

        $validator = Validator::make(
            [
                'nome'      => $name,
                'email'     => $email,
                'password'  => $password,
                'matricula' => $matricula

            ],
            [
                'nome'      => 'required',
                'email'     => 'required',
                'password'  => 'required',
                'matricula' => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (name, email or password) estão incorretos!"
            );

            echo json_encode( $retorno );
        }else {

            $user_func = UserFunc::select('email')
                ->where('email', $email)
                ->orWhere('matricula', $matricula)
                ->get();
            $retorno = array();
            if (sizeof($user_func) > 0) {
                $retorno = array(
                    "error" => true,
                    "error_msg" => "Usuario ja existente " . $email
                );
            } else {
                $uuid = uniqid('', true);
                $hash = $this->hashSSHA($password);
                $encrypted_password = $hash['encrypted'];
                $salt = $hash["salt"]; // salt

                //(unique_id, matricula, name, email, encrypted_password, salt, created_at)

                $usuario = new UserFunc();
                $usuario->unique_id = $uuid;
                $usuario->matricula = $matricula;
                $usuario->name = $name;
                $usuario->email = $email;
                $usuario->encrypted_password = $encrypted_password;
                $usuario->salt = $salt;
                $teste = $usuario->save();
               // echo json_encode(array( "retorno" => $teste ));
                $insertedId = $usuario->id;

                if( $teste ){

                    $user = UserFunc::find($insertedId);

                    if (sizeof($user) > 0) {
                        $retorno = array(
                            "error" => false,
                            "uid" => $user->unique_id,
                            "user" => array(
                                "name" => $user->name,
                                "matricula" => $user->matricula,
                                "email" => $user->email,
                                "created_at" => $user->created_at,
                                "updated_at" => $user->updated_at
                            )

                        );

                    }
                }

                 else {
                    $retorno = array(
                        "error" => true,
                        "error_msg" => "Ocorreu um erro ao realizar registro"
                    );
                }

            }

            echo json_encode($retorno);
        }
        
    }

    public function updateStatusServ(){
        // receiving the post params
        $id_serv  = Request::input('id_serv');
        $new_serv = Request::input('newStatus');
        $tetorno = array();
        $validator = Validator::make(
            [
                'id'       => $id_serv,
                'novo'     => $new_serv

            ],
            [
                'id'       => 'required',
                'novo'     => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (id_serv e newStatus) estão incorretos! POSTs"
            );

            echo json_encode( $retorno );
        }else{
            $servpen = ServPen::find( $id_serv );
            $servpen->statusServ = $new_serv;

            if( $servpen->save() ){
                $tetorno = array(
                    "error" => false,
                    "error_msg" => "Status do Service Atualizado"
                );
            }else{
                $tetorno = array(
                    "error" => false,
                    "error_msg" => "Ocorreu um erro ao atualisar service"
                );
            }
        }
        echo json_encode( $tetorno );
    }

    public function updateMatriFunc(){
        $id         = Request::input('id_serv');
        $matricula  = Request::input('newMatriFunc');
        $retorno = array();
        $validator = Validator::make(
            [
                'id'         => $id,
                'matricula'  => $matricula

            ],
            [
                'id'        => 'required',
                'matricula' => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (id_serv e newMatriFunc) estão incorretos! POSTs"
            );

            echo json_encode( $retorno );
        }else{

            $servpen = ServPen::find( $id );
            $servpen->MatriFuncTec = $matricula;
            if( $servpen->save() ){
                $retorno = array(
                    "error"  => false,
                    "error_msg" => "Referencia de funcionario ao fazer service realizada"
                );
            }else{
                $retorno = array(
                    "error"  => true,
                    "error_msg" => "Ocorreu um erro ao atualisar service"
                );
            }
            echo json_encode( $retorno );

        }

    }

    public function addPcsProbleOS(){
        $pc =   Request::input('id_pc');
        $os =   Request::input('id_os');
        $retorno = array();
        $validator = Validator::make(
            [
                'pc'  => $pc,
                'os'  => $os

            ],
            [
                'pc' => 'required',
                'os' => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (id_pc) estão incorretos!"
            );

            echo json_encode( $retorno );
        }else{
            //id_pc, id_pcs_os
            $pec = new PecsProbServFunc();
            $pec->id_pc = $pc;
            $pec->id_pcs_os = $os;
            if( $pec->save() ){
                $retorno = array(
                    "error" => false,
                    "error_msg" => "Dados inseridos com sucesso!"
                );
            }else{
                $retorno = array(
                    "error" => true,
                    "error_msg" => "Ocorreu um erro ao salvar"
                );
            }
            echo json_encode( $retorno );

        }

    }

    public function addPecs(){
        $nome   = Request::input('nome');
        $modelo = Request::input('modelo');
        $marca  = Request::input('marca');
        $retorno = array();
        $validator = Validator::make(
            [
                'nome'    => $nome,
                'modelo'  => $modelo,
                'marca'   => $marca

            ],
            [
                'nome'   => 'required',
                'modelo' => 'required',
                'marca'  => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (nome, modelo, marca) estão incorretos!"
            );

            echo json_encode( $retorno );
        }else{
            //id_pc, nome, modelo, marca
            $pec = new Pec();
            $pec->nome   = $nome;
            $pec->modelo = $modelo;
            $pec->marca  = $marca;

            if( $pec->save() ){
                $retorno = array(
                    "error"     => false,
                    "error_msg" => "Dados inseridos com sucesso!"
                );
            }else{
                $retorno = array(
                    "error"     => true,
                    "error_msg" => "Erro ao salvar dados"
                );
            }
            echo json_encode( $retorno );
        }

    }

    public function addServices(){
        $nome   = Request::input('nome');
        $descricao = Request::input('descri');
        $tempo  = Request::input('tempo');
        $retorno = array();
        $validator = Validator::make(
            [
                'nome'    => $nome,
                'desc'    => $descricao,
                'tempo'   => $tempo

            ],
            [
                'nome'   => 'required',
                'desc' => 'required',
                'tempo'  => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (nome, descri, tempo) estão incorretos"
            );

            echo json_encode( $retorno );
        }else{
            //id_service, nome, descri, tempo
            $service = new Service();
            $service->nome   = $nome;
            $service->descri = $descricao;
            $service->tempo  = $tempo;

            if( $service->save() ){
                $retorno = array(
                    "error"     => false,
                    "error_msg" => "Dados inseridos com sucesso!"
                );
            }else{
                $retorno = array(
                    "error"     => true,
                    "error_msg" => "Erro ao salvar dados"
                );
            }
            echo json_encode( $retorno );
        }

    }

    public function addServicesFuncOS(){
        $service   = Request::input('id_services');
        $os        = Request::input('id_os');

        $retorno = array();
        $validator = Validator::make(
            [
                'service'    => $service,
                'os'         => $os,
            ],
            [
                'service'   => 'required',
                'os'        => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (id_service) estão incorretos!"
            );

            echo json_encode( $retorno );
        }else{
            //id_service, id_services_os
            $objeto = new ServicesFunc();
            $objeto->id_service     = $service;
            $objeto->id_services_os = $os;

            if( $objeto->save() ){
                $retorno = array(
                    "error"     => false,
                    "error_msg" => "Dados inseridos com sucesso!"
                );
            }else{
                $retorno = array(
                    "error"     => true,
                    "error_msg" => "Erro ao salvar dados"
                );
            }
            echo json_encode( $retorno );
        }

    }

    public function addOS(){
        // receiving the post params
        $id_cli      = Request::input('id_cliente');
        $matri_func  = Request::input('matri_func');
        $tipo_manu   = Request::input('tipo_manu');
        $obs         = Request::input('obs');
        $data_       = Request::input('data');
        $hora_ini    = Request::input('hora_ini');
        $hora_fin    = Request::input('hora_fin');

        /*$data_t = str_replace('/','-', $data_);
        $data = date( 'Y-m-d', strtotime( $data_t ) );*/

        $retorno = array();
        $validator = Validator::make(
            [
                'id'     => $id_cli,
                'mat'    => $matri_func,
                'tipo'   => $tipo_manu,
                'obs'    => $obs,
                'data'   => $data_,
                'inicio' => $hora_ini,
                'fim'    => $hora_fin,
            ],
            [
                'id'     => 'required',
                'mat'    => 'required',
                'tipo'   => 'required',
                'obs'    => 'required',
                'data'   => 'required',
                'inicio' => 'required',
                'fim'    => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (id_cliente, matri_func, obs or tipo_manu) estão incorretos!"
            );

            echo json_encode( $retorno );
        }else{
            //id_os, id_cliente, matri_func, tipo_manu, obs, data, hora_ini, hora_fin
            $objeto = new OrdemService();
            $objeto->id_cliente = $id_cli;
            $objeto->matri_func = $matri_func;
            $objeto->tipo_manu  = $tipo_manu;
            $objeto->obs        = $obs;
            $objeto->data       = $data_;
            $objeto->hora_ini   = $hora_ini;
            $objeto->hora_fin   = $hora_fin;

            if( $objeto->save() ){
                $retorno = array(
                    "error"     => false,
                    "error_msg" => "Dados inseridos com sucesso!"
                );
            }else{
                $retorno = array(
                    "error"     => true,
                    "error_msg" => "Erro ao salvar dados"
                );
            }
            echo json_encode( $retorno );
        }

    }

    public function updateDescriAr(){
        // receiving the post params
        $id_ar= $_POST['id_ar'];
        $peso = $_POST['peso'];
        $has_control = $_POST['has_control'];
        $has_exaustor = $_POST['has_exaustor'];
        $saida_ar = $_POST['saida_ar'];
        $capaci_termica = $_POST['capaci_termica'];
        $tencao_tomada = $_POST['tencao_tomada'];
        $has_timer = $_POST['has_timer'];
        $tipo_modelo = $_POST['tipo_modelo'];
        $marca = $_POST['marca'];
        $temp_uso = $_POST['temp_uso'];
        $nivel_econo = $_POST['nivel_econo'];
        $tamanho = $_POST['tamanho'];
        $foto1 = $_POST['foto1'];
        $foto2 = $_POST['foto2'];
        $foto3 = $_POST['foto3'];

        $retorno = array();
        $validator = Validator::make(
            [
                'id'             => $id_ar,
                'peso'           => $peso,
                'has_control'    => $has_control,
                'has_exaustor'   => $has_exaustor,
                'saida_ar'       => $saida_ar,
                'capaci_termica' => $capaci_termica,
                'tencao_tomada'  => $tencao_tomada,
                'has_timer'      => $has_timer,
                'tipo_modelo'    => $tipo_modelo,
                'marca'          => $marca,
                'temp_uso'       => $temp_uso,
                'nivel_econo'    => $nivel_econo,
                'tamanho'        => $tamanho,
                'foto1'          => $foto1,
                'foto2'          => $foto2,
                'foto3'          => $foto3,
            ],
            [
                'id'             => 'required',
                'peso'           => 'required',
                'has_control'    => 'required',
                'has_exaustor'   => 'required',
                'saida_ar'       => 'required',
                'capaci_termica' => 'required',
                'tencao_tomada'  => 'required',
                'has_timer'      => 'required',
                'tipo_modelo'    => 'required',
                'marca'          => 'required',
                'temp_uso'       => 'required',
                'nivel_econo'    => 'required',
                'tamanho'        => 'required',
                'foto1'          => 'required',
                'foto2'          => 'required',
                'foto3'          => 'required'
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos  estão incorretos! POSTs"
            );

            echo json_encode( $retorno );
        }else{
            /*
             * UPDATE refrigeradores_clientes SET peso = ?, has_control = ?, has_exaustor = ?, saida_ar = ?, nivel_econo = ?,tamanho = ?,
               capaci_termica = ?, tencao_tomada = ?, has_timer = ?, tipo_modelo = ?, marca = ?, temp_uso = ?, foto1 = ?,
               foto2 = ?, foto3 = ?  WHERE id_refri LIKE ?");
             */
            $objeto =  RefrigeradoresCliente::find( $id_ar );
            $objeto->peso           = $peso;
            $objeto->has_control    = $has_control;
            $objeto->has_exaustor   = $has_exaustor;
            $objeto->saida_ar       = $saida_ar;
            $objeto->nivel_econo    = $nivel_econo;
            $objeto->tamanho        = $tamanho;
            $objeto->capaci_termica = $capaci_termica;
            $objeto->tencao_tomada  = $tencao_tomada;
            $objeto->has_timer      = $has_timer;
            $objeto->tipo_modelo    = $tipo_modelo;
            $objeto->marca          = $marca;
            $objeto->temp_uso       = $temp_uso;
            $objeto->foto1          = $foto1;
            $objeto->foto2          = $foto2;
            $objeto->foto3          = $foto3;


            if( $objeto->save() ){
                $retorno = array(
                    "error"     => false,
                    "error_msg" => "update de dados do ar condicionado do cliente feita com sucesso!!!!"
                );
            }else{
                $retorno = array(
                    "error"     => true,
                    "error_msg" => "Ocorreu um erro ao atualisar dados do AR"
                );
            }
            echo json_encode( $retorno );
        }

    }

    public function addPosiFunc(){
        // receiving the post params
        $matriFunc = Request::input('matriFunc');
        $latitude  = Request::input('latitude');
        $longitude = Request::input('longitude');
        $dataPosi  = Request::input('dataPosi');
        $horaPosi  = Request::input('horaPosi');

        $retorno = array();
        $validator = Validator::make(
            [
                'matricula'  => $matriFunc,
                'latitude'   => $latitude,
                'longitude'  => $longitude,
                'dataPosi'   => $dataPosi,
                'horaPosi'   => $horaPosi

            ],
            [
                'matricula'  => 'required',
                'latitude'   => 'required',
                'longitude'  => 'required',
                'dataPosi'   => 'required',
                'horaPosi'   => 'required'
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "dados inseridos (nome, modelo, marca) estão incorretos!"
            );

            echo json_encode( $retorno );
        }else{
            /*
             cod, matriFunc, latitude, longitude, dataPosi, horaPosi;
             */
            $objeto =  new PosiFunc(  );
            $objeto->matriFunc    = $matriFunc;
            $objeto->latitude     = $latitude;
            $objeto->longitude    = $longitude;
            $objeto->dataPosi     = $dataPosi;
            $objeto->horaPosi     = $horaPosi;


            if( $objeto->save() ){
                $retorno = array(
                    "error"     => false,
                    "error_msg" => "Dados inseridos com sucesso!"
                );
            }else{
                $retorno = array(
                    "error"     => true,
                    "error_msg" => "Ocorreu um erro ao salvar dados"
                );
            }
            echo json_encode( $retorno );
        }

    }


    /** metodos locais **/

    private function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    private function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }


    //função decodifica caracteres especiais acentuados
    public function array_utf8_encode($dat)
    {
        if (is_string($dat))
            return utf8_encode($dat);
        if (!is_array($dat))
            return $dat;
        $ret = array();
        foreach ($dat as $i => $d)
            $ret[$i] = self::array_utf8_encode($d);
        return $ret;
    }
}
