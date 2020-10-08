<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();
$account = new Account($con);

$email = FormSanitizer::email($_POST["email"]);
$password = FormSanitizer::password($_POST["password"]);

$id = $account->login($email, $password);
if (!empty($id)) {
  User::loggedIn($id);
  $usr = new User($con, $id);
  $data = $usr->dump();
  Response::ok($data);
} else {
  Response::fail("Invalid email or password", 403);
}
