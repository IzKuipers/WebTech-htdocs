<?php

require_once "vendor/autoload.php";
require_once "src/SessionManager.php";

$sessionManager->trySessionStart(); // Let's start the session
$_SESSION["toast"] = 4; // Set the 'you're logged out' toast message

$sessionManager->logout(); // Log the user out