<?php

class Video
{

  private $con;
  private $sqlData;
  private $userLoggedInObj;

  public function __construct($con, $input, $userLoggedInObj)
  {
    $this->con = $con;
    $this->userLoggedInObj = $userLoggedInObj;

    if (is_array($input)) {
      $this->sqlData = $input;
    } else {
      $query = $this->con->prepare("SELECT * FROM videos WHERE id = :id");
      $query->bindParam(":id", $input);
      $query->execute();

      $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }
  }

  public function getId()
  {
    return $this->sqlData["id"];
  }

  public function getUploadedBy()
  {
    return $this->sqlData["uploadedBy"];
  }

  public function getTitle()
  {
    return $this->sqlData["title"];
  }

  public function getDescription()
  {
    return $this->sqlData["description"];
  }

  public function getPrivacy()
  {
    return $this->sqlData["privacy"];
  }

  public function getFilePath()
  {
    return $this->sqlData["filePath"];
  }

  public function getCategory()
  {
    $query = $this->con->prepare("
      SELECT
        *
      FROM
        categories
      WHERE
        id=:id
    ");
    $query->bindParam(":id", $this->sqlData["category"]);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
  }

  public function getUploadDate()
  {
    $date = $this->sqlData["uploadDate"];
    return date("M j, Y", strtotime($date));
  }

  public function getTimeStamp()
  {
    $date = $this->sqlData["uploadDate"];
    return date("M jS, Y", strtotime($date));
  }

  public function getViews()
  {
    return $this->sqlData["views"];
  }

  public function getDuration()
  {
    return $this->sqlData["duration"];
  }

  public function incrementViews()
  {
    $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
    $videoId = $this->getId();
    $query->bindParam(":id", $videoId);

    $query->execute();

    $this->sqlData["views"] = $this->sqlData["views"] + 1;
  }

  public function updateDetails($t, $d, $p, $c)
  {
    $query = $this->con->prepare("
      UPDATE
        videos
      SET
        title=:title,
        description=:description,
        privacy=:privacy,
        category=:category
      WHERE
        id=:videoId
    ");
    $query->bindParam(":title", $t);
    $query->bindParam(":description", $d);
    $query->bindParam(":privacy", $p);
    $query->bindParam(":category", $c);
    $videoId = $this->getId();
    $query->bindParam(":videoId", $videoId);

    return $query->execute();
  }

  public function getLikes()
  {
    $query = $this->con->prepare("SELECT count(*) as 'count' FROM likes WHERE videoId = :videoId");
    $videoId = $this->getId();
    $query->bindParam(":videoId", $videoId);
    $query->execute();

    $data = $query->fetch(PDO::FETCH_ASSOC);
    return $data["count"];
  }

  public function getDislikes()
  {
    $query = $this->con->prepare("SELECT count(*) as 'count' FROM dislikes WHERE videoId = :videoId");
    $videoId = $this->getId();
    $query->bindParam(":videoId", $videoId);
    $query->execute();

    $data = $query->fetch(PDO::FETCH_ASSOC);
    return $data["count"];
  }

  public function like()
  {
    $id = $this->getId();
    $userId = $this->userLoggedInObj->getId();

    if ($this->wasLikedBy()) {
      // User has already liked
      $query = $this->con->prepare("DELETE FROM likes WHERE userId=:user AND videoId=:videoId");
      $query->bindParam(":user", $userId);
      $query->bindParam(":videoId", $id);
      $query->execute();
    } else {
      $query = $this->con->prepare("DELETE FROM dislikes WHERE userId=:user AND videoId=:videoId");
      $query->bindParam(":user", $userId);
      $query->bindParam(":videoId", $id);
      $query->execute();

      $query = $this->con->prepare("INSERT INTO likes(id, userId, videoId) VALUES(UUID(), :user, :videoId)");
      $query->bindParam(":user", $userId);
      $query->bindParam(":videoId", $id);
      $query->execute();
    }

    return $this->getLikes();
  }

  public function dislike()
  {
    $id = $this->getId();
    $userId = $this->userLoggedInObj->getId();

    if ($this->wasDislikedBy()) {
      // User has already liked
      $query = $this->con->prepare("DELETE FROM dislikes WHERE userId=:userId AND videoId=:videoId");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":videoId", $id);
      $query->execute();
    } else {
      $query = $this->con->prepare("DELETE FROM likes WHERE userId=:userId AND videoId=:videoId");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":videoId", $id);
      $query->execute();

      $query = $this->con->prepare("INSERT INTO dislikes(id, userId, videoId) VALUES(UUID(), :userId, :videoId)");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":videoId", $id);
      $query->execute();
    }

