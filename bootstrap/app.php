<?php

$app = new Mild\Application();

//register the request
$app['request'] = function($c){
    return Symfony\Component\HttpFoundation\Request::createFromGlobals();
};

$app->router->addNamespaceForController('\\App\\Controllers\\');

require __DIR__ . '/../app/routes.php';


return $app;