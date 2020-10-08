<?php

class Response
{
  static public function ok($data = null)
  {
    return self::out([
      "err" => 0,
      "data" => $data,
    ]);
  }

  static public function fail($msg, $err = 500)
  {
    return self::out([
      "err" => $err,
      "data" => $msg,
    ]);
  }

  static public function out($response)
  {
    header("Content-Type:application/json; charset=utf-8");
    echo json_encode($response);
  }
}
