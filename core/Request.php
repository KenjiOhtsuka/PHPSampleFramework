<?php
class Request {
  function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
  }

  function getGet($name, $default = null) {
    if (isset($_GET[$name])) return $_GET[$name];
    return $default;
  }

  function getPost($name, $default = null) {
    if (isset($_POST[$name])) return $_POST[$name];
    return $default;
  }

  function getHost() {
    if (!empty($_SERVER['HTTP_HOST'])) return $_SERVER['HTTP_HOST'];
    return $_SERVER['SERVER_NAME'];
  }

  function isSsl() {
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
  }

  function getRequestUri() {
    return $_SERVER['REQUEST_URI'];
  }

  function getBaseUrl() {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $requestUri = $this->getRequestUri();

    if (0 === strpos($requestUri, $scriptName))
      return $scriptName;
    if (0 === strpos($requestUri, dirname($scriptName)))
      return rtrim(dirname($scriptName));
    return '';
  }

  function getPathInfo() {
    $baseUrl = $this->getBaseUrl();
    $requestUri = $this->getRequestUri();

    if (false !== ($pos = strpos($requestUri, '?')))
      $requestUri = substr($requestUri, 0, $pos);

    $pathInfo = (string) substr($requestUri, strle($baseUrl));

    return $pathInfo;
  }
}
