<?php

class User
{

  private $con, $sqlData;

  public function __construct($con, $id)
  {
    $this->con = $con;

    $query = $this->con->prepare("SELECT * FROM users WHERE id = :id");
    $query->bindParam(":id", $id);
    $query->execute();

    $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
  }

  static public function loggedIn($user = null)
  {
    if (isset($user)) {
      Session::set("userLoggedIn", $user);
    }
    return Session::get("userLoggedIn");
  }

  public function getId()
  {
    return $this->sqlData["id"];
  }

  public function getUsername()
  {
    return $this->sqlData["username"];
  }

  public function getName()
  {
    return $this->sqlData["firstName"] . " " . $this->sqlData["lastName"];
  }

  public function getFirstName()
  {
    return $this->sqlData["firstName"];
  }

  public function getLastName()
  {
    return $this->sqlData["lastName"];
  }

  public function getEmail()
  {
    return $this->sqlData["email"];
  }

  public function getProfilePic()
  {
    return $this->sqlData["profilePic"];
  }

  public function getSignUpDate()
  {
    $date = $this->sqlData["signUpDate"];
    return date("M j, Y", strtotime($date));
  }

  public function getFullName()
  {
    $ret = [];
    $fn = $this->getFirstName();
    if (!empty($fn)) {
      $ret[] = $fn;
    }
    $ln = $this->getLastName();
    if (!empty($ln)) {
      $ret[] = $ln;
    }
    return join(" ", $ret);
  }

  public function isSubscribedTo($userTo)
  {
    $id = $this->getId();
    $query = $this->con->prepare("SELECT * FROM subscribers WHERE userTo=:userTo AND userFrom=:userFrom");
    $query->bindParam(":userTo", $userTo);
    $query->bindParam(":userFrom", $id);
    $query->execute();
    return $query->rowCount() > 0;
  }

  public function getSubscriberCount()
  {
    $id = $this->getId();
    $query = $this->con->prepare("SELECT * FROM subscribers WHERE userTo=:userTo");
    $query->bindParam(":userTo", $id);
    $query->execute();
    return $query->rowCount();
  }

  public function getSubscriptions()
  {
    $query = $this->con->prepare("SELECT userTo FROM subscribers WHERE userFrom=:userFrom");
    $id = $this->getId();
    $query->bindParam(":userFrom", $id);
    $query->execute();

    $subs = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $user = new User($this->con, $row["userTo"]);
      array_push($subs, $user);
    }
    return $subs;
  }

  public function subscribeTo($userTo)
  {
    $id = $this->getId();
    $subscribed = $this->isSubscribedTo($userTo);
    if ($subscribed) {
      // Delete
      $query = $this->con->prepare("DELETE FROM subscribers WHERE userTo=:userTo AND userFrom=:userFrom");
      $query->bindParam(":userTo", $userTo);
      $query->bindParam(":userFrom", $id);
      $query->execute();
    } else {
      // Insert
      $query = $this->con->prepare("INSERT INTO subscribers(id, userTo, userFrom) VALUES(UUID(), :userTo, :userFrom)");
      $query->bindParam(":userTo", $userTo);
      $query->bindParam(":userFrom", $id);
      $query->execute();
    }
    return !$subscribed;
  }

  public function getTotalViews()
  {
    $query = $this->con->prepare("SELECT sum(views) FROM videos WHERE uploadedBy=:id");
    $id = $this->getId();
    $query->bindParam(":id", $id);
    $query->execute();

    return $query->fetchColumn();
  }

  public function dump()
  {
    return [
      "id" => $this->getId(),
      "pic" => $this->getProfilePic(),
      "username" => $this->getUsername(),
      "email" => $this->getEmail(),
      "firstName" => $this->getFirstName(),
      "lastName" => $this->getLastName(),
      "fullName" => $this->getFullName(),
      "signUpDate" => $this->getSignUpDate(),
      "subscriberCount" => $this->getSubscriberCount(),
      "totalViews" => $this->getTotalViews(),
    ];
  }
}
