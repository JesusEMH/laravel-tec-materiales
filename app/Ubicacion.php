<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = 'ubicaciones';

    //relacion uno a muchos
    public function lugar(){
    	return $this->hasMany('App/Lugar');
    }

}
