<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    //relacion de uno a muchos inversa(muchos a uno)
    public function user(){
    	return $this->belongsTo('App/User', 'usuario_id');
    }

    public function categoria(){
    	return $this->belongsTo('App/Categoria', 'categoria_id');
    }
}
