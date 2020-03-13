<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusVehiculo extends Model
{
    protected $table = 'statusVehiculo';

    //relacion uno a muchos
    public function vehiculo(){
    	return $this->hasMany('App/Vehiculo');
    }

}
