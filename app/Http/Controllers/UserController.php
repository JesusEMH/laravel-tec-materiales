<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller
{
    public function prueba(Request $request){
      return "Accion de pruebas de USER-CONTROLLER";
    }

    public function register(Request $request){

      //recoger los datos del usuario en post
      $json = $request->input('json', null);
      $params = json_decode($json); //object
      $params_array = json_decode($json, true); //array


      if(!empty($params) && !empty($params_array)){
          //limpiar datos
          $params_array = array_map('trim', $params_array);

          //validar USUARIOS
          $validate  = \Validator::make($params_array, [
            'nombre'          =>'required',
            'apellidos'       =>'required',
            'correo'          =>'required|email|unique:users',
            'contrasena'      =>'required',
            'numero_control'  =>'required',
            'departamento_id' =>'required'

          ]);


          if($validate->fails()){
            $data = array(
              'status'  => 'error',
              'code'    => 404,
              'message' =>'el usuario no se ha creado',
              'errors'  => $validate->errors()
            );
          }else{
            //cifrar contrasena
          //  $pwd = password_hash($params->contrasena, PASSWORD_BCRYPT, ['cost' => 4]);
              $pwd = hash('sha256', $params->contrasena);

            //crear el usuario
            $user = new User();
            $user->nombre = $params_array['nombre'];
            $user->apellidos = $params_array['apellidos'];
            $user->correo = $params_array['correo'];
            $user->contrasena = $pwd;
            $user->rol = 'usuario';
            $user->numero_control = $params_array['numero_control'];
            $user->departamento_id = $params_array['departamento_id'];

            //guardar el usuario
            $user->save();

            $data = array(
              'status'  => 'success',
              'code'    =>  200,
              'message' => 'El usuario se ha creado correctamente',
              'user'    => $user
            );
          }

        }else{
          $data = array(
            'status'  => 'error',
            'code'    =>  404,
            'message' => 'los datos enviados no son correctos'
          );

        }


      return response()->json($data, $data['code']);


    }

    public function login(Request $request){
      $jwtAuth = new \JwtAuth();

      //recibir datos por post
      $json = $request->input('json', null);
      $params = json_decode($json);
      $params_array = json_decode($json, true);

      // validar esos datos
      $validate = \Validator::make($params_array, [
        'correo' => 'required|email',
        'contrasena' => 'required'
      ]);

      if($validate->fails()){
        $signup = array(
          'status' => 'error',
          'code' => 404,
          'message' => 'El usuario no se ha podido identificar',
          'errors' => $validate->errors()
        );
      }else{
        //cifrar la password
        $pwd = hash('sha256', $params->contrasena);

        //devolver token o datos
        $signup = $jwtAuth->signup($params->correo, $pwd);

        if(!empty($params->gettoken)){
          $signup = $jwtAuth->signup($params->correo, $pwd, true);
        }
      }

      return response()->json($signup, 200);
    }

    public function update(Request $request){
      //comporbar si el usuario esta identificado
      $token = $request->header('Authorization');
      $jwtAuth = new \JwtAuth();
      $checkToken = $jwtAuth->checkToken($token);        

      //recoger los datos por post
      $json = $request->input('json', null);
      $params_array = json_decode($json, true);

      if($checkToken && !empty($params_array)){

        //sacar el usuario identificado
        $user = $jwtAuth->checkToken($token, true);
        
        //validar datos
        $validate  = \Validator::make($params_array, [
            'nombre'          =>'required',
            'apellidos'       =>'required',
            'correo'          =>'required|email|unique:users'.$user->sub,
            'departamento_id' =>'required',
            'descripcion'     =>'required'

          ]);


        //quitar los campos que no quiero actualizar

        unset($params_array['id']);
        unset($params_array['contrasena']);
        unset($params_array['rol']);
        unset($params_array['numero_control']);
        unset($params_array['created_at']);
        unset($params_array['remember_token']);

        //actualizar usuaro en la base de datos

        $user_update = User::where('id', $user->sub)->update($params_array);


        //devolver array con resultado
        $data = array(
          'code' => 200,
          'status' => 'success',
          'user' => $user,
          'changes' => $params_array

        );

      }else{
        $data = array(
          'code' => 400,
          'status' => 'error',
          'message' => 'El usuario no esta identificado'
        );
      }

      return response()->json($data, $data['code']);
    }


    public function upload(Request $request){

      //recoger datos de la peticion
      $image = $request->file('file0');

      //validar imagen
      $validate = \Validator::make($request->all(), [
        'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
      ]);

      //guardar imagen
      if(!$image || $validate->fails()){

        $data = array(
          'code' => 400,
          'status' => 'error',
          'message' => 'Error al subir la imagen'
        );


      }else{

        $image_name = time().$image->getClientOriginalName();
        \Storage::disk('users')->put($image_name, \File::get($image));

        $data = array(
          'code' => 200,
          'status' => 'success',
          'image' => $image_name
        );
      }


        return response()->json($data, $data['code']);

    }

    public function getImage($filename){

      $isset = \Storage::disk('users')->exists($filename);

      if($isset){

        $file = \Storage::disk('users')->get($filename);
        return new Response($file, 200);

      }else{

        $data = array(
          'code' => 404,
          'status' => 'error',
          'message' => 'la imagen no existe'

        );

        return response()->json($data, $data['error']);
      }

    }

    public function detail($id){

      $user = User::find($id);

      if(is_object($user)){
        $data = array(
          'code' => 200,
          'status' => 'success',
          'user' => $user

        );
      }else{

        $data = array(
          'code' => 404,
          'status' => 'error',
          'message' => 'el usuario no existe'

        );

      }

      return response()->json($data, $data['code']);
    }
}
