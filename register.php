<?php
require_once 'core/init.php';
$user = new User();

if(Input::exists()){ //se ho qualcosa nel post
  if(Token::check(Input::get('token'))){

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
      ),
      'password_again' => array(
        'required' => true,
        'matches' => 'password'
      ),
      'fullname' => array(
        'required' => true,
        'min' => 2,
        'max' => 30
      ),
      'address' => array(
        'required' => true,
        'min' => 2,
        'max' => 30
      )
    ));


    if($validation->passed()){
      $user = new User();
      $salt = Hash::salt(32);
      $username = Input::get('username');
      $password = Hash::make(Input::get('password'), $salt);

      $params = array(
        'login' => $username,
        'password' => $password,
        'salt' => $salt,
        'fullname' => Input::get('fullname'),
        'address' => Input::get('address')
      );

      try{
        $user->create($params);
      }catch(Exception $e){
        die($e->getMessage());
      }
      Session::flash('success', 'You registered successfully');
      header('Location: index.php');
    }else{
      print_r($validation->errors());
    }

  }
}

include 'includes/header.php';
?>

<div style="float:right;">
  <form action="register.php" method = "post">
    <table style="width: 100%; margin:0 0 15px;">
    <tr><td>Username:</td><td><input type="text" name="username" id="username" value="" autocomplete="off"></td></tr>
    <tr><td>Password:</td><td><input type="password" name="password" id="password" value="" autocomplete="off"></td></tr>
    <tr><td>Password again:</td><td><input type="password" name="password_again" id="password_again" value="" autocomplete="off"></td></tr>
    <tr><td>Full name:</td><td><input type="text" name="fullname" id="fullname" value="" autocomplete="off"></td></tr>
    <tr><td>Address:</td><td><input type="text" name="address" id="address" value="" autocomplete="off"></td></tr>
    <input type='hidden' name='token' value="<?php echo Token::generate(); ?>"></td></tr>
    <tr><td></td><td><input type="submit" value="Register"></td></tr>
    </table>
  </form>
</div>

<?php

include 'includes/todayshow.php';

include 'includes/footer.php';

?>