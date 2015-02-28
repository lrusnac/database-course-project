<?php
require_once 'core/init.php';
$user = new User();

if(Input::exists()){ // se ho qualcosa nel post
  if(Token::check(Input::get('token'))){ //se il token corrisponde
    $validate = new Validate();
    $validation = $validate->check($_POST, array(
      'username' => array(
        'required' => true,
        'min' => 2,
        'max' => 20
      ),
      'password' => array(
        'required' => true,
        'min' => 6
      )
    ));

    if($validation->passed()){
      $user = new User();

      $remember = (Input::get('remember') === 'on');

      $login = $user->login(Input::get('username'), Input::get('password'), $remember);

      if($login){
        //Session::flash('success', 'You Logged in successfully');
        header('Location: index.php');
      }else{
        $message = "Sorry, wrong login";
      }
      
    }else{
      print_r($validation->errors());
    }
  }
}

include 'includes/header.php';
?>

<div style="float:right;">
  <?php 
  if (isset($message))
    echo $message; 
  ?>
  <form style="text-align:right;" action="" method="post">
    <table style="width: 100%; margin:0 0 15px;">
    <tr><td>Username:</td><td><input type="text" name="username" id="username" value="" autocomplete="off"></td></tr>
    <tr><td>Password:</td><td><input type="password" name="password" id="password" value="" autocomplete="off"></td></tr>
    <tr><td>Remember me:</td><td style="text-align:left;"><input type="checkbox" name="remember" id="remember"></td></tr>
    <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
    <tr><td></td><td style="text-align:left;"><input type="submit" value="Login"></td></tr>
    </table>
  </form>
</div>

<?php

include 'includes/todayshow.php';

include 'includes/footer.php';

?>