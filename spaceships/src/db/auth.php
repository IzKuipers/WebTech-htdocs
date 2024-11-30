<?php

require_once __DIR__ . "/../../env.php";
include_once "dbmodule.php";
require_once __DIR__ . "/../ui/error.php";

use Ramsey\Uuid\Uuid;

/**
 * AUTHORIZATION MANAGER
 * 
 * This class takes care of everything user related, from authorization to mutational operations.
 */

class AuthorizationManager extends DatabaseModule // It's a derivative of the DatabaseModule base class.
{
  public function __construct()
  {
    parent::__construct(); // Construct the parent as well
  }

  ////////////
  // TOKENS //
  ////////////

  // This function creates a new token for a user
  public function createToken(array $user)
  {
    $token = Uuid::uuid4()->toString(); // Generate the token

    // Query: Insert a new token owned by ? with value ?
    $query = "INSERT INTO tokens(value,userId) VALUES (?,?)";


    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Perpare the query

      $statement->bind_param("si", $token, $user["id"]); // Bind the parameters
      $statement->execute(); // Execute the query

      return $token; // Return the token
    } catch (Exception $e) {
      return ""; // Error occured, return empty string to inform the calling code
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function validates the given token
  public function validateToken(string $token): bool
  {
    // Query: Select the userId and token ID from tokens where the token value is equal to ?
    $query = "SELECT userId,id FROM tokens WHERE value = ?";

    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Prepate the query
      $statement->bind_param("s", $token); // Bind the token value to ?
      $statement->execute(); // Execute the statement
      $result = $statement->get_result(); // Get the result

      // 0 returned rows? Token invalid.
      if ($result->num_rows == 0) {
        return false;
      }

      return true; // Token valid
    } catch (Exception $e) {
      return false; // Error occured, token invalid
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function discontinues the specified token
  public function discontinueToken(string $token): bool
  {
    // Query: Delete all tokens whose value is equal to ?
    $query = "DELETE FROM tokens WHERE value = ?";

    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("s", $token); // Bind the token to ?
      $statement->execute(); // Execute the statement

      return true; // Discontinued, return true
    } catch (Exception $e) {
      return false; // Error occured, return false
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  ////////////////////
  // AUTHENTICATION //
  ////////////////////

  // This function authenticates the user by checking the username and password. It returns a token if the credentials are correct.
  public function authenticateUser(string $username, string $password): string
  {
    // Query: Get the username, password hash and ID of the users whose username matches ?
    $query = "SELECT username,hash,id FROM users WHERE username = ?";

    try {
      $connection = $this->connect();// Connect to the database
      $statement = $connection->prepare($query); // Prepare the query

      $statement->bind_param("s", $username); // Bind the username
      $statement->execute(); // execute the statement
      $result = $statement->get_result(); // Get the result

      // 0 returned rows? Show user not found dialog.
      if ($result->num_rows == 0) {
        Dialog(ErrorMessages::UserNotFound);

        return "";
      }

      $row = $result->fetch_assoc(); // Fetch the result as an associative array
      $hash = $row["hash"]; // Get the user's password hash

      // Verify the hash
      $passwordValid = $this->verifyPassword($hash, $password);

      // Password invalid? Show password incorrect dialog.
      if (!$passwordValid) {
        Dialog(ErrorMessages::PasswordIncorrect);

        return "";
      }

      // Let's now create a fresh token
      $token = $this->createToken($row);

      return $token; // Return the created token
    } catch (Exception $e) {
      return "";
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function verifies the password using its hash
  public function verifyPassword(string $hash, string $password): bool
  {
    return password_verify($password, $hash);
  }

  ///////////////////
  // USER MUTATION //
  ///////////////////

  // This function creates a fresh user using a username and password
  public function createUser($username, $password)
  {
    $safeUsername = htmlspecialchars(trim($username)); // Prevent XSS by escaping the username
    $hash = password_hash($password, PASSWORD_BCRYPT); // Hash the password using bcrypt

    // Query: Insert a new user with username ? and password hash ?
    $query = "INSERT INTO users(username,hash) VALUES (?,?)";

    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("ss", $safeUsername, $hash); // Bind the safe username and password hash
      $statement->execute(); // Execute the statement

      return true; // User created
    } catch (Exception $e) {
      // The only real error that can occur here is a conflict of the UNIQUE of `username`, so tell the user that the user already exists
      Dialog(ErrorMessages::UserAlreadyExists);

      return false; // User not created
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function deletes the user by their username. Because of CASCADE, the posts and tokens of this user will also be deleted.
  public function deleteUser(string $username)
  {
    // Query: Delete all users whose username matches ?
    $query = "DELETE FROM users WHERE username = ?";

    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("s", $username); // Bind the username to ?
      $statement->execute(); // Execute the statement

      return true; // Deleted, return true
    } catch (Exception $e) {
      return false; // Not deleted, return false
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  /////////////
  // GETTING //
  /////////////

  // This function gets the user by their token
  public function getUserByToken(string $token)
  {
    // Query: Select the user ID of all tokens whose value is equal to ?
    $query = "SELECT userId FROM tokens WHERE value = ?";

    try {
      $connection = $this->connect(); // Connec to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("s", $token); // Bind the token
      $statement->execute(); // Execute the statement
      $result = $statement->get_result(); // Get the result

      // 0 returned rows? No user. Return an empty array.
      if ($result->num_rows == 0)
        return [];

      // Fetch the result as an associative array
      $row = $result->fetch_assoc();
      $userId = (int) $row["userId"]; // Get the user ID from the assoc

      return $this->getUserById($userId); // Get the user by ID and return the result
    } catch (Exception $e) {
      return []; // Error occured, return empty
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function gets the user by their ID
  public function getUserById(int $userId)
  {
    // Query: Select all users whose ID matches ?
    $query = "SELECT * FROM users WHERE id = ?";

    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("i", $userId); // Bind the user ID to ?
      $statement->execute(); // Execute the statement
      $result = $statement->get_result(); // Get the result

      // 0 returned rows? No user. Return an empty array.
      if ($result->num_rows == 0)
        return [];

      // Fetch the result as an associative array
      $row = $result->fetch_assoc();

      return $row; // Return the assoc
    } catch (Exception $e) {
      return []; // Error occured, return empty array
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function gets the user by their username
  public function getUserByName(string $username)
  {
    // Query: Select all users whose username matches ?
    $query = "SELECT * FROM users WHERE username = ?";

    try {
      $connection = $this->connect(); // Connect to the database
      $statement = $connection->prepare($query); // Prepare the query
      $statement->bind_param("i", $username); // Bind the username to ?
      $statement->execute(); // Execute the statement
      $result = $statement->get_result(); // Get the result

      // 0 returned rows? No user. Return an empty array.
      if ($result->num_rows == 0)
        return [];

      // Fetch the result as an associative array
      $row = $result->fetch_assoc();

      return $row; // Return the assoc
    } catch (Exception $e) {
      return []; // Error occured, return empty array
    } finally {
      $this->disconnect($connection, $statement); // Close the connection and statement
    }
  }

  // This function resets the user by deleting their tokens and ships
  public function resetUser(array $user)
  {
    $userId = $user["id"]; // Get the user ID from the assoc

    // Query: Delete all tokens and all ships owned by $userId
    $query = "DELETE FROM tokens WHERE userId = $userId; DELETE FROM ships WHERE authorId = $userId;";

    try {
      $connection = $this->connect(); // Connect to the database
      $connection->multi_query($query); // Execute the query as multi because we have two queries in one interaction

      return true; // Reset done, return true
    } catch (Exception $e) {
      return false; // Reset failed, return false
    } finally {
      $this->disconnect($connection);// Close the connection
    }
  }
}

// Global instance of the AuthorizationManager
$authManager = new AuthorizationManager();