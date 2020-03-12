<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $fillable = [
        'titulo', 'contenido', 'lugar_id'
    ];

    //relacion de uno a muchos inversa(muchos a uno)
    public function user(){
    	return $this->belongsTo('App/User', 'usuario_id');
    }

    public function lugares(){
    	return $this->belongsTo('App/Lugar', 'lugar_id');
    }
}
