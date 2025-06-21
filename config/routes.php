<?php

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return function (RouteBuilder $routes): void {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder): void {
        $builder->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
        $builder->connect('/pages/*', 'Pages::display');
        $builder->fallbacks();
    });

    // ✅ APIルート（修正済み）
    $routes->prefix('api', function (RouteBuilder $builder) {
        $builder->setExtensions(['json']);

        // LoginControllerへのルート
        $builder->connect('/login', ['controller' => 'Login', 'action' => 'index']);

        $builder->connect('/me', ['controller' => 'Me', 'action' => 'index']);

        $builder->resources('Posts');

        // ✅ これを追加することで UsersController がREST対応になる！
        $builder->resources('Users');

        $builder->fallbacks(DashedRoute::class);
    });
};