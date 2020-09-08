<?php

namespace Gmichelan2\Sistema_permisos\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //es: desde aqui
    //en: from here
    protected $fillable = [
        'name','slug', 'description',
    ];

    public function users(){//muchos a muchos
       return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function permissions(){//muchos a muchos
        return $this->belongsToMany('Gmichelan2\Sistema_permisos\Models\Permission')->withTimestamps();
        //dd($this->belongsToMany('App\Permission')->withTimestamps());
     }
}
