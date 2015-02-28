<?php

//sanitize data, comming in and out data

//escape function
function escape($string){
  return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

?>