<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/listaService', "GeralController@getServices" );
Route::get('/listaPecs', "GeralController@getPecs" );
Route::get('/listaMarcas', "GeralController@getMarcas" );
Route::get('/listaModelos', "GeralController@getModelos" );
Route::get('/listaTencao', "GeralController@getTencao" );
Route::get('/listaNvEcon', "GeralController@getNvEcon" );
Route::get('/listaBTus', "GeralController@getBTU" );
Route::get('/listaAllAr', "GeralController@getAllAr" );
Route::get('/listaServPen', "GeralController@getServPen" );
Route::get('/image/{img}', "GeralController@getImg" );
Route::post('/login', "GeralController@getUserByEmailAndPasswordAndMatricula" );
Route::post('/retornaArCli', "GeralController@getArCli" );
Route::post('/register', "GeralController@storeUser" );
Route::post('/alterarStatus', "GeralController@updateStatusServ" );
Route::post('/alterarMatriFunc', "GeralController@updateMatriFunc" );
Route::post('/inserirPcsProbServ', "GeralController@addPcsProbleOS" );
Route::post('/inserirPecs', "GeralController@addPecs" );
Route::post('/inserirServices', "GeralController@addServices" );
Route::post('/inserirServicesFunc', "GeralController@addServicesFuncOS" );
Route::post('/inserirOS', "GeralController@addOS" );
Route::post('/alterarDescriAr', "GeralController@updateDescriAr" );
Route::post('/inserirPosiFunc', "GeralController@addPosiFunc" );
Route::post('/uploadImage', "GeralController@uploadImage" );