<?php

require 'core/ClassLoader.php';

$loader = new \core\ClassLoader();
$loader->registerDir(__DIR__);
#$loader->registerDir(__DIR__ . '/core');
#$loader->registerDir(__DIR__ . '/models');
$loader->register();

