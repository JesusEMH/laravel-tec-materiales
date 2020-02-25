<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes';

    //relacion de uno a muchos inversa (muchos a uno)
    public function user(){
    	return $this->belongsTo('App/User', 'usuario_id');
    }

    public function categoria(){
    	return $this->belongsTo('App/User', 'categoria_id');
    }
}
