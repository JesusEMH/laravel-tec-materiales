<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Ubicacion;

class ubicacionController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index(){
    	$ubicacion = Ubicacion::all();

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'ubicacion' => $ubicacion
    	], 200);

    }

    public function show($id){
    	$ubicacion = Ubicacion::find($id);

    	if(is_object($ubicacion)){
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'ubicacion' => $ubicacion
    		];
    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'no se encuentra la ubicacion solicitada'
    		];
    	}

    	return response()->json($data, $data['code']);

    }

    public function store(Request $request){
    	//recoger los datos que me llegan por post
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	//evaluar esos datos
    	if(!empty($params_array)){

    		//validar la informacion
    		$validate = \Validator::make($params_array, [
    			'lugar' => 'required',
    		]);

    		//crear el vehiculo
    		$ubicacion = new Ubicacion();
    		$ubicacion->lugar = $params_array['lugar'];
    		$ubicacion->save();

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'ubicacion' => $ubicacion
    		];

    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'los datos estan erroneos o estan vacios'
    		];

    	}
    	return response()->json($data, $data['code']);

    }

}
  