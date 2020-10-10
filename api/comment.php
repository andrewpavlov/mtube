<?php

$userLoggedInObj = new User($con, $_SESSION["userLoggedIn"]);

if (isset($_POST['commentText']) && isset($_POST['postedBy']) && isset($_POST['videoId'])) {

} else {
  echo "One or more parameters are not passed into subscribe.php the file";
}
