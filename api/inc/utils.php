<?php

function &getMapValue(array &$map, $key, $def = null)
{
  if (!is_array($key)) {
    $key = array($key);
  }
  $ptr = &$map;
  foreach ($key as $k) {
    if (!isset($ptr[$k])) {
      return $def;
    }
    $ptr = &$ptr[$k];
  }
  return $ptr;
}
