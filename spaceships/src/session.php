<?php

require_once "auth.php";


class SessionManager
{
  public string $token;

  public function __construct()
  {
    if (!isset($_COOKIE["token"]))
      return;

    $this->token = $_COOKIE["token"];
  }

  public function checkIfLoggedIn()
  {
    global $authManager;

    if (!isset($_COOKIE["token"])) {
      $this->logout();

      return;
    }

    $token = $_COOKIE["token"];
    $tokenValid = $this->isLoggedIn($token);

    if (!$tokenValid) {
      $this->logout();
    }
  }

  public function isLoggedIn($token)
  {
    global $authManager;

    return $authManager->validateToken($token);
  }

  public function logout()
  {
    setcookie("token", "", time() - 3600);

    header("Location:login.php");
  }

  public function me()
  {
    global $authManager;

    if (!isset($this->token))
      return null;

    return $authManager->getUserByToken($this->token);
  }
}

$sessionManager = new SessionManager();