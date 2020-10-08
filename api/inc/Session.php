<?php

class Session
{
  static public function start()
  {
    ob_start(); //Turns on output buffering 
    session_start();
  }

  static public function destroy()
  {
    session_destroy();
  }

  static public function get($section, $key = null, $def = null)
  {
    if (!isset($key)) {
      return getMapValue($_SESSION, $section, $def);
    } else
        if (!is_array($key)) {
      $key = array($key);
    }
    $path = array_merge(array($section), $key);
    return getMapValue($_SESSION, $path, $def);
  }

  static public function set($section, $val, $key = null)
  {
    $changed = false;
    if (isset($key)) {
      if (!isset($_SESSION[$section])) {
        $_SESSION[$section] = array();
      }
      if (
        !isset($_SESSION[$section][$key])
        || $_SESSION[$section][$key] !== $val
      ) {
        $_SESSION[$section][$key] = $val;
        $changed = true;
      }
    } else
        if (
      !isset($_SESSION[$section])
      || $_SESSION[$section] !== $val
    ) {
      $_SESSION[$section] = $val;
      $changed = true;
    }
    return $changed;
  }
};
