<?php

abstract class DbManager {
  protected $connection;

  function __construct($connection) {
    $this->setConnection($connection);
  }

  function setConnection($connection) {
    $this->connection = $connection;
  }

  function execute($sql, $param = []) {
    $stmt = $this->connection->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  function fetch($sql, $params = []) {
    return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
  }

  function fetchAll($sql, $params = []) {
    return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
  }
}
