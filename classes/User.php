<?php
class User{
  private $_db = null, $_data = null, $_sessionName, $_cookieName;
  private $_isLoggedIn = false;

  function __construct($user = null){
    $this->_db = DB::getInstance();

    $this->_sessionName = Config::get('session/session_name');
    $this->_cookieName = Config::get('remember/cookie_name');

    if(!$user){
      if(Session::exists($this->_sessionName)){
        $user = Session::get($this->_sessionName);
        if($this->find($user))
          $this->_isLoggedIn = true;
        else{
          //log out
        }
      }
    }else{
      $this->find($user);
    }
  }

  public function create($params){
    echo "create";
    if($this->_db->query("select new_user(:login, :password, :salt, :fullname, :address)", $params)->error()){
      print_r($this->_db->error());
      throw new Exception("Error creating an account");
    }
  }

  public function find($username = null){
    if($username){
      $prepst = "select * from users where login = :login";
      $data = $this->_db->query($prepst, array("login" => $username));
      if($data->count()>0){
        $this->_data = $data->results();
        return true;
      }
    }
    return false;
  }

  public function login($username = null, $password = null, $remember = false){
    if(!$username && !$password && $this->exists()){
      Session::put($this->_sessionName, $this->_data[0]->login);
    }else{
      $user = $this->find($username);
      if($user){
        if($this->_data[0]->password === Hash::make($password, $this->_data[0]->salt)){
          Session::put($this->_sessionName, $this->_data[0]->login);

          if($remember){
            $hash = Hash::unique();
            $prepst = "select * from users_session where login = :login";
            $hashCheck = $this->_db->query($prepst, array("login" => $username));
            
            if(!$hashCheck->count()){
              $prepst = "insert into users_session (login, hash) values (:login, :hash)";
              $this->_db->query($prepst, array("login" => $username, "hash" => $hash));
            }else{
              $hash = $hashCheck->first()->hash;
            }
            Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
          }

          return true;
        }
      }
    }
    return false;
  }

  public function logout(){

    $this->_db->query("delete from users_session where login = :login", array("login" => $this->_data[0]->login));

    Session::delete($this->_sessionName);
    Cookie::delete($this->_cookieName);
    $this->_isLoggedIn = false;
  }

  public function update($fields = array(), $login = null){
    
    if(!$login && $this->isLoggedIn()){
      $login = $this->_data[0]->login;
    }

    $prepsmt = "update users set   ";

    foreach ($fields as $key => $value) {
      $prepsmt .= $key." = :".$key.", ";
    }

    $prepsmt = substr($prepsmt, 0, strlen($prepsmt)-2);

    $prepsmt .= " where login = :login";

    $fields['login'] = $login;

    if(!$this->_db->query($prepsmt, $fields)){
      throw new Exception('There was a problem updating');
    }
    
  }

  public function hasPermission($key){
    $group = $this->_data[0]->group;

    if($key == 'admin'){
      return ($group == 2);
    }

    return false;
  }

  public function data(){
    return $this->_data[0];
  }

  public function isLoggedIn(){
    return $this->_isLoggedIn;
  }

  public function exists(){
    return !empty($this->_data);
  }
}

?>