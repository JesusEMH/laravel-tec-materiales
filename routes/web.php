<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//cargando clases
use App\Http\middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});

//RUTAS DEL CONTROLADOR DE USUARIOS

Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}', 'UserController@getImage');
Route::get('/api/user/detail/{id}', 'UserController@detail');

//rutas para el controlador de lugares
Route::resource('api/lugares', 'LugaresController');

//rutas para el controlador de vehiculos
Route::resource('api/vehiculos', 'vehiculosController');

//rutas para el controlador de departamentos
Route::resource('api/departamentos', 'departamentosController');

//rutas para el controlador de status
Route::resource('api/status', 'statusVehiculoController');

//rutas para el controlador de ubicaciones
Route::resource('api/ubicaciones', 'ubicacionController');


//rutas para el controlador de solicitudes de eventos
Route::resource('api/eventos', 'eventosController');
Route::post('/api/eventos/upload', 'eventosController@upload');
Route::get('/api/eventos/image/{filename}', 'eventosController@getImage');
Route::get('/api/eventos/status/{status}', 'eventosController@getStatus');
Route::get('/api/eventos/user/{id}', 'eventosController@getSolicitudByUser');

//rutas para el controlador de solicitudes de salidas
Route::resource('api/salidas', 'salidasController');
Route::post('/api/salidas/upload', 'salidasController@upload');
Route::get('/api/salidas/image/{filename}', 'salidasController@getImage');
Route::get('/api/salidas/status/{status}', 'salidasController@getStatus');
Route::get('/api/salidas/user/{id}', 'salidasController@getSolicitudByUser');

//rutas para el controlador de solicitudes de mantenimiento
Route::resource('api/mantenimiento', 'mantenimientoController');
Route::post('/api/mantenimiento/upload', 'mantenimientoController@upload');
Route::get('/api/mantenimiento/image/{filename}', 'mantenimientoController@getImage');
Route::get('/api/mantenimiento/status/{status}', 'mantenimientoController@getStatus');
Route::get('/api/mantenimiento/user/{id}', 'mantenimientoController@getSolicitudByUser');



