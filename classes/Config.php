<?php
class Config{

  public static function get($path = null){
    if($path){
      $config = $GLOBALS['config'];
      $path = explode('/', $path);

      foreach ($path as $bit) {
        if(array_key_exists($bit, $config)){
          $config = $config[$bit];
        }else
          return null;
      }
      return $config;
    }
    return null;
  }
}

?>