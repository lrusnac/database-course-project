<?php
class Hash{
  
  public static function make($string, $salt=''){
    return hash('sha256', $string.$salt);
  }

  public static function salt($length){
    //return "ciaooo";
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
    //return mcrypt_create_iv($length);
  }

  public static function unique(){
    return self::make(uniqid());
  }
}
?>