<?php
require_once 'core/init.php';
$user = new User();
if(!$user->isLoggedIn())
  header('Location: index.php');

if(Input::exists()){ //se ho qualcosa nel post
  if(Token::check(Input::get('token'))){
    $validate = new Validate();
    $validation = $validate->check($_POST, array(
      'name' => array(
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
      try{
        $user->update(array(
          'fullname' => Input::get('name'),
          'address' => Input::get('address')
        ));

        Session::flash('success', 'You details have been updated.');
        header('Location: index.php');

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
  echo '<p style="float:right;">Hello <a href="bookings.php">'.escape($user->data()->login).'</a>!</p>';
} else {
  echo '<p style="float:right;">You need to <a href="login.php">log in</a> or <a href="register.php">register</a></p>';
}
?>

<form style="text-align:right;" action="" method="post">
  <table style="width: 30%; margin:20px auto 15px;">
  <tr><td>Full name:</td><td><input type="text" name="name" id="name" value="<?php echo escape($user->data()->fullname); ?>"></td></tr>
  <tr><td>Address:</td><td><input type="text" name="address" id="address" value="<?php echo escape($user->data()->address); ?>"></td></tr>
  <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
  <tr><td></td><td style="text-align:left;"><input type="submit" value="Update"></td></tr>
  <table>
</form>
    
<?php
include 'includes/footer.php';
?>    