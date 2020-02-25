<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    //relacion de uno a muchos
    public function reporte(){
    	return $this->hasMany('App/Reporte');
    }
}
