<?php

include_once("inc/autoload.php");

Session::start();
Session::destroy();

Response::ok();
