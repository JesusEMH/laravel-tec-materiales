<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\helpers\JwtAuth;
use App\Evento;


class eventosController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index(){
    	$eventos = Evento::all()->load('lugares');

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'eventos' => $eventos
    	], 200);
    }

    public function show($id){
    	$evento = Evento::find($id)->load('lugares');

    	if(is_object($evento)){
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'evento' => $evento
    		];
    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'evento no encontrado en la base de datos'
    		];
    	}

    	return response()->json($data, $data['code']);
    }

    public function store(Request $request){
    	//recoger los datos por post
    	$json = $request->input('json', null);
    	$params = json_decode($json);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)){
    		//conseguir usuario identificado
    		$user = $this->getIdentity($request);


	    	//validar los datos
	    	$validate = \Validator::make($params_array, [
	    		'titulo' => 'required',
	    		'contenido' => 'required',
	    		'lugar_id' => 'required',

	    	]);

	    	//guardar la solicitud

	    	if($validate->fails()){
	    		$data = [
	    			'code' => 404,
	    			'status' => 'error',
	    			'message' => 'los datos son erroneos o faltan datos'
	    		];
	    	}else{
	    		$evento = new Evento();
	    		$evento->titulo = $params_array['titulo'];
	    		$evento->contenido = $params_array['contenido'];
	    		$evento->usuario_id = $user->sub;
	    		$evento->lugar_id = $params_array['lugar_id'];
	    		$evento->distribucion = $params_array['distribucion'];
	    		$evento->hora_inicio = $params_array['hora_inicio'];
	    		$evento->hora_final = $params_array['hora_final'];
	    		$evento->fecha = $params_array['fecha'];
	    		$evento->save();

	    		$data = [
	    			'code' => 200,
	    			'status' => 'success',
	    			'evento' => $evento
	    		];
	    	}

    	}else{
    		$data = [
	    			'code' => 404,
	    			'status' => 'error',
	    			'message' => 'envia los datos correctamente'
	    		];

    	}


    	//mostrar el resultado
    	return response()->json($data, $data['code']);
    }

    public function update($id, Request $request){
    	//recoger los datos que me llegan por post
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	$data = [
	    		'code' => 404,
	    		'status' => 'error',
	    		'message' => 'los datos no son validos'
	    	];

    	if(!empty($params_array)){
	    	//validar los datos
	    	$validate = \Validator::make($params_array, [
	    		'titulo' => 'required',
	    		'contenido' => 'required',
	    		'lugar_id' => 'required'
	    	]);

	    	if($validate->fails()){
	    		$data['errors'] = $validate->errors();
	    		return response()->json($data, $data['code']);
	    	}

	    	//quitar lo que no quiero actualizar
	    	unset($params_array['id']);
	    	unset($params_array['usuario_id']);
	    	unset($params_array['created_at']);

	    	//conseguir usuario identificado
    		$user = $this->getIdentity($request);

    		//buscar el registro a actualizar
    		$evento = Evento::where('id', $id)
    						  ->where('usuario_id', $user->sub)
    						  ->first();

    		if(!empty($evento) && is_object($evento)){
				//actualizar el registro
    			$evento->update($params_array);

    			//devover el resultado
		    	$data = [
		    		'code' => 200,
		    		'status' => 'success',
		    		'evento' => $evento,
		    		'cambios' => $params_array
		    	];

    		}

    	}else{
    		   	$data = [
	    			'code' => 404,
	    			'status' => 'error',
	    			'message' => 'los datos estan vacios'
	    		];
    	}
    	//devolver los datos
    	return response()->json($data, $data['code']);

    }

    public function destroy($id, Request $request){

    	//conseguir usuario identificado
    	$user = $this->getIdentity($request);

    	//conseguir el evento
    	$evento = Evento::where('id', $id)->where('usuario_id', $user->sub)->first();

    	if(!empty($evento)){

	    	// borrar el evento
	    	$evento->delete();

	    	//devolver algo
	    	$data = [
	    		'code' = >200,
	    		'status' => 'success',
	    		'evento' => $evento
	    	];

    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'no se ha podido borrar el evento'
    		];
    	}


    	return response()->json($data, $data['code']);
    }

    private function getIdentity($request){
    	$jwtAuth = new JwtAuth();
    	$token = $request->header('Authorization', null);
    	$user = $jwtAuth->checkToken($token, true);

    	return $user;
    }
}
