<?php

namespace core;

class Session {
  protected static $sessionStarted = false;
  protected static $sessionIdRegenerated = false;

  function __construct() {
    if (!self::$sessionStarted) {
      session_start();
      self::$sessionStarted = true;
    }
  }

  function set($name, $value) {
    $_SESSION[$name] = $value;
  }

  function get($name, $default = null) {
    if (isset($_SESSION[$name]))
      return $_SESSION[$name];
    return $default;
  }

  function remove($name) {
    unset($_SESSION[$name]);
  }

  function clear() {
    $_SESSION = [];
  }

  function regenerate($destroy = true) {
    if (!self::$sessionIdRegenerated) {
      session_regenerate_id($destroy);
      self::$sessionIdRegenerated = true;
    }
  }

  function setAuthenticated($bool) {
    $this->set('_authenticated', (bool)$bool);
    $this->regenerate();
  }

  function isAuthenticated() {
    return $this->get('_authenticated', false);
  }
}
