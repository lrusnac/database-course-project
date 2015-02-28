<?php
require_once 'core/init.php';
/*if(Session::exists('success')){
  echo Session::flash('success');
}*/
$user = new User();

include 'includes/header.php';

if($user->isLoggedIn()){
  echo '<p style="float:right;">Hello <a href="bookings.php">'.escape($user->data()->login).'</a>!</p>';
} else {
  echo '<p style="float:right;">You need to <a href="login.php">log in</a> or <a href="register.php">register</a></p>';
}

include 'includes/todayshow.php';

include 'includes/footer.php';
?>


