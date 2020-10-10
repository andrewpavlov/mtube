<?php

class Comment
{
  private $con;
  private $sqlData;
  private $userLoggedInObj;
  private $videoId;

  public function __construct($con, $input, $userLoggedInObj, $videoId)
  {
    if (!is_array($input)) {
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

    $postedBy = $_POST['postedBy'];
    $videoId = $_POST['videoId'];
    $responseTo = isset($_POST['responseTo']) ? $_POST['responseTo'] : 0;
    $commentText = $_POST['commentText'];

    $query->execute();

    $comment = new Comment($this->con, $uuid, $this->userLoggedInObj, $this->videoId);
    return $comment->dump();
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

    if ($this->wasLikedBy()) {
      // User has already liked
      $query = $this->con->prepare("DELETE FROM likes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":username", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();

      return -1;
    } else {
      $query = $this->con->prepare("DELETE FROM dislikes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":username", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();
      $count = $query->rowCount();

      $query = $this->con->prepare("INSERT INTO likes(userId, commentId) VALUES(:userId, :commentId)");
      $query->bindParam(":username", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();

      return 1 + $count;
    }
  }

  public function dislike()
  {
    $id = $this->getId();
    $userId = $this->userLoggedInObj->getId();

    if ($this->wasDislikedBy()) {
      // User has already liked
      $query = $this->con->prepare("DELETE FROM dislikes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":username", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();

      return 1;
    } else {
      $query = $this->con->prepare("DELETE FROM likes WHERE userId=:userId AND commentId=:commentId");
      $query->bindParam(":username", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();
      $count = $query->rowCount();

      $query = $this->con->prepare("INSERT INTO dislikes(userId, commentId) VALUES(:userId, :commentId)");
      $query->bindParam(":username", $userId);
      $query->bindParam(":commentId", $id);
      $query->execute();

      return -1 - $count;
    }
  }

  public function getReplies()
  {
    $query = $this->con->prepare("
      SELECT
        *
      FROM
        comments
      WHERE
        responseTo=:commentId
      ORDER BY
        datePosted ASC
    ");
    $id = $this->getId();
    $query->bindParam(":commentId", $id);

    $query->execute();

    $comments = [];
    $videoId = $this->getVideoId();
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $comments[] = new Comment($this->con, $row, $this->userLoggedInObj, $videoId);
    }

    return $comments;
  }

  public function dumpReplies() {
    $replies = $this->getReplies();
    $comments = [];
    foreach ($replies as &$comment) {
      $comments[] = $comment->dump();
    }
    return $comments;
  }

  public function dump()
  {
    $postedBy = new User($this->con, $this->getPostedBy());
    return [
      "id" => $this->getId(),
      "likes" => $this->getLikes(),
      "disLikes" => $this->getDisLikes(),
      "videoId" => $this->getVideoId(),
      "postedBy" => $postedBy->dump(),
      "body" => $this->getBody(),
      "repliesCount" => $this->getNumberOfReplies(),
      // "replies" => $this->dumpReplies(),
    ];
  }
}
