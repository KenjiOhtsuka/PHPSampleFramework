<?php

require 'core/ClassLoader.php';

$loader = new ClassLoader();
$loader->registerDir(__FILE__ . '/core');
$loader->registerDir(__FILE__ . '/models');
$loader->register();

