<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'titulo', 'contenido', 'lugar_id'
    ];

    //relacion de uno a muchos inversa(muchos a uno)
    public function users(){
    	return $this->belongsTo('App\User', 'usuario_id');
    }

    public function lugares(){
    	return $this->belongsTo('App\Lugar', 'lugar_id');
    }
}
