<?php

namespace core;

class View {
  protected $baseDir;
  protected $defaults;
  protected $layoutVariables = [];

  function __construct($baseDir, $defaults = []) {
    $this->baseDir = $baseDir;
    $this->defaults = $defaults;
  }

  function setLayoutVar($name, $value) {
    $this->layoutVariables[$name] = $value;
  }

  function render($_path, $_variables = [], $_layout = false) {
    $_file = $this->baseDir . '/' . $_path . '.php';
    extract(array_merge($this->defaults, $_variables));
    ob_start();
    ob_implicit_flush(0);
    require $_file;
    $content = ob_get_clean();
    if ($_layout) {
      $content = $this->render(
        $_layout,
        array_merge($this->layoutVariables, ['_content' => $content])
      );
    }
    return $content;
  }

  function excape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
  }
}
