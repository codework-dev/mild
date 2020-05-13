<?php

namespace Mild;

use Mild\Container;
use Mild\Router\Router;
use Whoops\Run as Whoops;
use Whoops\Handler\PrettyPageHandler;

class Application extends Container
{
    
    public $router;
    
    public function __construct()
    {
        $this->errorHandler();
        $this->initView();
        $this->initRouter();
    }
    
    public function initView()
    {
        $this->bind('view','Mild\View\ViewFactory');
    }
    
    public function errorHandler()
    {
        
        if ($_SERVER['APP_DEBUG']) {
            $whoops = new Whoops;
            $whoops->pushHandler(new PrettyPageHandler);
            $whoops->register();
        }
        
    }
    
    public function initRouter()
    {
        $this->router = new Router($this);
    }
    
    public function get($path,$callback)
    {
        $this->router->add('GET',$path,$callback);
    }
    
    public function run()
    {
        $this->router->dispatcher();
    }
    
}