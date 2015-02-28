<!DOCTYPE html>

<?php
require_once 'core/init.php';
$user = new User();
if(!$user->isLoggedIn())
  header('Location: index.php');

if(Input::exists()){ //se ho qualcosa nel post
  echo "input";
  if(Token::check(Input::get('token'))){
    $validate = new Validate();
    $validation = $validate->check($_POST, array(
      'password_current' => array(
        'required' => true,
        'min' => 6
      ),
      'password_new' => array(
        'required' => true,
        'min' => 6
      ),
      'password_new_again' => array(
        'required' => true,
        'matches' => 'password_new',
        'min' => 6
      )
    ));

    if($validation->passed()){
      try{
        if(Hash::make(Input::get('password_current'), $user->data()->salt) !== $user->data()->password){
          echo "your password is wrong";
        }else{
          $salt = Hash::salt(32);
          $user->update(array(
            'password' => Hash::make(Input::get('password_new'), $salt),
            'salt' => $salt
          ));

          Session::flash('success', 'You password has been updated.');
          header('Location: index.php');
        }
      }catch(Exception $e){
        die($e->getMessage());
      }
    }else{
      print_r($validation->errors());
    }
  }
}

include 'includes/header.php';

if($user->isLoggedIn()){
  echo '<p style="float:right;">Hello <a href="books.php">'.escape($user->data()->login).'</a>!</p>';
} else {
  echo '<p style="float:right;">You need to <a href="login.php">log in</a> or <a href="register.php">register</a></p>';
}
?>

<form style="text-align:right;" action="" method="post">
  <table style="width: 35%; margin:20px auto 15px;">
  <tr><td>Current password:</td><td><input type="password" name="password_current" id="password_current" value="" autocomplete="off"></td></tr>
  <tr><td>New password:</td><td><input type="password" name="password_new" id="password_new" value="" autocomplete="off"></td></tr>
  <tr><td>New password again:</td><td><input type="password" name="password_new_again" id="password_new_again" value="" autocomplete="off"></td></tr>
  <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
  <tr><td></td><td style="text-align:left;"><input type="submit" value="Change password"></td></tr>
  </table>
</form>

<?php
include 'includes/footer.php';
?>
        