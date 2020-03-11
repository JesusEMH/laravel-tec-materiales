<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use illuminate\Http\Response;
use App\Lugar;

class LugaresController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index(){
    	$lugares = Lugar::all();

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'lugares' => $lugares
    	], 200);
    }

    public function show($id){
    	$lugar = Lugar::find($id);

    	if(is_object($lugar)){
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'lugar' => $lugar
    		];
    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'lugar no encontrado en la base de datos'
    		];
    	}
    	return response()->json($data, $data['code']);
    }

    public function store(Request $request){
    	//recoger los datos por post
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)){
    		//validar los datos

    		$validate = \Validator::make($params_array, [
    			'lugar' => 'required'
    		]);

    		//guardar los datos

    		if($validate->fails()){
    			$data = [
    				'code' => 404,
    				'status' => 'error',
    				'message' => 'los datos son erroneos'
    			];
    		}else{
    			$lugar = new Lugar();
    			$lugar->lugar = $params_array['lugar'];
    			$lugar->ubicacion = $params_array['ubicacion'];
    			$lugar->save();

    			$data = [
    				'code' => 200,
    				'status' = > 'success',
    				'lugar' => $lugar
    			];
    		}



    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'los datos recibidos son erroneos'
    		];
    	}

    	return response()->json($data, $data['code']);

    }

    public function update($id, Request $request){
    	//recoger los datos que me llegan por put
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	if(!empty($params_array)){
    		//validar los datos
    		$validate = \Validator::make($params_array, [
    			'lugar' => 'required'
    		]);

    		//quitar los datos que no quiero actualizar
    		unset($params_array['created_at']);
    		unset($params_array['id']);

    		//actualizar el lugar
    		$lugar = Lugar::where('id', $id)->update($params_array);

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'lugar' => $params_array
    		];



    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'los datos recibidos estan vacios o son erroneos'
    		];
    	}

    	return response()->json($data, $data['code']);
    }
}
