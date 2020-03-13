<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Departamento;

class departamentosController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show']]);
	}

    public function index(){
    	$departamentos = Departamento::all();

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'departamentos' => $departamentos
    	], 200);

    }

    public function show($id){
    	$departamento = Departamento::find($id);

    	if(is_object($departamento)){
    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'departamento' => $departamento
    		];
    	}else{
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'no se encuentra el departamento solicitado'
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
    			'nombre' => 'required',
    			'jefe' => 'required',
    		]);

    		//crear el vehiculo
    		$departamento = new Departamento();
    		$departamento->nombre = $params_array['nombre'];
    		$departamento->jefe = $params_array['jefe'];
    		$departamento->correo = $params_array['correo'];
    		$departamento->telefono = $params_array['telefono'];
    		$departamento->save();

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'departamento' => $departamento
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
    		$validate = \Validator::make($params_array, [
    			'nombre' => 'required',
    			'jefe' => 'required',
    		]);

    		//eliminar los datos que no queremos actualizar
    		unset($params_array['id']);
    		unset($params_array['created_at']);

    		//actualizar el vehiculo
    		$departamento = Departamento::where('id', $id)->update($params_array);

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'departamento' => $params_array
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
  