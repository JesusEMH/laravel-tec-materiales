<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\StatusVehiculo;

class statusvehiculoController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index(){
    	$statusV = StatusVehiculo::all();

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'statusVehiculo' => $statusV
    	], 200);

    }

    public function show($id){
    	$statusV = StatusVehiculo::find($id);

    	if(is_object($statusV)){
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'statusVehiculo' => $statusV
    		];
    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'no se encuentra el status solicitado'
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
    			'status' => 'required',
    		]);

    		//crear el vehiculo
    		$statusV = new StatusVehiculo();
    		$statusV->status = $params_array['status'];
    		$statusV->save();

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'statusVehiculo' => $statusV
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
  