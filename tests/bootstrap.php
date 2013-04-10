<?php
require_once 'PHPUnit/Framework/Assert/Functions.php';

call_user_func(function () {
    $loader = require __DIR__ . '/../vendor/autoload.php';
    $loader->add('ngyuki\Tests', __DIR__);
});
