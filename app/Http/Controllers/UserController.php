<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'nombre'          =>'required|alpha',
            'apellidos'       =>'required|alpha',
            'correo'          =>'required|email|unique:users',
            'contrasena'      =>'required',
            'numero_control'  =>'required',

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
            $user->nick = 'default';
            $user->numero_control = $params_array['numero_control'];

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
      $token = $request->header('Authorization');
      $jwtAuth = new \JwtAuth();
      $checkToken = $jwtAuth->checkToken($token);

      if($checkToken){
        echo "<h1>Login correcto</h1>";
      }else{
        echo "<h1>Login incorrecto</h1>";
      }

      die();
    }
}
