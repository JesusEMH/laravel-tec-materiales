<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\helpers\JwtAuth;
use App\Salida;


class salidasController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index(){
    	$salidas = Salida::all()->load('vehiculos');

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'salidas' => $salidas
    	], 200);

    }

    public function show($id){
    	$salida = Salida::find($id)-load('vehiculos');

    	if(is_object($salida)){
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'salida' => $salida
    		];

    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'los datos no son correctos'
    		];

    	}

    	return response()->json($data, $data['code']);

    }

    public function store(Request $request){
    	//recoger los datos por post
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);

    	//hacer un condicional
    	if(!empty($params_array)){
    		//conseguir usuario identificado
    		$user = $this->getIdentity($request);

    		//validar los datos
    		$validate = \validate::make($params_array, [
    			'titulo' => 'required',
    			'contenido' => 'required',
    			'vehiculo_id' => 'required'
    		]);

    		//guardar la solicitud
    		$salida = new Salida();
    		$salida->titulo = $params_array['titulo'];
    		$salida->contenido = $params_array['contenido'];
    		$salida->vehiculo_id = $params_array['vehiculo_id'];
    		$salida->usuario_id = $user->sub;
    		$salida->fecha = $params_array['fecha'];
    		$salida->hora_inicio = $params_array['hora_inicio'];
    		$salida->hora_final = $params_array['hora_final'];
    		$salida->save();

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'salida' => $salida
    		];

    	}else{
    		$data = [
    			'code' => 404,
    			'stauts' => 'error',
    			'message' => 'datos erroneos'
    		];

    	}

    	//devolver un resultado
    	return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
    	//recoger los datos que llegan por put
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	//MENSAJE DE ERROR POR DEFECTO
    	$data = [
	    		'code' => 404,
	    		'status' => 'error',
	    		'message' => 'los datos no son validos'
	    	];


    	//hacer un condicional
    	if(!empty($params_array)){

    		//validar los datos
    		$validate = \validator::make($params_array, [
    			'titulo' => 'required',
    			'contenido' => 'required',
    			'vehiculo_id' => 'required'
    		]);

    		//quitar los campos que no quiero actualizar
    		unset($params_array['id']);
    		unset($params_array['created_at']);
    		unset($params_array['usuario_id']);

    		//obtener el usuario identificado
    		$user = $this->getIdentity($request);

    		//buscar el registro a actualizar
    		$salida = Salida::where('id', $id)->where('usuario_id', $user->sub)->first();

    		//hacer un condicional
    		if(!empty($salida) && is_object($salida)){
    			//actualizar la salida
    			$salida->update($params_array);

    			//devolver el resultado
    			$data = [
    				'code' => 200,
    				'status' => 'success',
    				'salida' => $salida,
    				'cambios' => $params_array
    			];
    		}




    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'datos erroneos'
    		];
    	}

    	//devolver un resultado
    	return response()->json($data, $data['code']);

    }

    public function destroy($id, Request $request){
    	//recuperar al usuario identificado
    	$user = $this->getIdentity($request);

    	//obtener la salida
    	$salida = Salida::where('id', $id)->where('usuario_id', $user->sub)->first();

    	//evaluar que la salida no este vacia
    	is(!empty($salida)){
    		//borrar la salida
    		$salida->delete();

    		//devolver mensaje de exito
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'salida' => $salida
    		];

    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'datos erroneos'
    		];
    	}

    	//devolver un resultado
    	return response()->json($data, $data['code']);

    }

    private function getIdentity($request){
    	$jwtAuth = new JwtAuth();
    	$token = $request->header('Authorization', null);
    	$user = $jwtAuth->checkToken($token, true);

    	return $user;
    }
}
