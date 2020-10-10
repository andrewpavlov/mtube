<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();

$id = User::loggedIn();
$usr = new User($con, $id);

$videoId = getMapValue($_GET, "id", getMapValue($_POST, "id"));
if (empty($videoId)) {
  Response::fail("Bad parameters", 400);
  exit;
}
$video = new Video($con, $videoId, $usr);

$do = getMapValue($_POST, "do");
if (!empty($do)) {
  if (!$usr->loggedIn()) {
    Response::fail("Not authenticated", 401);
    exit;
  }

  if ($do === "like") {
    $video->like();
  } else if ($do === "dislike") {
    $video->dislike();
  }
} else {
  // watch
  $video->incrementViews();
}

$ret = $video->dump(true);
Response::ok($ret);
