<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
   
    public function evento(){
      return $this->hasMany('App\evento');
    }
}
