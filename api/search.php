<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();
$id = User::loggedIn();
$usr = new User($con, $id);
$srp = new SearchResultsProvider($con, $usr);

$ret = [];
$found = [];
if (isset($_GET["liked"])) {
  $found = $srp->likedVideos();
} else if (isset($_GET["subscriptions"])) {
  $found = $srp->subscriptions();
} else if (isset($_GET["trending"])) {
  $found = $srp->trending();
} else if (isset($_GET["recommended"])) {
  $found = $srp->recommended($_GET["recommended"]);
} else if (isset($_GET["user"])) {
  $found = $srp->userVideos($_GET["user"]);
} else {
  $term = getMapValue($_GET, "term", "");
  $orderBy = getMapValue($_GET, "orderBy", "views");
  $found = $srp->search($term, $orderBy);
}
$ret = Video::dumpMany($found);

Response::ok($ret);
