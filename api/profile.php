<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();

$id = User::loggedIn();
$usr = new User($con, $id);

$do = getMapValue($_POST, "do", "nothing");

if (!$usr->loggedIn()) {
  Response::fail("Not authenticated", 401);
  exit;
}

if ($do === "changeDetails") {
  $account = new Account($con);

  $firstName = FormSanitizer::string($_POST["firstName"]);
  $lastName = FormSanitizer::string($_POST["lastName"]);
  $email = FormSanitizer::email($_POST["email"]);

  if (!$account->updateDetails($firstName, $lastName, $email, $usr->getId())) {
    $err = $account->getFirstError();
    Response::fail($err);
  } else {
    Response::ok();
  }
  exit;
}

if ($do === "changePassword") {
  $account = new Account($con);

  $oldPassword = FormSanitizer::password($_POST["password"]);
  $newPassword = FormSanitizer::password($_POST["newPassword"]);
  $verifyPassword = FormSanitizer::password($_POST["verifyPassword"]);

  if (!$account->updatePassword($oldPassword, $newPassword, $verifyPassword, $usr->getId())) {
    $err = $account->getFirstError();
    Response::fail($err);
  } else {
    Response::ok();
  }
  exit;
}

$info = $usr->dump();
Response::ok($info);
