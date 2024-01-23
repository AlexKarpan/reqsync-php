#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use ReqSync\ConsoleApp;

$app = new ConsoleApp();
$app->run($argv);
