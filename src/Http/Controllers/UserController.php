<?php

namespace Gmichelan2\Sistema_permisos\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gmichelan2\Sistema_permisos\Models\Role;
use App\User;
use Gmichelan2\Sistema_permisos\Models\Permission;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $this->authorize('haveaccess','user.index');
        $users= User::with('roles')->orderBy('id', 'Desc')->paginate(10);
        return view('SistemaPermisos::user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create',User::class);//usamos el método create de la politica y le pasamos el modelo
        $roles = Role::get(); //igual que all()
        return view('SistemaPermisos::user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        $this->authorize('store', User::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'surname'=>'required|string|max:255',
            'email'=>'required|email',
            'nick' =>'required|string|max:255|unique:users,nick',
            'password' => 'required|string|min:8|confirmed',
            'role'=>'required',
        ]);

        $request->merge(['password'=>Hash::make($request->get('password'))]);
        $user=User::create($request->all());
        

        //al crear el nuevo usuario debo meterle los permisos del role asignado en la tabla permission_user
        $user->roles()->sync($request->get('role'));
        $permisos=[];
        foreach($user->roles as $role){//por cada rol tomo los id de los permisos
            foreach($role->permissions as $perm){//tomo los permisos de cada rol
                if(!in_array(strval($perm->id), $permisos)){
                    $permisos[]=$perm->id;
                }
            }
        }

        $user->permissions()->sync($permisos);//sincronizo los permisos obtenidos

        return redirect()->route('user.index')->with('status_success','Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        $this->authorize('view', [$user, ['user.show','userown.show']]);//política le paso varias variables

        $permission_user=[];
        foreach($user->permissions()->get() as $permission){
            $permission_user[]=$permission->id;//id de los permisos del usuario
        }

        $role_user=[];
        foreach($user->roles as $role){
            $role_user[]=$role->id;
        }

        $roles = Role::orderBy('name')->get(); //igual que all() todos los roles declarados
        $permissions= Permission::get();//todos lso permisos declarados


        return view('SistemaPermisos::user.view', compact('roles', 'user','permissions', 'permission_user','role_user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
        $this->authorize('update', [$user, ['user.edit','userown.edit']]);//política le paso varias variables

        $permission_user=[];
        foreach($user->permissions()->get() as $permission){
            $permission_user[]=$permission->id;//id de los permisos del usuario
        }

        $role_user=[];
        foreach($user->roles as $role){
            $role_user[]=$role->id;
        }

        $roles = Role::orderBy('name')->get(); //igual que all()
        $permissions= Permission::get();//todos lso permisos declarados


        return view('SistemaPermisos::user.edit', compact('roles', 'user', 'permissions', 'permission_user', 'role_user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    { 
        //dd(Auth::user());
        //hay que tener en cuenta si el usuario que edita es admin o es user comun
        if(Auth::user()->isAdmin()){
            $request->validate(
                [
                'name'=>'required|max:50|unique:users,name,'.$user->id,
                'surname'=>'max:50|unique:users,surname,'.$user->id,
               // 'nick'=>'required|max:50|unique:users,nick,'.$user->id,
                'role'=>'required'//que el admin nunca se olvide de seleccionar un rol aunque sea el básico
            ]);
    
            $user->update($request->all());//colocas los atributos fillables en los datos del usuario
            $user->roles()->sync($request->get('role'));//sincronizas los roles del usuario 

            //ahora hay que sincronizar los permisos del usuario, tanto si estan dentro de un rol o si le agregaron permisos individuales
            $perm=$request->get('permission');
            //dd($request);
            foreach($user->roles as $role){
                foreach($role->permissions as $permission){
                    if(!in_array("'".$permission->id."'", $perm)){
                        $perm[]=$permission->id;
                    }
                }
            }

            //ya obtenidos todos los permisos sincronizo de nuevo todos los permisos del usuario
            //dd($perm);
            $user->permissions()->sync($perm);

            //redirecciona al index ya que tiene acceso por ser admin o tener permiso de admin
            return redirect()->route('user.show', compact('user'))->with('status_success','Usuario actualizado exitosamente');
        }
        else{//es user comun solo edita su info personal
            $request->validate(
                [
                'name'=>'required|max:50|unique:users,name,'.$user->id,
                'surname'=>'max:50|unique:users,surname,'.$user->id,
                //'nick'=>'required|max:50|unique:users,nick,'.$user->id,
            ]);

            $user->update($request->all());
            //redirecciona solo a su info personal
            return redirect()->route('user.show', compact('user'))->with('status_success','Tus datos han sido actualizados exitosamente');
        }
    }

    public function editContraseña(User $user){
        //dd($user);
        $this->authorize('update', [$user, ['user.edit','userown.edit']]);//política le paso el usuario y los slugs de los permisos
        
        return view('SistemaPermisos::user.password', compact('user'));
    }

    public function updateContraseña(Request $request , User $user){
        //dd($request);
        $request->validate(
            [
            'contrasenia'=>'required|min:8|same:repetircontrasenia',
            'repetircontrasenia'=>'required|min:8',
        ]);

        $request->merge(['contrasenia'=>Hash::make($request->get('contrasenia'))]);

        $updatedUser=User::find($user->id);
        $updatedUser->password=$request->get('contrasenia');
        $updatedUser->save();

        return redirect()->route('user.show', compact('user'))->with('status_success', 'Contraseña modificada');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
        $this->authorize('haveaccess','user.destroy');
        $user->delete();

        return redirect()->route('user.index')->with('status_success','Usuario eliminado exitosamente');
    }
}
