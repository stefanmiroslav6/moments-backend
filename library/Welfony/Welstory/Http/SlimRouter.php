<?php

// ==============================================================================
//
// This file is part of the WelStory.
//
// Create by Welfony Support <support@welfony.com>
// Copyright (c) 2012-2014 welfony.com
//
// For the full copyright and license information, please view the LICENSE
// file that was distributed with this source code.
//
// ==============================================================================

namespace Welfony\Welstory\Http;

Class SlimRouter
{
    protected $routes;
    protected $request;

    public function __construct()
    {
        $env = \Slim\Environment::getInstance();
        $this->request = new \Slim\Http\Request($env);
        $this->routes = array();
    }

    public function addRoutes($routes)
    {
        foreach ($routes as $route => $path) {
            $method = "any";

            if (strpos($path, "@") !== false) {
                list($path, $method) = explode("@", $path);
            }

            $func = $this->processCallback($path);

            $r = new \Slim\Route($route, $func);
            $r->setHttpMethods(strtoupper($method));

            array_push($this->routes, $r);
        }
    }

    public function set404Handler($path)
    {
        $this->errorHandler = $this->processCallback($path);
    }

    public function run()
    {
        $display404 = true;

        $uri = $this->request->getResourceUri();
        $method = $this->request->getMethod();

        foreach ($this->routes as $i => $route) {
            if ($route->matches($uri)) {
                if ($route->supportsHttpMethod($method) || $route->supportsHttpMethod("ANY")) {
                    call_user_func_array($route->getCallable(), array_values($route->getParams()));
                    $display404 = false;
                }
            }
        }

        if ($display404) {
            if (is_callable($this->errorHandler)) {
                call_user_func($this->errorHandler);
            } else {
                echo "404 - route not found";
            }
        }
    }

    protected function processCallback($path)
    {
        $class = "Index";

        if (strpos($path, ":") !== false) {
            list($class, $path) = explode(":", $path);
        }

        $function = ($path != "") ? $path : "index";

        $func = function () use ($class, $function) {
            $class = '\Welfony\Welstory\Controller\API\\' . $class . 'Controller';
            $class = new $class();

            $args = func_get_args();

            return call_user_func_array(array($class, $function), $args);
        };

        return $func;
    }

}