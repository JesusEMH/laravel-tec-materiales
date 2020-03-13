<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre', 'apellidos', 'numero_control', 'correo', 'contrasena',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'contrasena', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //relacion d euno a muchos
    public function eventos(){
      return $this->hasMany('App\Evento');
    }
    
    //relacion d euno a muchos
    public function mantenimiento(){
      return $this->hasMany('App\Mantenimiento');
    }

    //relacion de uno a muchos
    public function salidas(){
      return $this->hasMany('App\Mantenimiento');
    }

    //relacion de muchos a uno
    public function departamentos(){
        return $this->belongsTo('App\Departamento', 'departamento_id');
    }
}
