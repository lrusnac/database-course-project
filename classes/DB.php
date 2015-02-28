<?php
class DB{
  //singleton
  private static $_instance = null;
  private $_pdo = null, $_query = null, $_error = false, $_results = null, $_count = 0;

  // costruttore privato in quanto e' un singleton
  private function __construct(){
    try{
      $this->_pdo = new PDO('pgsql:host='.Config::get('pgsql/host').' port='.Config::get('pgsql/port').' dbname='.Config::get('pgsql/dbname').' user='.Config::get('pgsql/username').' password='.Config::get('pgsql/password'));
    } catch(PDOException $e){
      die($e->getMessage());
    }
  }

  //lazy instantiation, connect to db only once
  public static function getInstance(){
    if(!isset(self::$_instance)){
      self::$_instance = new DB();
    }
    return self::$_instance;
  }

  public function query($sql, $params = array()){
    $this->_error = false;
    if($this->_query = $this->_pdo->prepare($sql)){
      if ($this->_query->execute($params)) {
        $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
        $this->_count = $this->_query->rowCount();
      }else{
        print_r($this->_pdo->errorInfo());
        $this->_error = true;
      }
    }
    return $this;
  }

  public function results(){
    return $this->_results;
  }

  public function error(){
    return $this->_error;
  }

  public function count(){
    return $this->_count;
  }

  public function first(){
    return $this->_results[0];
  }

}
?>
