<?php

class Validate{
  private $_passed = false, $_errors = array(), $_db = null;
  function __construct(){
    $this->_db = DB::getInstance();
  }

  public function check($source, $items=array()){
    foreach ($items as $item => $rules) {
      foreach($rules as $rule => $rule_value){
        $value = trim(Input::get($item));

        if($rule === 'required' && $rule_value && empty($value)){
          $this->addError("{$item} is required");
        }else if(!empty($value)){
          switch ($rule) {
            case 'min':
              if(strlen($value) < $rule_value)
                $this->addError("{$value} must be minimum of {$rule_value} chars");
              break;
            case 'max':
              if(strlen($value) > $rule_value)
                $this->addError("{$value} must be maximum of {$rule_value} chars");
              break;
            case 'matches':
              if($value != trim(Input::get($rule_value)))
                $this->addError("{$item} don't match with {$rule_value}");
              break;
          }
        }
      }
    }

    if(empty($this->_errors)){
      $this->_passed = true;
    }

    return $this;
  }

  private function addError($error){
    $this->_errors[] = $error;
  }

  public function errors(){
    return $this->_errors;
  }

  public function passed(){
    return $this->_passed;
  }
}
?>