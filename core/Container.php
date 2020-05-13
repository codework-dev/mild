<?php

namespace Mild;

use Exception;
use ArrayAccess;
use ReflectionClass;

class Container implements ArrayAccess
{
    
    protected $bindings = [];
    protected $instances = [];
    
    public function bind($key,$value,$singleton = false)
    {
        $this->bindings[$key] = array('value' => $value,'singleton' => $singleton);
    }
    
    public function getBindings($key)
    {
        if(!array_key_exists($key,$this->bindings)){
            return null;
        }
        
        return $this->bindings[$key];
    }
    
    public function singleton($key,$value)
    {
        $this->bind($key,$value,true);
    }
    
    public function getSingletonInstance($key)
    {
        return $this->singletonResolved($key) ? $this->instances[$key] : null;
    }
    
    public function resolve($key,array $args = [])
    {
        $class = $this->getBindings($key);
        
        if(is_null($class)){
            $class = $key;
        }
        
        if($this->isSingleton($key) && $this->singletonResolved($key)){
            return $this->getSingletonInstance($key);
        }
        
        
        return $this->build($class,$args);
    }
    
    public function singletonResolved($key)
    {
        return array_key_exists($key,$this->instances);
    }
    
    public function isSingleton($key)
    {
        $bind = $this->getBindings($key);
        if(is_null($bind)){
            return false;
        }
        
        return $bind['singleton'] = true;
        
    }
    
    protected function build($classes,$args)
    {
        $className = $classes['value'];
        if(is_callable($className)){
            return $className($this);
        }

        $reflector = new ReflectionClass($className);
        
        if(!$reflector->isInstantiable()){
            throw new Exception('Class ['.$className.'] is not a resolvable dependency');
        }
        
        if($reflector->getConstructor() !== null){
            $constructor = $reflector->getConstructor();
            $dependencies = $constructor->getParameters();
        
        
            foreach($dependencies as $deb){
                if($deb->isOptional()) continue;
                if($deb->isArray()) continue;
                $class = $deb->getClass();
                if(is_null($class)) continue;
            
                if(get_class($this) === $class->name){
                    array_unshift($args,$this);
                    continue;
                }
                
                array_unshift($args,$this->resolve($class->name));
            }
        }
        
        
        return $reflector->newInstanceArgs($args);
        
    }
    
    public function offsetGet($key)
    {
        return $this->resolve($key);
    }
    
    public function offsetSet($key,$value)
    {
        $this->bind($key,$value);
    }
    
    public function offsetExists($key)
    {
        return array_key_exists($key,$this->bindings);
    }
    
    public function offsetUnset($key)
    {
        unset($this->bindings[$key]);
    }
    
}