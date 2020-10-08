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

if (empty($_FILES["file"])) {
  Response::fail("Bad parameters", 400);
  exit;
}

$videoProcessor = new VideoProcessor($con);
$videoId = $videoProcessor->upload($_FILES["file"], $usr->getId());
if (!$videoId) {
  $err = $videoProcessor->getAllErrors();
  Response::fail($err);
} else {
  Response::ok($videoId);
}
