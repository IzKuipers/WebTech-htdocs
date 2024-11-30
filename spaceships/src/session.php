<?php

require_once "db/auth.php";

/**
 * SESSION MANAGER
 * 
 * This class is responsible for verifying the logged in state, for logging out the user, and
 * getting the user's data by their token. It's also used for account resetting and deletion,
 * and $_SESSIONs.
 */

class SessionManager
{
  public string $token; // The user's token

  public function __construct()
  {
    // No token in the cookies? Stop.
    if (!isset($_COOKIE["token"]))
      return;

    // Store the token
    $this->token = $_COOKIE["token"];
  }

  // This function checks if the user is logged in
  public function checkIfLoggedIn()
  {
    global $authManager; // AuthorizationManager

    $this->trySessionStart(); // Try to start the session

    // No token in cookies? Set 'you have to be logged in' toast and log out.
    if (!isset($_COOKIE["token"])) {
      $_SESSION['toast'] = 6;
      $this->logout();

      return;
    }

    $token = $_COOKIE["token"]; // Get the token
    $tokenValid = $this->isLoggedIn($token); // Verify the token

    // Token invalid? Log out (in case of deletion)
    if (!$tokenValid) {
      $this->logout();
    }

    // Token is valid, no need for further action.
  }

  // This function simply verifies a provided token by calling validateToken() of the AuthorizationManager.
  public function isLoggedIn($token)
  {
    global $authManager; // AuthorizationManager

    return $authManager->validateToken($token); // Validate token and return the result
  }

  // This function logs the user out
  public function logout()
  {
    global $authManager; // AuthorizationManager

    $authManager->discontinueToken($this->token); // Discontinue the token
    setcookie("token", "", time() - 3600); // Delete the token

    header("Location:login.php"); // Go to the login
  }

  // This function returns the user data by the stored token
  public function me()
  {
    global $authManager; // AuthorizationManager

    if (!isset($this->token)) // No token? No user data.
      return [];

    // Get the user by their token and return the result.
    return $authManager->getUserByToken($this->token);
  }

  // This function starts the session only if it isn't already started.
  public function trySessionStart()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  // This function deletes the user only if 'rmacc' was requested.
  public function checkForRmAccRequest()
  {
    global $authManager; // AuthorizationManager

    // No rmacc? Stop.
    if (!isset($_GET["rmacc"]))
      return;

    $authManager->deleteUser($this->me()["username"]); // Delete the user
    header("Location: login.php"); // Go to the login
  }

  // This function resets the user only if 'accreset' was requested.
  public function checkForAccResetRequest()
  {
    global $authManager; // AuthorizationManager

    // No accreset? Stop.
    if (!isset($_GET["accreset"]))
      return;

    // Get the user
    $me = $this->me();

    // No user somehow? Stop.
    if (!$me)
      return;

    $authManager->resetUser($me); // Reset the user data
    $this->logout(); // Log out since the token got invalidated in the process
  }
}

$sessionManager = new SessionManager();