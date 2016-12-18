<?php

class DbManager {
  protected $connecions = [];
  protected $repositories = [];
  protected $repositoryConnectionMap = [];

  function connect($name, $params) {
    $params = array_merge([
      'dsn' => null,
      'user' => '',
      'password' => '',
      'options' => [],
    ], $params);

    $con = new PDO(
      $params['dsn'],
      $params['user'],
      $params['password'],
      $params['options']
    );

    $con->setAttribute(
      PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->connections[$name] = $con;
  }

  function getConnection($name = null) {
    if (is_null($name))
      return current($this->connections);
    return $this->connections[$name];
  }

  function setRepositoryConnectionMap($repositoryName, $name) {
    $this->repositoryConnectionMap[$repositoryName] = $name;
  }

  function getConnectionForRepository($repositoryName) {
    if (isset($this->repositoryConnectionMap[$repositoryName])) {
      $name = $this->repositoryConnectionMap[$repositoryName];
      $con = $this->getConnection($name);
    } else {
      $con = $this->getConnection();
    }
    return $con;
  }

  function get($repositoryName) {
    if (!isset($this->repositories[$repositoryName])) {
      $repositorClass = $repositoryName . 'Repository';
      $con = $this->getConnectionForRepository($repositoryName);
      $repository = new $repositoryClass($con);
      $this->repositories[$repositoryName] = $repository;
    }
    return $this->repositories[$repositoryName];
  }

  function __destruct() {
    foreach ($this->repositories as $repository) unset($repository);
    foreach ($this->connections as $connection) unset($connection);
  }
}
