<?php

namespace App\Controllers;

use Mild\Application;

class Controller
{
    
    protected $app;
    
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    public function __get($property)
    {
        if(isset($this->app->{$property})){
            return $this->app->{$property};
        }
        return $this->app->resolve($property);
    }
}