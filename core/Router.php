<?php

class Router {
  protected $routes;

  function __construct($definitions) {
    $this->routes = $this->compileRoutes($definitions);
  }

  function compileRoutes($definitions) {
    $routes = [];

    foreach ($definitions as $url => $params) {
      $tokens = explode('/', ltrim($url, '/'));
      if (0 === strpos($token, ':')) {
        $name = substr($token, 1);
        $token = '(?P<' . $name . '>[^/]+)';
      }
      $tokens[$i] = $token;
    }
    $pattern = '/' . implode('/', $tokens);
    $routes[$pattern] = $params;

    return $routes;
  }

  function resolve($pathInfo) {
    if ('/' !== substr($pathInfo, 0, 1)) {
      $pathInfo = '/' . $pathInfo;
    }

    foreach ($this->routes as $pattern => $params) {
      if (preg_match('#^' . $pattern . '$#', $pathInfo, $matches)) {
        $params = array_merge($params, $matches);
        return $params;
      }
    }

    return false;
  }
}
