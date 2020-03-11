<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    //relacion uno a muchos
    public function salida(){
    	return $this->hasMany('App/Salida');
    }
}
