<?php

// define routes

$routes = array(
    array(
        "pattern" => "features",
        "controller" => "home",
        "action" => "features"
    ),
    array(
        "pattern" => "home",
        "controller" => "home",
        "action" => "index"
    ),
    array(
        "pattern" => "logout",
        "controller" => "auth",
        "action" => "logout"
    ),
    array(
        "pattern" => "dmca",
        "controller" => "home",
        "action" => "dmca"
    ),
    array(
        "pattern" => "users/manage",
        "controller" => "auth",
        "action" => "manageUsers"
    )
);

// add defined routes
foreach ($routes as $route) {
    $router->addRoute(new Framework\Router\Route\Simple($route));
}

// unset globals
unset($routes);
