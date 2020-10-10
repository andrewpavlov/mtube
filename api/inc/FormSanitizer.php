<?php

class FormSanitizer
{

  public static function string($inputText)
  {
    $inputText = strip_tags($inputText);
    $inputText = str_replace(" ", "", $inputText);
    $inputText = strtolower($inputText);
    $inputText = ucfirst($inputText);
    return $inputText;
  }

  public static function text($inputText)
  {
    return trim($inputText);
  }

  public static function guid($inputText)
  {
    return self::string($inputText);
  }

  public static function username($inputText)
  {
    $inputText = strip_tags($inputText);
    $inputText = str_replace(" ", "", $inputText);
    return $inputText;
  }

  public static function password($inputText)
  {
    $inputText = strip_tags($inputText);
    return $inputText;
  }

  public static function email($inputText)
  {
    $inputText = strip_tags($inputText);
    $inputText = str_replace(" ", "", $inputText);
    return $inputText;
  }
}
