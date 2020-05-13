<?php

require_once __DIR__.'/../vendor/autoload.php';


$_SERVER['APP_DEBUG'] = true;

$app = require_once __DIR__.'/../bootstrap/app.php';



$app->run();