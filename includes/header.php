
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="description" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="google-site-verification" content="0DZtHDsA_BOHlo2Qg_PvyIG1MEjkTCGJkIrUA6rd088" />
    <title>Cinema - DB Project</title>
    <style type ="text/css">
     @import url("./css/style.css");
    </style>
  </head>
  
  <body>
  
    <!-- Header -->
    <div id="header">
      <div id="cont">
  
        <!-- Navigation Bar -->
        <div id="nav">
          <ul>
            <?php
            if($user->isLoggedIn()){
            ?>
              <li><a href="./index.php">Home</a></li>
              <li><a href="./update.php">Update details</a></li>
              <li><a href="./changepassword.php">Change password</a></li>
              <li><a href="./bookings.php">Bookings</a></li>
              <?php
                if($user->isLoggedIn() && $user->hasPermission('admin')){
                  echo '<li><a href="./admin.php">Admin</a></li>';
                }
              ?>
              <li><a href="./logout.php">Log out</a></li>
            <?php
            }else{
              ?>
              <li><a href="./index.php">Home</a></li>
              <li><a href="./login.php">Login</a></li>
              <li><a href="./register.php">Register</a></li>
              <?php
            }
            ?>
          </ul>
        </div>
        <!-- Navigation Bar -->
    
        <!--<img src="images/.png" class="logo" >-->
      </div>
    </div>
    <!-- Header -->

    <div id="cont">
      <div id="layout">

        <!-- Center -->
        <div id="center">