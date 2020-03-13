<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
	protected $table = 'lugares';

   //relacion de uno a muchos
    public function evento(){
      return $this->hasMany('App\Evento');
    }

    //relacion de uno a muchos
    public function mantenimiento(){
      return $this->hasMany('App\Mantenimiento');
    }

    //relacion de muchos a uno
    public function ubicaciones(){
    	return $this->belongsTo('App\Ubicacion', 'ubicacion_id');
    }
}