    return $this->getDislikes();
  }

  public function wasLikedBy()
  {
    $query = $this->con->prepare("SELECT * FROM likes WHERE userId=:userId AND videoId=:videoId");

    $userId = $this->userLoggedInObj->getId();
    $query->bindParam(":userId", $userId);

    $id = $this->getId();
    $query->bindParam(":videoId", $id);

    $query->execute();

    return $query->rowCount() > 0;
  }

  public function wasDislikedBy()
  {
    $query = $this->con->prepare("SELECT * FROM dislikes WHERE userId=:userId AND videoId=:videoId");

    $userId = $this->userLoggedInObj->getId();
    $query->bindParam(":userId", $userId);

    $id = $this->getId();
    $query->bindParam(":videoId", $id);

    $query->execute();

    return $query->rowCount() > 0;
  }

  public function getRate()
  {
    $userId = $this->userLoggedInObj->getId();
    if ($this->wasLikedBy($userId)) {
      return 1;
    }
    if ($this->wasDislikedBy($userId)) {
      return -1;
    }
    return 0;
  }

  public function getNumberOfComments()
  {
    $query = $this->con->prepare("SELECT * FROM comments WHERE videoId=:videoId");
    $id = $this->getId();
    $query->bindParam(":videoId", $id);

    $query->execute();

    return $query->rowCount();
  }

  public function getComments()
  {
    $query = $this->con->prepare("
      SELECT
        *
      FROM
        comments
      WHERE
        videoId=:videoId
        AND responseTo=0
      ORDER BY
        datePosted DESC
    ");
    $id = $this->getId();
    $query->bindParam(":videoId", $id);

    $query->execute();

    $comments = array();

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $comment = new Comment($this->con, $row, $this->userLoggedInObj, $id);
      array_push($comments, $comment);
    }

    return $comments;
  }

  public function getThumbnail()
  {
    $query = $this->con->prepare("SELECT filePath FROM thumbnails WHERE videoId=:videoId AND selected=1");
    $videoId = $this->getId();
    $query->bindParam(":videoId", $videoId);

    $query->execute();

    return $query->fetchColumn();
  }

  public function getUploadedByUser()
  {
    $usr = new User($this->con, $this->getUploadedBy());
    $ret = $usr->dump();
    unset($usr);
    return $ret;
  }

  public function dump()
  {
    return Video::dumpOne($this);
  }

  static public function dumpOne($vidInstance)
  {
    return [
      "id" => $vidInstance->getId(),
      "thumb" => $vidInstance->getThumbnail(),
      "duration" => $vidInstance->getDuration(),
      "title" => $vidInstance->getTitle(),
      "url" => $vidInstance->getFilePath(),
      "user" => $vidInstance->getUploadedByUser(),
      "views" => $vidInstance->getViews(),
      "description" => $vidInstance->getDescription(),
      "privacy" => $vidInstance->getPrivacy(),
      "category" => $vidInstance->getCategory(),
      "timestamp" => $vidInstance->getTimeStamp(),
      "likes" => $vidInstance->getLikes(),
      "dislikes" => $vidInstance->getDislikes(),
      "rate" => $vidInstance->getRate(),
    ];
  }

  static public function dumpMany($vids)
  {
    $ret = [];
    foreach ($vids as $vid) {
      $ret[] = Video::dumpOne($vid);
    }
    return $ret;
  }
}
