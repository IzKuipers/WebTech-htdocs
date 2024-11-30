<?php

require_once "src/session.php";

$sessionManager->trySessionStart(); // Let's start the session
$_SESSION["toast"] = 4; // Set the 'you're logged out' toast message

$sessionManager->logout(); // Log the user out