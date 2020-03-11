<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Respose;
use App\Vehiculo;

class vehiculosController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index(){
    	$vehiculos = Vehiculo::all();

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'vehiculos' => $vehiculos
    	], 200);

    }

    public function show($id){
    	$vehiculo = Vehiculo::find($id);

    	if(is_object($vehiculo)){
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'vehiculo' => $vehiculo
    		];
    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'no se encuentra el vehiculo solicitado'
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
    		$validate = \validate::make($params_array, [
    			'vehiculo' => 'required',
    			'marca' => 'required',
    			'modelo' => 'required',
    			'placas' => 'required'
    		]);

    		//crear el vehiculo
    		$vehiculo = new Vehiculo();
    		$vehiculo->vehiculo = $params_array['vehiculo'];
    		$vehiculo->marca = $params_array['marca'];
    		$vehiculo->modelo = $params_array['modelo'];
    		$vehiculo->placas = $params_array['placas'];
    		$vehiculo->save();

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'vehiculo' => $vehiculo
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

    public function update($id, Request $request){
    	//recoger los datos que me llegan por put
    	$json = $request->input('json', null);
    	$params_array = json_decode($json, true);

    	//evalua que los datos no esten vacios
    	if(!empty($params_array)){

    		//validar los datos
    		$validate = \validate::make($params_array, [
    			'vehiculo' => 'required',
    			'marca' => 'required',
    			'modelo' => 'required',
    			'placas' => 'required'
    		]);

    		//eliminar los datos que no queremos actualizar
    		unset($params_array['id']);
    		unset($params_array['created_at']);

    		//actualizar el vehiculo
    		$vehiculo = Vehiculo::where('id', $id)->update($params_array);

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'vehiculo' => $params_array
    		];

    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'los datos estan vacios o estan erroneos'
    		];

    	}

    	return response()->json($data, $data['code']);


    }
}
  