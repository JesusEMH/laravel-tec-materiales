<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    //relacion uno a muchos
    public function salidas(){
    	return $this->hasMany('App\Salida');
    }

    //relacion muchos a uno
    public function status(){
    	return $this->belongsTo('App\StatusVehiculo', 'status_id');
    }

}
