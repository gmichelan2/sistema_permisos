@extends('SistemaPermisos::layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h2>Lista de Usuarios</h2></div>

                <div class="card-body">
                @include('SistemaPermisos::custom.message')
                @can('haveaccess','user.create')
                    <a href="{{route('user.create')}}" class="btn btn-primary float-right">Nuevo</a>
                    <br>
                @endcan
                <br>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nombres</th>
                            <th scope="col">Apellido</th>
                            <th scope="col">Nick</th>
                            <th scope="col">Rol(es)</th>
                            <th colspan="3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                             @foreach($users as $user)
                             <tr>
                                <th scope="row">{{$user->id}}</th>
                                <td>{{$user->name}}</td>
                                <td>{{$user->surname}}</td>
                                <td>{{$user->nick}}</td>
                                <td>
                                @isset($user->roles[0]->name)
                                    {{$user->roles[0]->name}}
                                @endisset    
                                </td>
                                <td>
                                @can('view',[$user, ['user.show','userown.show']])<!--Es con politica y no con gates-->
                                    <a class="btn btn-info" href="{{route('user.show', $user->id)}}">Mostrar</a>
                                @endcan
                                </td>
                                <td>
                                @can('update', [$user, ['user.edit','userown.edit']])
                                    @if($user->nick!='admin')
                                        <a class="btn btn-success" href="{{route('user.edit', $user->id)}}">Editar</a>
                                    @else
                                        <a class="btn btn-success disabled" href="{{route('user.edit', $user->id)}}">Editar</a>
                                    @endif
                                @endcan    
                                </td>
                                <td>
                                @can('haveaccess','user.destroy')
                                    @if($user->nick!=='admin')
                                        <form action="{{route('user.destroy', $user->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">Borrar</button>
                                        </form>
                                    @else
                                        <form action="{{route('user.destroy', $user->id)}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-secondary" disabled>Borrar</button>
                                        </form>
                                    @endif
                                @endcan
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                        </table>
                        {{$users->links()}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection