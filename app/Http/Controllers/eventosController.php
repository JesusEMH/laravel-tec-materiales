<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\helpers\JwtAuth;
use App\Evento;


class eventosController extends Controller
{
	public function __construct(){
		$this->middleware('api.auth', ['except' => ['index', 'show', 'getStatus', 'getSolicitudByUser']]);
	}

    public function index(){
    	$eventos = Evento::all()->load('lugares')->load('users');

    	return response()->json([
    		'code' => 200,
    		'status' => 'success',
    		'eventos' => $eventos
    	], 200);
    }

    public function show($id){
    	$evento = Evento::find($id)->load('lugares')->load('users');

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
	    		$evento->hora_inicio = $params_array['hora_inicio'];
	    		$evento->hora_final = $params_array['hora_final'];
	    		$evento->fecha = $params_array['fecha'];
                $evento->status = "pendiente";
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
	    		'code' => 200,
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
        $evento = Evento::where('status', $status)->get();

        return response()->json([
            'status' => 'success',
            'evento' => $evento
        ],200);
    }


    public function getSolicitudByUser($id){
        $evento = Evento::where('usuario_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'evento' => $evento
        ], 200);
    }
}
