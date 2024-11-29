<?php

require_once "../vendor/autoload.php";

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$logging = new Logger('klachtverwerking');
$logging->pushHandler(new StreamHandler('../info.log', Level::Info)); 