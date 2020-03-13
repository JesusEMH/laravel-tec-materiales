<?php
namespace App\helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

  public $key;

  public function __construct(){
    $this->key = 'esto_es_una_clave_super_secreta_9988787876';
  }

  public function signup($correo, $contrasena, $getToken = null){
    //buscar si existe el usuario con las credenciales
    $user = User::where([
      'correo' => $correo,
      'contrasena' => $contrasena
    ])->first();

    //comprobar si son correctas(objeto)
    $signup = false;
    if(is_object($user)){
      $signup = true;
    }
    //generar el token con los datos del usuario identificado
    if($signup){
      $token = array(
        "sub" => $user->id,
        "nombre" => $user->nombre,
        "apellidos" => $user->apellidos,
        "rol" => $user->rol,
        "correo" => $user->correo,
        "telefono" => $user->telefono,
        "numero_control" => $user->numero_control,
        "departamento_id" => $user->departamento_id,
        "descripcion" => $user->descripcion,
        "imagen" => $user->imagen,
        "iat" => time(),
        "exp" => time() + (7 * 24 * 60 * 60)
      );

      $jwt = JWT::encode($token, $this->key, 'HS256');
      $decoded = JWT::decode($jwt, $this->key, ['HS256']);

      //devolver los datos de codificados o el token en funcion de un parametro

      if(is_null($getToken)){
        $data = $jwt;
      }else{
        $data = $decoded;
      }


    }else{
      $data = array(
        'status' => 'error',
        'message' => 'Login incorrecto'
      );
    }
    return $data;
    }

    public function checkToken($jwt, $getIdentity = false){
      $auth = false;

      try{
        $jwt = str_replace('"', '', $jwt);
        $decoded = JWT::decode($jwt, $this->key, ['HS256']);
      }catch(\UnexpectedValueException $e){
        $auth = false;
      }catch(\DomainException $e){
        $auth = false;
      }

      if(!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
        $auth = true;
      }else{
        $auth = false;
      }

      if($getIdentity){
        return $decoded;
      }

      return $auth;
    }
  }



 ?>
