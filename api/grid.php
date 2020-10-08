<?php

include_once("inc/autoload.php");

Session::start();
$con = DbConnection::get();

$ret = [
  "subscription" => [],
  "recommended" => [],
];
$id = User::loggedIn();
$usr = new User($con, $id);
$srp = new SearchResultsProvider($con, $usr);
if (!empty($id)) {
  $subscription = $srp->subscriptions();
  $ret["subscription"] = Video::dumpMany($subscription);
}
$recommended = $srp->recommended();
$ret["recommended"] = Video::dumpMany($recommended);

Response::ok($ret);
