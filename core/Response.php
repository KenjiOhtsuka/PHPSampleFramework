<?php

class Response {
  protected $content;
  protected $statusCode = 200;
  protected $statusText = 'OK';
  protected $httpHeaders = [];

  function send() {
    header('HTTP/1.1 ' . $this->statusCode . ' ' . $this->statusText);
    foreach ($this->httpHeaders as $name => $value) {
      header($name . ': ' . $value);
    }
    echo $this->content;
  }

  function setContent($content): Response {
    $this->content = $content;
    return $this;
  }

  function setStatusCode($statusCode, $statusText = ''): Response {
    $this->statusCode = $statusCode;
    $this->statusText = $statusText;
    return $this;
  }

  function setHttpHeader($name, $value): Response {
    $this->httpHeaders[$name] = $value;
    return $this;
  }
}
