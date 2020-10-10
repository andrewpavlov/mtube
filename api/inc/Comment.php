<?php

class Comment
{
  private $con;
  private $sqlData;
  private $userLoggedInObj;
  private $videoId;

  public function __construct($con, $input, $userLoggedInObj, $videoId)
  {
    if (!empty($input) && !is_array($input)) {
      $query = $con->prepare("SELECT * FROM comments where id=:id");
      $query->bindParam(":id", $input);
      $query->execute();

      $input = $query->fetch(PDO::FETCH_ASSOC);
    }

    $this->sqlData = $input;
    $this->con = $con;
    $this->userLoggedInObj = $userLoggedInObj;
    $this->videoId = $videoId;
  }

  public function create($responseTo, $commentText)
  {
    $query = $this->con->query("SELECT UUID() as `id`");
    $sqlData = $query->fetch(PDO::FETCH_ASSOC);
    $uuid = $sqlData["id"];

    $query = $this->con->prepare("
      INSERT INTO
        comments (id, postedBy, videoId, responseTo, body)
      VALUES
        (:id, :postedBy, :videoId, :responseTo, :body)
    ");
    $query->bindParam(":id", $uuid);
    $userId = $this->userLoggedInObj->getId();
    $query->bindParam(":postedBy", $userId);
    $videoId = $this->videoId;
    $query->bindParam(":videoId", $videoId);
    $query->bindParam(":responseTo", $responseTo);
    $query->bindParam(":body", $commentText);

    $query->execute();

    return $uuid;
  }

  public function getNumberOfReplies()
  {
    $query = $this->con->prepare("
      SELECT
        count(*)
      FROM
        comments
      WHERE
        responseTo=:responseTo
    ");
    $id = $this->getId();
    $query->bindParam(":responseTo", $id);
    $query->execute();

    return $query->fetchColumn();
  }

  public function getId()
  {
    return $this->sqlData["id"];
  }

  public function getVideoId()
  {
    return $this->videoId;
  }

  public function getPostedBy()
  {
    return $this->sqlData["postedBy"];
  }

  public function getResponseTo()
  {
    return $this->sqlData["responseTo"];
  }

  public function getBody()
  {
    return  $this->sqlData["body"];
  }

  public function getPublishedAt()
  {
    return  $this->sqlData["datePosted"];
  }

  public function wasLikedBy()
  {
    $query = $this->con->prepare("SELECT * FROM likes WHERE userId=:userId AND commentId=:commentId");
    $userId = $this->userLoggedInObj->getId();
    $query->bindParam(":userId", $userId);
    $id = $this->getId();
    $query->bindParam(":commentId", $id);

    $query->execute();

    return $query->rowCount() > 0;
  }

  public function wasDislikedBy()
  {
    $query = $this->con->prepare("SELECT * FROM dislikes WHERE userId=:userId AND commentId=:commentId");
    $userId = $this->userLoggedInObj->getId();
    $query->bindParam(":userId", $userId);
    $id = $this->getId();
    $query->bindParam(":commentId", $id);

    $query->execute();

    return $query->rowCount() > 0;
  }

  public function getLikes()
  {
    $query = $this->con->prepare("SELECT count(*) as 'count' FROM likes WHERE commentId=:commentId");
    $commentId = $this->getId();
    $query->bindParam(":commentId", $commentId);
    $query->execute();

    $data = $query->fetch(PDO::FETCH_ASSOC);
    $numLikes = $data["count"];

    return $numLikes;
  }

  public function getDisLikes()
  {
    $query = $this->con->prepare("SELECT count(*) as 'count' FROM dislikes WHERE commentId=:commentId");
    $commentId = $this->getId();
    $query->bindParam(":commentId", $commentId);
    $query->execute();

    $data = $query->fetch(PDO::FETCH_ASSOC);
    $numDislikes = $data["count"];

    return $numDislikes;
  }

  public function like()
  {
    $id = $this->getId();
    $userId = $this->userLoggedInObj->getId();
    $wasLiked = $this->wasLikedBy();

    if ($wasLiked) {
      $query = $this->con->prepare("DELETE FROM likes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();
    } else {
      $query = $this->con->prepare("DELETE FROM dislikes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();

      $query = $this->con->prepare("INSERT INTO likes(id, userId, commentId) VALUES (UUID(), :userId, :commentId)");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();
    }

    return $wasLiked ? 0 : 1;
  }

  public function dislike()
  {
    $id = $this->getId();
    $userId = $this->userLoggedInObj->getId();
    $wasDisLiked = $this->wasDislikedBy();

    if ($wasDisLiked) {
      $query = $this->con->prepare("DELETE FROM dislikes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();
    } else {
      $query = $this->con->prepare("DELETE FROM likes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();

      $query = $this->con->prepare("INSERT INTO dislikes(id, userId, commentId) VALUES (UUID(), :userId, :commentId)");
      $query->bindParam(":userId", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();
    }

    return $wasDisLiked ? 0 : -1;
  }

  public function dump()
  {
    $postedBy = new User($this->con, $this->getPostedBy());
    return [
      "id" => $this->getId(),
      "likes" => $this->getLikes(),
      "dislikes" => $this->getDisLikes(),
      "videoId" => $this->getVideoId(),
      "postedBy" => $postedBy->dump(),
      "body" => $this->getBody(),
      "publishedAt" => $this->getPublishedAt(),
      "repliesCount" => $this->getNumberOfReplies(),
      "rate" => $this->wasLikedBy() ? 1 : ($this->wasDislikedBy() ? -1 : 0),
    ];
  }
}
