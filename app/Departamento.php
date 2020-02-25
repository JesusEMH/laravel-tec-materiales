<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    //relacion uno a muchos
    public function reporte(){
    	return $this->hasMany('App/Reporte');
    }
}
