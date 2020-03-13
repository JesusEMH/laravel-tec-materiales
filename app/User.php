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
    //relacion de uno a muchos
    public function reporte(){
      return $this->hasMany('App\Reporte');
    }

    //relacion d euno a muchos
    public function evento(){
      return $this->hasMany('App\Evento');
    }
    
    //relacion de muchos a uno
    public function departamento(){
        return $this->belongsTo('App/Departamento', 'departamento_id');
    }
}
