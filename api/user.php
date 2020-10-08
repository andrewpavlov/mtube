<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();

$id = $_GET["user"];
if (empty($_GET["user"])) {
  $id = User::loggedIn();
}
$usr = new User($con, $id);
$ret = $usr->dump();

Response::ok($ret);
