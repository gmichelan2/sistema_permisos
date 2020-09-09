<?php

namespace Gmichelan2\Sistema_permisos;
use Illuminate\Support\ServiceProvider;

class SistemaPermisosServiceProvider extends ServiceProvider
{
    public function register(){
        //en caso de que no funcione este archivo por defecto se debe ejecutar php artisan config:clear
            $this->mergeConfigFrom(
                __DIR__.'/../config/SistemaPermisos.php','SistemaPermisos'
            );
    }

    public function boot(){
        //cargar los datos de las migraciones

        $this->loadMigrationsFrom([
            __DIR__.'/../database/migrations'
        ]);

        //publicar migraciones
        $this->publishes([
            __DIR__.'/../database/migrations'=>database_path('migrations')
        ], 'SistemaPermisos-migrations');

        //publicar seeds
        $this->publishes([
            __DIR__.'/../database/seeds'=>database_path('seeds')
        ], 'SistemaPermisos-seeds');

        //publicar politicas y gates
        $this->publishes([
            __DIR__.'/../Policies'=>app_path('Policies')
        ], 'SistemaPermisos-policies');

        //cargas de rutas
        $this->loadRoutesFrom(
            __DIR__.'/../routes/web.php'
        );
        //cargas de vistas
        $this->loadViewsFrom(
            __DIR__.'/../resources/views','SistemaPermisos'//se le agrega un nombre para que no colisionen con las vistas normales
        );

        //publicar vistas
        $this->publishes([
            __DIR__.'/../resources/views'=> resource_path('view/vendor/SistemaPermisos'),'SistemaPermisos-view'
        ]);//van si o si dentro de /vendor/[namespace]

        //publicar configuración
        $this->publishes([
            __DIR__.'/../config/SistemaPermisos.php'=> config_path('SistemaPermisos.php'),'SistemaPermisos-config'
        ]);//van si o si dentro de /vendor/[namespace]

        //publicar el modelo de usuario
        $this->publishes([
            __DIR__.'Models/User.php'=>app_path(),'SistemaPermisos-userModel'
        ]);
    }
}
