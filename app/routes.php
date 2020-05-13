<?php

$app->get('/',[
    'namespace'     => 'home',
    'controller'    => 'HomeController@index'
]);


$app->get('/auth/signup',[
    'namespace'     => 'signup',
    'controller'    => 'AuthController@signup'
]);

