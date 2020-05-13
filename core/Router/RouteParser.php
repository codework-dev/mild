<?php

namespace Mild\Router;

class RouteParser
{
    public function parse(string $path)
    {
        $route = preg_replace('/\{(.*?)\}/','(?P<$1>[\w-]+)',$path);
        
        return $route;
    }
}