<?php

class VideoProcessor
{
  private $con;
  private $sizeLimit = 500000000;
  private $allowedTypes = array("mp4", "flv", "webm", "mkv", "vob", "ogv", "ogg", "avi", "wmv", "mov", "mpeg", "mpg");
  private $ffmpegPath = "ffmpeg/ffmpeg";
  private $ffprobePath = "ffmpeg/ffprobe";
  private $errorArray = [];

  public function __construct($con)
  {
    $this->con = $con;
  }

  public function upload($videoData, $uploadedBy)
  {
    $targetDir = "uploads/videos/";

    $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);
    $tempFilePath = str_replace(" ", "_", $tempFilePath);

    if (!$this->processData($videoData, $tempFilePath)) {
      return false;
    }

    if (move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
      $finalFilePath = $targetDir . uniqid() . ".mp4";

      $videoId = $this->insertVideoData($uploadedBy, $finalFilePath);
      if (!$videoId) {
        $this->errorArray[] = "Insert query failed";
        return false;
      }

      if (!$this->convertVideoToMp4($tempFilePath, $finalFilePath)) {
        return false;
      }

      if (!$this->deleteFile($tempFilePath)) {
        return false;
      }

      if (!$this->generateThumbnails($finalFilePath, $videoId)) {
        $this->errorArray[] = "Upload failed - could not generate thumbnails";
        return false;
      }
    }

    return $videoId;
  }

  private function processData($videoData, $filePath)
  {
    $videoType = pathInfo($filePath, PATHINFO_EXTENSION);

    if (!$this->isValidSize($videoData)) {
      $this->errorArray[] = "File too large. Can't be more than " . $this->sizeLimit . " bytes";
      return false;
    }
    if (!$this->isValidType($videoType)) {
      $this->errorArray[] = "Invalid file type";
      return false;
    }
    if ($this->hasError($videoData)) {
      $this->errorArray[] = "Error code: " . $videoData["error"];
      return false;
    }

    return true;
  }

  private function isValidSize($data)
  {
    return $data["size"] <= $this->sizeLimit;
  }

  private function isValidType($type)
  {
    $lowercased = strtolower($type);
    return in_array($lowercased, $this->allowedTypes);
  }

  private function hasError($data)
  {
    return $data["error"] != 0;
  }

  private function insertVideoData($uploadedBy, $filePath)
  {
    $query = $this->con->query("SELECT UUID() as `id`");
    $sqlData = $query->fetch(PDO::FETCH_ASSOC);
    $uuid = $sqlData["id"];

    $query = $this->con->prepare("
      INSERT INTO
        videos (id, uploadedBy, filePath)
      VALUES
        (:id, :uploadedBy, :filePath)
    ");
    $query->bindParam(":id", $uuid);
    $query->bindParam(":uploadedBy", $uploadedBy);
    $url = "api/" . $filePath;
    $query->bindParam(":filePath", $url);

    if (!$query->execute()) {
      return false;
    }

    return $uuid;
  }

  public function convertVideoToMp4($tempFilePath, $finalFilePath)
  {
    $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1";

    $outputLog = array();
    exec($cmd, $outputLog, $returnCode);

    if ($returnCode != 0) {
      //Command failed
      foreach ($outputLog as $line) {
        $this->errorArray[] = $line;
      }
      return false;
    }

    return true;
  }

  private function deleteFile($filePath)
  {
    if (!unlink($filePath)) {
      $this->errorArray[] = "Could not delete file";
    }
    return true;
  }

  public function generateThumbnails($filePath, $videoId)
  {
    $thumbnailSize = "210x118";
    $numThumbnails = 3;
    $pathToThumbnail = "uploads/videos/thumbnails";

    $duration = $this->getVideoDuration($filePath);

    $this->updateDuration($duration, $videoId);

    for ($num = 1; $num <= $numThumbnails; $num++) {
      $imageName = uniqid() . ".jpg";
      $interval = ($duration * 0.8) / $numThumbnails * $num;
      $fullThumbnailPath = "$pathToThumbnail/$videoId-$imageName";

      $cmd = "$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";

      $outputLog = array();
      exec($cmd, $outputLog, $returnCode);

      if ($returnCode != 0) {
        //Command failed
        foreach ($outputLog as $line) {
          $this->errorArray[] = $line;
        }
      }

      $query = $this->con->prepare("
        INSERT INTO
          thumbnails (id, videoId, filePath, selected)
        VALUES
          (UUID(), :videoId, :filePath, :selected)
      ");
      $query->bindParam(":videoId", $videoId);
      $url = "api/" . $fullThumbnailPath;
      $query->bindParam(":filePath", $url);
      $selected = $num == 1 ? 1 : 0;
      $query->bindParam(":selected", $selected);

      $success = $query->execute();

      if (!$success) {
        $this->errorArray[] = "Error inserting thumbail";
        return false;
      }
    }

    return true;
  }

  private function getVideoDuration($filePath)
  {
    return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
  }

  private function updateDuration($duration, $videoId)
  {
    $hours = floor($duration / 3600);
    $mins = floor(($duration - ($hours * 3600)) / 60);
    $secs = floor($duration % 60);

    $hours = ($hours < 1) ? "" : $hours . ":";
    $mins = ($mins < 10) ? "0" . $mins . ":" : $mins . ":";
    $secs = ($secs < 10) ? "0" . $secs : $secs;

    $duration = $hours . $mins . $secs;

    $query = $this->con->prepare("
      UPDATE
        videos
      SET
        duration=:duration
      WHERE
        id=:videoId
    ");
    $query->bindParam(":duration", $duration);
    $query->bindParam(":videoId", $videoId);
    $query->execute();
  }

  public function getFirstError()
  {
    if (!empty($this->errorArray)) {
      return $this->errorArray[0];
    }

    return false;
  }

  public function getAllErrors()
  {
    return $this->errorArray;
  }
}
