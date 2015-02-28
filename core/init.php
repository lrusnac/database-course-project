<?php
//include everywhere, in every page
session_start();

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);
error_reporting(E_ALL | E_STRICT);

//array of configs
$GLOBALS['config'] = array(
  'pgsql' => array(
    'dbname' => "database",
    'host' => "host",
    'port' => 2345,
    'username' => "user",
    'password' => "pass"
  ),
  'remember' => array(
    'cookie_name' => 'hash',
    'cookie_expiry' => 604800 //in seconds
  ),
  'session' => array(
    'session_name' => 'user',
    'token_name' => 'token'
  )
);

//in questo modo faccio include di solo quello di cui ho bisogno e non di tutte le classi
spl_autoload_register(function($class){
  require_once 'classes/' . $class . '.php';
});

require_once 'functions/sanitize.php';

if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))){
  $hash = Cookie::get(Config::get('remember/cookie_name'));
  $hashCheck = DB::getInstance()->query("select * from users_session where hash = :hash", array("hash" => $hash));

  if($hashCheck->count()){
    $user = new User($hashCheck->first()->login);
    $user->login();
  }
}

?>