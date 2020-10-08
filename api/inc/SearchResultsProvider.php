<?php

class SearchResultsProvider
{

  private $con, $userLoggedInObj;

  public function __construct($con, $userLoggedInObj = null)
  {
    $this->con = $con;
    $this->userLoggedInObj = $userLoggedInObj;
  }

  public function search($term, $orderBy)
  {
    $query = $this->con->prepare("
      SELECT
        *
      FROM
        videos
      WHERE
        title LIKE CONCAT('%', :term, '%')
        OR uploadedBy LIKE CONCAT('%', :term, '%')
      ORDER BY
        $orderBy DESC
      LIMIT 50
    ");
    $query->bindParam(":term", $term);
    $query->execute();

    $videos = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $video = new Video($this->con, $row, $this->userLoggedInObj);
      $videos[] = $video;
    }

    return $videos;
  }

  public function trending()
  {
    $query = $this->con->prepare("
      SELECT
        *
      FROM
        videos
      WHERE
        uploadDate >= now() - INTERVAL 7 DAY
      ORDER BY
        views DESC
      LIMIT 15
    ");
    $query->execute();

    $videos = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $video = new Video($this->con, $row, $this->userLoggedInObj);
      array_push($videos, $video);
    }

    return $videos;
  }

  public function recommended($id = "")
  {
    $query = $this->con->prepare("
      SELECT
        *
      FROM
        videos
      WHERE
        id != :id
      ORDER BY
        RAND()
      LIMIT 15
    ");
    $query->bindParam(":id", $id);
    $query->execute();

    $videos = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $video = new Video($this->con, $row, $this->userLoggedInObj);
      $videos[] = $video;
    }

    return $videos;
  }

  public function subscriptions()
  {
    $videos = array();
    $subscriptions = $this->userLoggedInObj->getSubscriptions();
    if (sizeof($subscriptions) > 0) {

      // user1, user2, user3
      // SELECT * FROM videos WHERE uploadedBy = ? OR uploadedBy = ? OR uploadedBy = ? 
      // $query->bindParam(1, "user1");
      // $query->bindParam(2, "user2");
      // $query->bindParam(3, "user3");

      $condition = "";
      $i = 0;

      while ($i < sizeof($subscriptions)) {

        if ($i == 0) {
          $condition .= "WHERE uploadedBy=?";
        } else {
          $condition .= " OR uploadedBy=?";
        }
        $i++;
      }

      $videoSql = "SELECT * FROM videos $condition ORDER BY uploadDate DESC";
      $videoQuery = $this->con->prepare($videoSql);
      $i = 1;

      foreach ($subscriptions as $sub) {

        $subUserId = $sub->getId();
        $videoQuery->bindValue($i, $subUserId);
        $i++;
      }

      $videoQuery->execute();
      while ($row = $videoQuery->fetch(PDO::FETCH_ASSOC)) {
        $video = new Video($this->con, $row, $this->userLoggedInObj);
        array_push($videos, $video);
      }
    }

    return $videos;
  }

  public function likedVideos()
  {
    $videos = array();

    $query = $this->con->prepare("
      SELECT
        videoId
      FROM
        likes
      WHERE
        userId=:userId
        AND isNull(commentId)
      ORDER BY
        id DESC
      LIMIT 50
    ");
    $userId = $this->userLoggedInObj->getId();
    $query->bindParam(":userId", $userId);
    $query->execute();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $videos[] = new Video($this->con, $row["videoId"], $this->userLoggedInObj);
    }

    return $videos;
  }

  public function userVideos($id = null)
  {
    $query = $this->con->prepare("
      SELECT
        *
      FROM
        videos
      WHERE
        uploadedBy=:uploadedBy
      ORDER BY
        uploadDate DESC
      LIMIT 50
    ");
    if (empty($id)) {
      $id = $this->userLoggedInObj->getId();
    }
    $query->bindParam(":uploadedBy", $id);
    $query->execute();

    $videos = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $videos[] = new Video($this->con, $row, $this->userLoggedInObj);
    }
    return $videos;
  }

  public function getCategories()
  {
    $query = $this->con->query("
      SELECT
        *
      FROM
        categories
    ");
    $ret = array();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $ret[] = $row;
    }
    return $ret;
  }
}
