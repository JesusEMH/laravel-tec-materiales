<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
	protected $table = 'salidas';

    //relacion de uno a muchos inversa(muchos a uno)
    public function users(){
    	return $this->belongsTo('App\User', 'usuario_id');
    }

    public function vehiculos(){
    	return $this->belongsTo('App\Vehiculo', 'vehiculo_id');
    }
