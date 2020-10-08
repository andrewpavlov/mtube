<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();

$id = User::loggedIn();
$usr = new User($con, $id);
if (!$usr->loggedIn()) {
  Response::fail("Not authenticated", 401);
  exit;
}

$userTo = getMapValue($_GET, "id");
if (!empty($userTo)) {
  $ret = $usr->isSubscribedTo($userTo);
  Response::ok($ret);
  exit;
}

$userTo = getMapValue($_POST, "id");
if ($userTo === $id) {
  Response::fail("Bad parameters", 400);
  exit;
}
$ret = $usr->subscribeTo($userTo);
Response::ok($ret);
