<?php

abstract class Application {
  protected $debug = false;
  protected $request;
  protected $response;
  protected $session;
  protected $dbManager;

  function __construct($debug) {
    $this->setDebugMode($debug);
    $this->initialize();
    $this->configure();
  }

  protected function setDebugMode($debug) {
    if ($debug) {
      $this->debug = true;
      ini_set('display_errors', 1);
      error_reporting(-1);
    } else {
      $this->debug = false;
      ini_set('display_errors', 0);
    }
  }

  protected function initialize() {
    $this->request = new Request();
    $this->response = new Response();
    $this->session = new Session();
    $this->dbManager = new DbManager();
    $this->router = new Router($this->registerRoutes());
  }

  protected function configure() {

  }

  abstract function getRootDir();
  abstract protected function registerRoutes();

  function isDebugMode() {
    return $this->debug;
  }

  function getRequest() {
    return $this->request;
  }

  function getResponse() {
    return $this->response;
  }

  function getSession() {
    return $this->session;
  }

  function getDbManager() {
    return $this->dbManager;
  }

  function getControllerDir() {
    return $this->getRootDir() . '/controllers';
  }

  function getViewDir() {
    return $this->getRootDir() . '/views';
  }

  function getModelDir() {
    return $this->getRootDir() . '/models';
  }

  function getWebDir() {
    return $this->getRootDir() . '/web';
  }

  function run() {
    try {
      $params = $this->router->resolve($this->request->getPathInfo());
      if ($params === false) {

      }

      $controller = $params['controller'];
      $action = $params['action'];
      $this->runAction($controller, $action, $params);
    } catch (HttpNotFoundException $e) {
      $this->render404Page($e);
    }
    $this->response->send();
  }

  function runAction($controllerName, $action, $params = []) {
    $controllerClass = ucfirst($controllerName) . 'Controller';
    $controller = $this->findController($controllerClass);
    if ($controller === false) {
      
    }
    $content = $controller->run($action, $params);
    $this->response->setContent($content);
  }

  protected function findController($controllerClass) {
    if (!class_exists($controllerClass)) {
      $controllerFile = $this->getControllerDir() . '/' . $controllerClass . '.php';
      if (!is_readable($controller_file)) {
        return false;
      } else {
        require_once $controllerFile;
        if (!class_exists($controllerClass))
          return false;
      }
    }
    return new $controllerClass($this);
  }

  protected function render404Page($e) {
    $this->response->setStatusCode(404, 'Not Found');
    $message = $this->isDebugMode() ? $e->getMessag() : 'Page not found.';
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $this->response->setContent(<<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>404</title>
</head>
<body>
  {$message}
</body>
</html>
EOF
    );
  }
}
