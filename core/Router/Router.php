<?php

namespace Mild\Router;

use Exception;
use Mild\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Router
{
    
    protected $routes = [];
    
    protected $app;
    protected $controllerNamespace;
    
    public function __construct(Application $app)
    {
        $app->bind('routeparser','Mild\Router\RouteParser');
        $this->app = $app;
        
    }
    
    public function add($methods,$path,$callback)
    {
        $namespace = null;
        $route = $this->app['routeparser']->parse($path);
        if(isset($callback['namespace'])){
            $namespace = $callback['namespace'];
        }
        foreach((array) $methods as $method){
            $this->routes[$namespace] = [
                'method'   => $method,
                'path'     => $route,
                'callback' => $callback
            ];
        }
        
        return $this;
        
    }
    
    public function dispatcher()
    {
        $request = $this->app['request'];
        $vars = [];
        $uri = $request->getRequestUri();
        $method = $request->getMethod();
        
        foreach($this->routes as $name => $routes){
            if(preg_match('~^'.$routes['path'].'$~',$uri,$match)){
                $found = 1;
                $routesMatch[] = array_merge($routes,$match);
                foreach($match as $mk => $mv){
                    if(is_string($mk)){
                        $vars[$mk] = $mv;
                    }
                }
                
                if(strtoupper($routes['method']) === $method){
                    $foundMethod = 1;
                }
                
            }
        }
        
        switch($found){
            case 1:
                //found
                switch($foundMethod){
                    case 1:
                     //condition
                     list($controllerName,$action) = explode(
                         '@',$this->controllerNamespace.$routesMatch[0]['callback']['controller']);
                     $this->app[
                         $routeMatch[0]['callback']['namespace']
                         ] = $controllerName;
                     
                     $response = new Response(
                          call_user_func_array([
                            $this->app[
                                $routeMatch[0]['callback']['controller']
                                ],$action  
                            ],array_values($vars))
                        );
                        
                    $response->send();
                    break;
                    default:
                        throw new Exception('Method Not Allowed!');
                }
            break;
            default:
                throw new Exception('Pages Not Found!');
        }
        
        
    }
    
    public function addNamespaceForController($name)
    {
        $this->controllerNamespace = $name;
    }
    
    public function redirect($to,$code = 302,array $headers = [])
    {
        return new RedirectResponse($this->routes[$to]['path'],$code,$headers);
    }
    
    
    
}