<?php

include_once("inc/autoload.php");

$con = DbConnection::get();
$video = new SearchResultsProvider($con);

$ret = $video->getCategories();
Response::ok($ret);
