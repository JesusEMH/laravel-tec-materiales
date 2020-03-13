<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\helpers\JwtAuth;
use App\Mantenimiento;
use App\Lugar;

class mantenimientoController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => [
			'index', 'show', 'getImage', 'getStatus', 'getSolicitudByUser']]);
	}

	public function index(){
		$mantenimiento = Mantenimiento::all()->load('users')->load('lugares');

		return response()->json([
			'code' => 200,
			'status' => 'success',
			'mantenimiento' => $mantenimiento
		], 200);

	}

	public function show($id){
		$mantenimiento = Mantenimiento::find($id)->load('lugares');

		if(is_object($mantenimiento)){
			$data = [
				'code' => 200,
				'status' => 'success',
				'mantenimiento' => $mantenimiento
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
			$validate = \Validator::make($params_array, [
				'titulo' => 'required',
				'contenido' => 'required',
				'lugar_id' => 'required'
			]);

			//guardar la solicidut
			$mantenimiento = new Mantenimiento();
			$mantenimiento->titulo = $params_array['titulo'];
			$mantenimiento->contenido = $params_array['contenido'];
			$mantenimiento->usuario_id = $user->sub;
			$mantenimiento->lugar_id = $params_array['lugar_id'];
			$mantenimiento->fecha = $params_array['fecha'];
			$mantenimiento->hora_inicio = $params_array['hora_inicio'];
			$mantenimiento->hora_final = $params_array['hora_final'];
			$mantenimiento->status = "pendiente";
			$mantenimiento->save();

			$data = [
				'code' => 200,
				'status' => 'success',
				'mantenimiento' => $mantenimiento
			];

		}else{
			$data = [
				'code' => 404,
				'status' => 'error',
				'message' => 'datos erroneos'
			];

		}

		//devolver el resultado
		return response()->json($data, $data['code']);

	}

	public function update($id, Request $request){
		//recoger los datos que llegan por put
		$json = $request->input('json', null);
		$params_array = json_decode($json, true);

		$data = [
				'code' => 404,
				'status' => 'error',
				'message' => 'datos erroneos'
			];

		//hacer un condicional
		if(!empty($params_array)){

			//validar los datos
			$validate = \Validator::make($params_array, [
				'titulo' => 'required',
				'contenido' => 'required',
				'lugar_id' => 'required'
			]);

    		//quitar los campos que no quiero actualizar
    		unset($params_array['id']);
    		unset($params_array['usuario_id']);
    		unset($params_array['created_at']);

    		//obtener el usuario identificado
    		$user = $this->getIdentity($request);

    		//buscar el registro a actualizar
    		$mantenimiento = Mantenimiento::where('id', $id)->where('usuario_id', $user->sub)->first();

    		//hacer un condicional
    		if(!empty($mantenimiento) && is_object($mantenimiento)){
    			//actualizar el registro
    			$mantenimiento->update($params_array);

    			//devolver el resultado
    			$data = [
    				'code' => 200,
    				'status' => 'success',
    				'mantenimiento' => $mantenimiento,
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

		//conseguir al usuario identificado
		$user = $this->getIdentity($request);

		//obtener la solicitud de mantenimiento
		$mantenimiento = Mantenimiento::where('id', $id)->where('usuario_id', $user->sub)->first();

		//evaluar si no esta vacio el objeto
		if(!empty($mantenimiento)){
			//eliminar la solicutud
			$mantenimiento->delete();

			//devolver mensaje de exito
			$data = [
				'code' => 200,
				'status' => 'success',
				'mantenimiento' => $mantenimiento
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

    public function upload(Request $request){
    	//recoger la imagen de la peticion
    	$image = $request->file('file0');

    	//validar la imagen
    	$validate = \Validator::make($request->all(), [
    		'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
    	]);

    	//guardar la imagen
    	if(!$image || $validate->fails()){
    		$data = [
    			'code' => 400,
    			'status' => 'error',
    			'message' => 'error al subir la imagen'
    		];

    	}else{
    		$image_name = time().$image->getClientOriginalName();

    		\Storage::disk('images')->put($image_name, \File::get($image));

    		$data = [
    			'code' => 200,
    			'status' => 'success',
    			'image' => $image_name
    		];

    	}

    	return response()->json($data, $data['code']);

    }

    public function getImage($filename){
    	//comprobar si existe el fichero
    	$isset = \Storage::disk('images')->exists($filename);

    	if($isset){
    		//conseguir la imagen
    		$file = \Storage::disk('images')->get($filename);

    		//devolver la imagen
    		return Response($file, 200);
    	}else{
    		//mostrar error
    		$data = [
    			'code' => 404,
    			'status' => 'error',
    			'message' => 'la imagen no existe'
    		];
    	}
    	return Response()->json($data, $data['code']);
    	

    }

    public function getStatus($status){
    	$mantenimiento = Mantenimiento::where('status', $status)->get();

    	return response()->json([
    		'status' => 'success',
    		'mantenimiento' => $mantenimiento
    	],200);
    }


    public function getSolicitudByUser($id){
    	$mantenimiento = Mantenimiento::where('usuario_id', $id)->get();

    	return response()->json([
    		'status' => 'success',
    		'mantenimiento' => $mantenimiento
    	], 200);
    }
}
