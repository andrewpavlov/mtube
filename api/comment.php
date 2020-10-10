<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();

$id = User::loggedIn();
$usr = new User($con, $id);

$do = getMapValue($_POST, "do");

if (!empty($do) && !$usr->loggedIn()) {
  Response::fail("Not authenticated", 401);
  exit;
}

$videoId = getMapValue($_GET, "videoId", getMapValue($_POST, "videoId"));
if (empty($videoId)) {
  Response::fail("Bad parameters", 400);
  exit;
}

$commentId = getMapValue($_GET, "id", getMapValue($_POST, "id"));
if (!empty($commentId)) {
  $commentId = FormSanitizer::guid($commentId);
}
$comment = new Comment($con, $commentId, $usr, $videoId);

if ($do === "comment") {
  $text = getMapValue($_POST, "text", "");
  $text = FormSanitizer::text($text);

  $replyTo = getMapValue($_POST, "replyTo");
  if (!empty($replyTo)) {
    $replyTo = FormSanitizer::guid($replyTo);
  }

  $comment->create($replyTo, $text);

  $video = new Video($con, $videoId, $usr);
  $comments = $video->getComments();
  $ret = [];
  foreach ($comments as $comment) {
    $ret[] = $comment->dump();
  }
  Response::ok($ret);
  exit;
}

if ($do === "like") {
  $comment->like();
  Response::ok($comment->dump());
  exit;
}

if ($do === "dislike") {
  $comment->dislike();
  Response::ok($comment->dump());
  exit;
}

$video = new Video($con, $videoId, $usr);
$comments = $video->getComments();
$ret = [];
foreach ($comments as $comment) {
  $ret[] = $comment->dump();
}
Response::ok($ret);
