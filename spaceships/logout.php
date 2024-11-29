<?php

require_once "src/session.php";

$sessionManager->trySessionStart();
$_SESSION["toast"] = 4;

$sessionManager->logout();