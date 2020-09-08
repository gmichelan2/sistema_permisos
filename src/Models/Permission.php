<?php

namespace Gmichelan2\Sistema_permisos\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $fillable = [
        'name','slug', 'description',
    ];

    public function roles(){//muchos a muchos
        return $this->belongsToMany('Gmichelan2\Sistema_permisos\Models\Role')->withTimestamps();
     }

    public function users(){//pertenece a muchos usuarios, devuelve todos esos usuariosA
        return $this->belongsToMany('Gmichelan2\Sistema_permisos\Models\User')->withTimestapms();

    }
}
