<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();
$account = new Account($con);


$firstName = FormSanitizer::string($_POST["firstName"]);
$lastName = FormSanitizer::string($_POST["lastName"]);
$username = FormSanitizer::username($_POST["username"]);
$email = FormSanitizer::email($_POST["email"]);
$password = FormSanitizer::password($_POST["password"]);

$id = $account->register($firstName, $lastName, $username, $email, $password);
if (!empty($id)) {
  User::loggedIn($id);
  $usr = new User($con, $id);
  $data = $usr->dump();
  Response::ok($data);
} else {
  $err = $account->getFirstError();
  Response::fail($err);
}
