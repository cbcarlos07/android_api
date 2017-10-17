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


Route::get('/listaService', "ServiceController@getServices" );
Route::get('/listaPecs', "PecController@getPecs" );
Route::get('/listaMarcas', "MarcaController@getMarcas" );
Route::get('/listaModelos', "ModeloController@getModelos" );
Route::get('/listaTencao', "TencaoController@getTencao" );
Route::get('/listaNvEcon', "NivelEconoController@getNvEcon" );
Route::get('/listaBTus', "CapacidadeController@getBTU" );
Route::get('/listaAllAr', "RefrigeradoresController@getAllAr" );
Route::get('/listaServPen', "ServPenController@getServPen" );