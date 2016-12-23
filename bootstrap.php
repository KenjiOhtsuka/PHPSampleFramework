<?php
declare(strict_types=1);

require 'core/ClassLoader.php';

$loader = new \core\ClassLoader();
$loader->registerDir(__DIR__);
$loader->register();

