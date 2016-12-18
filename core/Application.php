<?php

abstract class Application {
  protected $debug = false;
  protected $loginAction = [];
  protected $request;
  protected $response;
  protected $session;
  protected $dbManager;

  function __construct(bool $debug) {
    $this->setDebugMode($debug);
    $this->initialize();
    $this->configure();
  }

  protected function setDebugMode(bool $debug): Application {
    if ($debug) {
      $this->debug = true;
      ini_set('display_errors', 1);
      error_reporting(-1);
    } else {
      $this->debug = false;
      ini_set('display_errors', 0);
    }
    return $this;
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

  function isDebugMode(): bool {
    return $this->debug;
  }

  function getRequest(): string {
    return $this->request;
  }

  function getResponse(): string {
    return $this->response;
  }

  function getSession(): string {
    return $this->session;
  }

  function getDbManager(): string {
    return $this->dbManager;
  }

  function getControllerDir(): string {
    return $this->getRootDir() . '/controllers';
  }

  function getViewDir(): string {
    return $this->getRootDir() . '/views';
  }

  function getModelDir(): string {
    return $this->getRootDir() . '/models';
  }

  function getWebDir(): string {
    return $this->getRootDir() . '/web';
  }

  function run() {
    try {
      $params = $this->router->resolve($this->request->getPathInfo());
      if ($params === false) {
        throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
      }

      $controller = $params['controller'];
      $action = $params['action'];
      $this->runAction($controller, $action, $params);
    } catch (HttpNotFoundException $e) {
      $this->render404Page($e);
    } catch (UnauthorizedActionException $e) {
      list($controller, $action) = $this->loginAction;
      $this->runAction($controller, $action);
    }
    $this->response->send();
  }

  function runAction($controllerName, $action, $params = []) {
    $controllerClass = ucfirst($controllerName) . 'Controller';
    $controller = $this->findController($controllerClass);
    if ($controller === false) {
      throw new HttpNotFoundException($controllerClass . ' controller is not found.');
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
    $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
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
