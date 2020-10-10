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

$videoId = getMapValue($_GET, "id", getMapValue($_POST, "id"));
if (empty($videoId)) {
  Response::fail("Bad parameters", 400);
  exit;
}


$title = FormSanitizer::text($_POST["title"]);
$description = FormSanitizer::text($_POST["description"]);
$privacy = intval($_POST["privacy"]) === 1 ? 1 : 0;
$category = intval($_POST["category"]);


$video = new Video($con, $videoId, $usr);
$ret = $video->updateDetails($title, $description, $privacy, $category);

Response::ok($ret);
