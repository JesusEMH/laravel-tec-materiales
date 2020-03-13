<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';

    //relacion uno a muchos
    public function Usuario(){
    	return $this->hasMany('App\User');
    }
}
