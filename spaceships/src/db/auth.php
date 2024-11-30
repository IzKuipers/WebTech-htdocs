<?php

require_once __DIR__ . "/../../env.php";
include_once "dbmodule.php";
require_once __DIR__ . "/../error.php";

use Ramsey\Uuid\Uuid;

class AuthorizationManager extends DatabaseModule
{
  public function __construct()
  {
    parent::__construct();
  }

  ////////////
  // TOKENS //
  ////////////

  public function createToken(array $user)
  {
    $token = Uuid::uuid4()->toString();

    try {
      $connection = $this->connect();
      $query = "INSERT INTO tokens(value,userId) VALUES (?,?)";
      $statement = $connection->prepare($query);

      $statement->bind_param("si", $token, $user["id"]);
      $statement->execute();

      return $token;
    } catch (Exception $e) {
      return "";
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function validateToken(string $token): bool
  {
    try {
      $connection = $this->connect();
      $query = "SELECT userId,id FROM tokens WHERE value = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $token);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0) {
        return false;
      }

      return true;
    } catch (Exception $e) {
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function discontinueToken(string $token): bool
  {
    try {

      $connection = $this->connect();
      $query = "DELETE FROM tokens WHERE value = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $token);
      $statement->execute();

      return true;
    } catch (Exception $e) {
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  ////////////////////
  // AUTHENTICATION //
  ////////////////////

  public function authenticateUser(string $username, string $password): string
  {
    try {
      $connection = $this->connect();
      $query = "SELECT username,hash,id FROM users WHERE username = ?";
      $statement = $connection->prepare($query);

      $statement->bind_param("s", $username);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0) {
        Dialog(ErrorMessages::UserNotFound);

        return "";
      }

      $row = $result->fetch_assoc();
      $hash = $row["hash"];

      $passwordValid = $this->verifyPassword($hash, $password);

      if (!$passwordValid) {
        Dialog(ErrorMessages::PasswordIncorrect);

        return "";
      }

      $token = $this->createToken($row);

      return $token;
    } catch (Exception $e) {
      return "";
    }
  }


  public function verifyPassword(string $hash, string $password): bool
  {
    return password_verify($password, $hash);
  }

  ///////////////////
  // USER MUTATION //
  ///////////////////

  public function createUser($username, $password)
  {
    $safeUsername = htmlspecialchars(trim($username));
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO users(username,hash) VALUES (?,?)";

    try {
      global $connection, $statement;
      $connection = $this->connect();
      $statement = $connection->prepare($query);
      $statement->bind_param("ss", $safeUsername, $hash);
      $statement->execute();
      return true;
    } catch (Exception $e) {
      Dialog(ErrorMessages::UserAlreadyExists);
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function deleteUser(string $username)
  {
    try {
      global $connection, $statement;
      $connection = $this->connect();
      $query = "DELETE FROM users WHERE username = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $username);
      $statement->execute();

      return true;
    } catch (Exception $e) {
      return false;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  /////////////
  // GETTING //
  /////////////

  public function getUserByToken(string $token)
  {
    try {
      $connection = $this->connect();
      $query = "SELECT userId FROM tokens WHERE value = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $token);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0)
        return null;

      $row = $result->fetch_assoc();
      $userId = (int) $row["userId"];

      return $this->getUserById($userId);
    } catch (Exception $e) {
      return null;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function getUserById(int $userId)
  {
    try {
      $connection = $this->connect();
      $query = "SELECT * FROM users WHERE id = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("i", $userId);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0)
        return null;

      $row = $result->fetch_assoc();

      return $row;
    } catch (Exception $e) {
      return null;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }


  public function getUserByName(string $username)
  {
    try {
      $connection = $this->connect();
      $query = "SELECT * FROM users WHERE username = ?";
      $statement = $connection->prepare($query);
      $statement->bind_param("s", $username);
      $statement->execute();
      $result = $statement->get_result();

      if ($result->num_rows == 0)
        return null;

      $row = $result->fetch_assoc();

      return $row;
    } catch (Exception $e) {
      return null;
    } finally {
      $this->disconnect($connection, $statement);
    }
  }

  public function resetUser(array $user)
  {
    $userId = $user["id"];

    try {
      $connection = $this->connect();
      $query = "DELETE FROM tokens WHERE userId = $userId; DELETE FROM ships WHERE authorId = $userId;";
      $connection->multi_query($query);

      return true;
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    } finally {
      $this->disconnect($connection);
    }
  }
}

$authManager = new AuthorizationManager();