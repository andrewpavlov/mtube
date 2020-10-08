<?php

class Account
{

  private $con;
  private $errorArray = array();

  public function __construct($con)
  {
    $this->con = $con;
  }

  public function login($un, $pw)
  {
    $pw = hash("sha512", $pw);

    $query = $this->con->prepare("
      SELECT
        id
      FROM
        users
      WHERE 1
        AND (username=:un OR email=:un)
        AND password=:pw
    ");
    $query->bindParam(":un", $un);
    $query->bindParam(":pw", $pw);

    $query->execute();

    if ($query->rowCount() == 1) {
      $sqlData = $query->fetch(PDO::FETCH_ASSOC);
      return $sqlData["id"];
    }

    array_push($this->errorArray, Constants::$loginFailed);
    return false;
  }

  public function register($fn, $ln, $un, $em, $pw)
  {
    $this->validateFirstName($fn);
    $this->validateLastName($ln);
    $this->validateUsername($un);
    $this->validateNewEmail($em, null);
    $this->validatePassword($pw);

    if (empty($this->errorArray)) {
      return $this->insertUserDetails($fn, $ln, $un, $em, $pw);
    }

    return false;
  }

  public function updateDetails($fn, $ln, $em, $id)
  {
    $this->validateFirstName($fn);
    $this->validateLastName($ln);
    $this->validateNewEmail($em, $id);

    if (empty($this->errorArray)) {
      $query = $this->con->prepare("UPDATE users SET firstName=:fn, lastName=:ln, email=:em WHERE id=:id");
      $query->bindParam(":fn", $fn);
      $query->bindParam(":ln", $ln);
      $query->bindParam(":em", $em);
      $query->bindParam(":id", $id);

      return $query->execute();
    }

    return false;
  }

  public function updatePassword($oldPw, $pw, $pw2, $id)
  {
    $this->validateOldPassword($oldPw, $id);
    $this->validatePasswords($pw, $pw2);

    if (empty($this->errorArray)) {
      $query = $this->con->prepare("UPDATE users SET password=:pw WHERE id=:id");
      $pw = hash("sha512", $pw);
      $query->bindParam(":pw", $pw);
      $query->bindParam(":id", $id);

      return $query->execute();
    }

    return false;
  }

  private function validateOldPassword($oldPw, $id)
  {
    $pw = hash("sha512", $oldPw);

    $query = $this->con->prepare("SELECT * FROM users WHERE id=:id AND password=:pw");
    $query->bindParam(":id", $id);
    $query->bindParam(":pw", $pw);

    $query->execute();

    if ($query->rowCount() == 0) {
      array_push($this->errorArray, Constants::$passwordIncorrect);
    }
  }

  public function insertUserDetails($fn, $ln, $un, $em, $pw)
  {
    $pw = hash("sha512", $pw);
    $profilePic = "assets/images/profilePictures/default.png";

    $query = $this->con->query("SELECT UUID() as `id`");
    $sqlData = $query->fetch(PDO::FETCH_ASSOC);
    $uuid = $sqlData["id"];

    $query = $this->con->prepare("
      INSERT INTO users (id, firstName, lastName, username, email, password, profilePic)
      VALUES (:id, :fn, :ln, :un, :em, :pw, :pic)
    ");

    $query->bindParam(":id", $uuid);
    $query->bindParam(":fn", $fn);
    $query->bindParam(":ln", $ln);
    $query->bindParam(":un", $un);
    $query->bindParam(":em", $em);
    $query->bindParam(":pw", $pw);
    $query->bindParam(":pic", $profilePic);

    $query->execute();

    return $uuid;
  }

  private function validateFirstName($fn)
  {
    if (strlen($fn) > 25 || strlen($fn) < 2) {
      array_push($this->errorArray, Constants::$firstNameCharacters);
    }
  }

  private function validateLastName($ln)
  {
    if (strlen($ln) > 25 || strlen($ln) < 2) {
      array_push($this->errorArray, Constants::$lastNameCharacters);
    }
  }

  private function validateUsername($un)
  {
    if (strlen($un) > 25 || strlen($un) < 5) {
      array_push($this->errorArray, Constants::$usernameCharacters);
      return;
    }

    $query = $this->con->prepare("
      SELECT
        username
      FROM
        users
      WHERE
        username=:un
    ");
    $query->bindParam(":un", $un);
    $query->execute();

    if ($query->rowCount() != 0) {
      array_push($this->errorArray, Constants::$usernameTaken);
    }
  }

  private function validateNewEmail($em, $id)
  {

    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
      array_push($this->errorArray, Constants::$emailInvalid);
      return;
    }

    $query = $this->con->prepare("SELECT email FROM users WHERE email=:em AND id!=:id");
    $query->bindParam(":em", $em);
    $query->bindParam(":id", $id);
    $query->execute();

    if ($query->rowCount() != 0) {
      array_push($this->errorArray, Constants::$emailTaken);
    }
  }

  private function validatePassword($pw)
  {
    if (preg_match("/[^A-Za-z0-9]/", $pw)) {
      array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
      return;
    }

    if (strlen($pw) > 30 || strlen($pw) < 5) {
      array_push($this->errorArray, Constants::$passwordLength);
    }
  }

  private function validatePasswords($pw, $pw2)
  {
    if ($pw != $pw2) {
      array_push($this->errorArray, Constants::$passwordsDoNotMatch);
      return;
    }

    if (preg_match("/[^A-Za-z0-9]/", $pw)) {
      array_push($this->errorArray, Constants::$passwordNotAlphanumeric);
      return;
    }

    if (strlen($pw) > 30 || strlen($pw) < 5) {
      array_push($this->errorArray, Constants::$passwordLength);
    }
  }

  public function getError($error)
  {
    if (in_array($error, $this->errorArray)) {
      return $error;
    }

    return false;
  }

  public function getFirstError()
  {
    if (!empty($this->errorArray)) {
      return $this->errorArray[0];
    }

    return false;
  }
}
