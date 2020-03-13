<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
   //relacion de uno a muchos
    public function evento(){
      return $this->hasMany('App\evento');
    }

    //relacion de muchos a uno
    public function ubicacion(){
    	return $this->belongsTo('App/Ubicacion', 'ubicacion_id');
    }
}
