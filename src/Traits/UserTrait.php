<?php

namespace Gmichelan2\Sistema_permisos\Traits;

trait UserTrait{
    public function roles(){//muchos a muchos devuelve todos los roles del usuario
        return $this->belongsToMany('Gmichelan2\Sistema_permisos\Models\Role')->withTimestamps();
     }

    public function havePermission($permission){
        foreach($this->roles as $role){//recorremos los roles que tiene el usuario
            foreach($role->permissions()->get() as $perm){
                if($perm->slug==$permission){
                    return true;
                }
            }
        }
        return false;
    }

    public function permissions(){//devuelve todos los permisos del usuario
        return $this->belongsToMany('Gmichelan2\Sistema_permisos\Models\Permission')->withTimestamps();
    }

    public function hasPermission2($perm){//este método deberia sustituir el havePermission ya qeu ahorra buscar por rol
        foreach($this->permissions as $permission){
            if($permission->slug==$perm){
                return true;
            }

        }
        return false;
    }

    public function isAdmin(){
        foreach($this->roles as $role){
            if($role->slug =='adminfullaccess'){
                return true;
            }
        }
        return false;
    }

}