<?php

namespace App\Http\Controllers;




use App\Http\Requests;
use Illuminate\Http\Request;
use App\OrdemService;
use App\PecsProbServFunc;
use App\PosiFunc;
use App\ServicesFunc;
//use Request;
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
use Illuminate\Support\Facades\DB;
//use Symfony\Component\HttpFoundation\Request;
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

    public function getServPen( Request $request ){
        $status    = $request->input( 'status' );
        $matriFunc = $request->input( 'matriFunc' );

        $retorno = array();
        $dados   = array();


        $validator = Validator::make(
            [
                'status'      => $status,
                'matricula'   => $matriFunc

            ],
            [
                'status'    => 'required',
                'matricula' => 'required',
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "Parametro do Status Incorreto!"
            );

            echo json_encode( $retorno );
        }else {


            if( $matriFunc == null ){
                $sql = "SELECT id_serv_pen, cliente, complemento, serv_pen.ender, 
                               cep, lotacionamento, statusServ,latitude, longitude,
                               date_format(data_serv,'%d/%m/%Y') as data_serv, hora_serv,descriCliProblem, descriTecniProblem, descriCliRefrigera,
                               nome, tipo, celular, fone_fixo, codRefriCli 
                        FROM serv_pen,clientes 
                        WHERE id_cli=cliente 
                          AND statusServ LIKE ? 
                        ORDER BY `serv_pen`.`data_serv` ASC";
                $objeto = DB::select( $sql, array( $status ) );
                 /*$objeto = ServPen::join( 'clientes', 'cliente', '=', 'id_cli' )
                     ->select('id_serv_pen','cliente','complemento', 'serv_pen.ender', 'cep','lotacionamento',
                         'statusServ', 'latitude','longitude',DB::raw('DATE_FORMAT(data_serv, "%d/%m/%Y") as data_serv'),
                         'hora_serv', 'descriCliProblem', 'descriTecniProblem','descriCliRefrigera', 'nome', 'tipo','celular',
                         'fone_fixo','codRefriCli')
                     ->where('statusServ', $status )
                     ->get();*/
                /*$objeto = ServPen::with('clientes')
                    ->select('id_serv_pen','cliente','complemento', 'serv_pen.ender', 'cep','lotacionamento',
                        'statusServ', 'latitude','longitude',DB::raw('DATE_FORMAT(data_serv, "%d/%m/%Y") as data_serv'),
                        'hora_serv', 'descriCliProblem', 'descriTecniProblem','descriCliRefrigera', 'nome', 'tipo','celular',
                        'fone_fixo','codRefriCli')
                    ->whereColumn([
                        ['id_cli', 'cliente'],
                        ['statusServ', $status ]
                    ])
                    ->get();*/
                //$objeto = ServPen::all();

            }else{
              /*  $sql = "SELECT id_serv_pen, cliente, complemento, serv_pen.ender,
                               cep, lotacionamento, statusServ,latitude, longitude,
                               date_format(data_serv,'%d/%m/%Y') as data_serv, hora_serv,descriCliProblem, descriTecniProblem, descriCliRefrigera,
                               nome, tipo, celular, fone_fixo, codRefriCli 
                        FROM serv_pen,clientes 
                        WHERE id_cli=cliente 
                          AND statusServ LIKE ? AND MatriFuncTec LIKE ?
                        ORDER BY `serv_pen`.`data_serv` ASC";
                $objeto = DB::select( $sql, array( $status, $matriFunc ) );*/
                /*$objeto = ServPen::join( 'clientes', 'cliente', '=', 'id_cli' )
                    ->select('id_serv_pen','cliente','complemento', 'serv_pen.ender', 'cep','lotacionamento',
                        'statusServ', 'latitude','longitude',DB::raw('DATE_FORMAT(data_serv, "%d/%m/%Y") as data_serv'),
                        'hora_serv', 'descriCliProblem', 'descriTecniProblem','descriCliRefrigera', 'nome', 'tipo','celular',
                        'fone_fixo','codRefriCli')
                    ->whereColumn([
                        ['statusServ', $status],
                        ['MatriFuncTec', $matriFunc]

                    ])
                    ->get();*/
                $objeto = ServPen::with('clientes')
                    ->select('id_serv_pen','cliente','complemento', 'serv_pen.ender','lotacionamento',
                        'statusServ', 'latitude','longitude',DB::raw('DATE_FORMAT(data_serv, "%d/%m/%Y") as data_serv'),
                        'hora_serv', 'descriCliProblem', 'descriTecniProblem','descriCliRefrigera', 'codRefriCli')
                    ->where('statusServ',$status)
                    ->get();
                //$objeto = ServPen::all();
            }
           // echo $objeto;
            if( sizeof( $objeto ) > 0 ){
                foreach ( $objeto as $item) {

                    $dados[] = array(
                        "uid"                => $item->id_serv_pen,
                        "latitude"           => $item->latitude,
                        "longitude"          => $item->longitude,
                        "cliente"            => $item->cli,
                        "lotacionamento"     => $item->lotacionamento,
                        "ender"              => $item->ender,
                        "complemento"        => $item->complemento,
                        "cep"                => $item->clientes->cep,
                        "data_serv"          => $item->data_serv,
                        "hora_serv"          => $item->hora_serv,
                        "descriCliProblem"   => $item->descriCliProblem,
                        "descriTecniProblem" => $item->descriTecniProblem,
                        "descriCliRefrigera" => $item->descriCliRefrigera,
                        "statusServ"         => $item->statusServ,
                        "nome"               => $item->clientes->nome,
                        "tipo"               => $item->clientes->tipo,
                        "fone1"              => $item->clientes->celular,
                        "fone2"              => $item->clientes->fone_fixo,
                        "id_refriCli"        => $item->codRefriCli,
                    );

                }
                $retorno = array(
                    "error" => false,
                    "data"  => $dados
                );

            }else{
                $retorno = array(
                    "error" => true,
                    "error_list" => "Lista Vazia!"
                );
            }




            return json_encode( $retorno );
        }


    }

    public function getTencao(){
        $tencao = TencaoTomadaAr::all();
        return json_encode( $tencao );
    }

    public function getUserByEmailAndPasswordAndMatricula( Request $request ){

        $email     = $request->input('email');
        $pwd       = $request->input('password');
        $matricula = $request->input('matricula');
       // echo $matricula;
        $retorno = array();
        $validator = Validator::make(
            [
                'email'     => $email,
                'pwd'       => $pwd,
                'matricula' => $matricula

            ],
            [
                'email'     => 'required',
                'pwd'       => 'required',
                'matricula' => 'required'
            ],
            [
                'required'  => ':attribute é obrigatório.'
            ]
        );
        if( $validator->fails() ){

            $retorno = array(
                "error"      => true,
                "error_msg" => "Login ou Senha incorretos!"
            );


        }else {
            $user_func = UserFunc::where('email', $email)
                ->orWhere('matricula', $matricula)
                ->get();
            
            //var_dump( $user_func );

            //echo sizeof( $user_func );
            if( sizeof( $user_func ) > 0 ){

                $salt               = $user_func[0]->salt;
                $encrypted_password = $user_func[0]->encrypted_password;
                $id         = $user_func[0]->unique_id;
                $nome       = $user_func[0]->name;
                $email      = $user_func[0]->email;
                $matricula  = $user_func[0]->matricula;
                $created_at = $user_func[0]->created_at;
                $updated_at = $user_func[0]->updated_at;



                $hash = $this->checkhashSSHA( $salt, $pwd );

                if( $encrypted_password == $hash ){

                    $retorno = array(
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

                }else{
                    $retorno = array(
                        'error' => true,
                        'error_msg' => 'As credenciais do login estão com erro'

                    );
                }

            }else{
                $retorno = array(
                    'error' => true,
                    'error_msg' => 'Matricula ou email não foram encontradas'

                );



            }

        }
        echo json_encode( $retorno );

    }

    public function getArCli( Request $request ){

        $id = $request->input('id_ar');

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
    
    public function storeUser( Request $request ){
        $name      = $request->input('name');
        $email     = $request->input('email');
        $password  = $request->input('password');
        $matricula = $request->input('matricula');

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

    public function updateStatusServ( Request $request ){
        // receiving the post params
        $id_serv  = $request->input('id_serv');
        $new_serv = $request->input('newStatus');
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

    public function updateMatriFunc( Request $request ){
        $id         = $request->input('id_serv');
        $matricula  = $request->input('newMatriFunc');
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

    public function addPcsProbleOS( Request $request ){
        $pc =   $request->input('id_pc');
        $os =   $request->input('id_os');
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

    public function addPecs( Request $request ){
        $nome   = $request->input('nome');
        $modelo = $request->input('modelo');
        $marca  = $request->input('marca');
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

    public function addServices( Request $request ){
        $nome   = $request->input('nome');
        $descricao = $request->input('descri');
        $tempo  = $request->input('tempo');
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

    public function addServicesFuncOS( Request $request ){
        $service   = $request->input('id_services');
        $os        = $request->input('id_os');

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

    public function addOS( Request $request ){
        // receiving the post params
        $id_cli      = $request->input('id_cliente');
        $matri_func  = $request->input('matri_func');
        $tipo_manu   = $request->input('tipo_manu');
        $obs         = $request->input('obs');
        $data_       = $request->input('data');
        $hora_ini    = $request->input('hora_ini');
        $hora_fin    = $request->input('hora_fin');

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

    public function updateDescriAr( Request $request ){
        // receiving the post params
        $id_ar          = $request->input('id_ar');
        $peso           = $request->input('peso');
        $has_control    = $request->input('has_control');
        $has_exaustor   = $request->input('has_exaustor');
        $saida_ar       = $request->input('saida_ar');
        $capaci_termica = $request->input('capaci_termica');
        $tencao_tomada  = $request->input('tencao_tomada');
        $has_timer      = $request->input('has_timer');
        $tipo_modelo    = $request->input('tipo_modelo');
        $marca          = $request->input('marca');
        $temp_uso       = $request->input('temp_uso');
        $nivel_econo    = $request->input('nivel_econo');
        $tamanho        = $request->input('tamanho');
        $foto1          = $request->input('foto1');
        $foto2          = $request->input('foto2');
        $foto3          = $request->input('foto3');

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

    public function addPosiFunc( Request $request ){
        // receiving the post params
        $matriFunc = $request->input('matriFunc');
        $latitude  = $request->input('latitude');
        $longitude = $request->input('longitude');
        $dataPosi  = $request->input('dataPosi');
        $horaPosi  = $request->input('horaPosi');

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

    public function uploadImage( Request $request ){
        //$destinationPath = public_path('uploads');

        //Image::make($avatar)->resize(300,300)->save($destinationPath.'/'.$filename);
        //$request = \Symfony\Component\HttpFoundation\Request::instance();

        $content = $request->getContent();

        $json = json_decode( $content );


        $name = $json["name"]; //within square bracket should be same as Utils.imageName & Utils.image
        $image = $json["image"];

        $response = array();

        $decodedImage = base64_decode( $image );

        $return = file_put_contents("img/".$name.".JPG", $decodedImage);

        //Image::make( $image )->resize(300,300)->save($destinationPath.'/'.$filename);

        if($return !== false){
            $response['success'] = 1;
            $response['message'] = "Image Uploaded Successfully";
            $response['img'] = $image;

        }else{
            $response['success'] = 0;
            $response['message'] = "Image Uploaded Failed";
        }


        //echo json_encode($response);
        echo json_encode( $json );
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
