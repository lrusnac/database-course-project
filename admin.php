<?php

require_once 'core/init.php';
$user = new User();
if(!$user->isLoggedIn())
  header('Location: index.php');

if(!$user->hasPermission('admin'))
  header('Location: index.php');

include 'includes/header.php';
include 'includes/header_admin.php';

if(Input::exists('get')){
  $dbconn = DB::getInstance();
  $action = Input::get('action');

  switch ($action) {
    case 'films':
      //view all films and add the possibility to add some
      if(Input::get('del')){ //must delete a film
        if($dbconn->query("DELETE FROM bookings WHERE idshow = (select idshow from shows where idfilm=:idfilm);", array('idfilm' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=films');
        }
        if($dbconn->query("DELETE FROM shows WHERE idfilm=:idfilm;", array('idfilm' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=films');
        }
        if($dbconn->query("DELETE FROM films WHERE idfilm=:idfilm;", array('idfilm' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=films');
        }
      }
      
      if(Input::exists()){//add or edit a film to the database
        if(Token::check(Input::get('token'))){
          $validate = new Validate();
          $validation = $validate->check($_POST, array(
            'title' => array('required' => true),
            'genre' => array('required' => true),
            'castmembers' => array('required' => true),
            'directedby' => array('required' => true),
            'runningtime' => array('required' => true)
          ));

          if($validation->passed() && is_numeric(Input::get('runningtime')) && Input::get('runningtime')>0){
            $params = array(
              'title' => Input::get('title'),
              'genre' => Input::get('genre'), 
              'castmembers' => Input::get('castmembers'), 
              'directedby' => Input::get('directedby'), 
              'runningtime' => Input::get('runningtime')
            );
            if(strcmp(Input::get('mode'), 'add') == 0){ //add new film
              if($dbconn->query("select new_film(:title, :genre, :castmembers, :directedby, :runningtime);", $params)->error()) {
                throw new Exception("Error");
              }
              header('Location: admin.php?action=films');
            }else{ //update the film
              $params['idfilm'] = Input::get('mode');
              if($dbconn->query("update films set title=:title, genre=:genre, castmembers=:castmembers, directedby=:directedby, runningtime=:runningtime where idfilm=:idfilm;", $params)->error()) {
                throw new Exception("Error");
              }
              header('Location: admin.php?action=films');
            }
          }else{
            echo "All fields must be filled and running time must be a number > 0!";
          }
        }
      }

      //list all films 
      $headtable = true;
      foreach($dbconn->query('SELECT * FROM films ORDER BY title')->results() as $r) { 
        if($headtable){
          $headtable = false;
          echo '<table style="width: 100%; margin-bottom:10px;" border=1>'; 
          echo '<tr><td>Title</td><td>Genre</td><td>Cast Members</td><td>Directed By</td><td>Running Time</td><td></td><td></td></tr>';
        }
        echo '<tr>'; 
        echo '<td>'.$r->title.'</td><td>'.$r->genre.'</td><td>'.$r->castmembers.'</td><td>'.$r->directedby.'</td><td>'.$r->runningtime.'</td>';
        echo '<td><a href="admin.php?action=films&del='.$r->idfilm.'">Cancel</a></td>';
        echo '<td><a href="admin.php?action=films&edit='.$r->idfilm.'">Edit</a></td>';
        echo '</tr>'; 
      }
      if($headtable){
        echo "There isn't any film!<br>";
      }else{
        echo '</table>'; 
      }

      // insert or edit form
      if(Input::get('add') || Input::get('edit')){ 
        $title = ''; $genre = ''; $castmembers = ''; $directedby = ''; $runningtime = '';
        if(Input::get('edit')){
          foreach($dbconn->query('SELECT * FROM films where idfilm=:i', array('i' => Input::get('edit')))->results() as $r) {
            $title = $r->title;
            $genre = $r->genre;
            $castmembers = $r->castmembers;
            $directedby = $r->directedby;
            $runningtime = $r->runningtime;
          }
          if(!$title){
            header('Location: admin.php?action=films');
          }
        }

        ?>
        <form style="text-align:right;" action="admin.php?action=films" method="post">
          <table style="width: 50%; margin:20px auto 15px;">
          <tr><td>Title:</td><td><input type="text" name="title" size=50 value="<?php echo $title; ?>"></td></tr>
          <tr><td>Genre:</td><td><input type="text" name="genre" size=50 value="<?php echo $genre; ?>"></td></tr>
          <tr><td>Cast members:</td><td><input type="text" name="castmembers" size=50 value="<?php echo $castmembers; ?>"></td></tr>
          <tr><td>Directed by:</td><td><input type="text" name="directedby" size=50 value="<?php echo $directedby; ?>"></td></tr>
          <tr><td>Running time:</td><td><input type="text" name="runningtime" size=50 value="<?php echo $runningtime; ?>"></td></tr>
          <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
          <input type='hidden' name='mode' value="<?php echo $title ? Input::get('edit') : 'add'; ?>">
          <tr><td></td><td style="text-align:left;"><input type="submit" value="<?php echo $title ? 'Edit' : 'Add'; ?>"></td></tr>
          <table>
        </form>
      <?php
      }else{
        echo '<br><center><a href="admin.php?action=films&add=t">Add new film</a></center><br>';
      }

      break;
    case 'rooms':
      if(Input::get('del')){ //must delete a room

        if($dbconn->query("DELETE FROM bookings WHERE idshow = (select idshow from shows where idroom=:idroom);", array('idroom' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=films');
        }
        if($dbconn->query("DELETE FROM shows WHERE idroom=:idroom;", array('idroom' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=films');
        }

        if($dbconn->query("DELETE FROM rooms WHERE idroom=:i;", array('i' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=rooms');
        }
      }

      if(Input::exists()){//add or edit a room to the database
        if(Token::check(Input::get('token'))){
          $validate = new Validate();
          $validation = $validate->check($_POST, array(
            'name' => array('required' => true),
            'seats' => array('required' => true)
          ));

          if($validation->passed() && is_numeric(Input::get('seats')) && Input::get('seats')>0){
            $params = array(
              'name' => Input::get('name'),
              'seats' => Input::get('seats')
            );
            if(strcmp(Input::get('mode'), 'add') == 0){ //add new room
              if($dbconn->query("select new_room(:name, :seats);", $params)->error()) {
                throw new Exception("Error");
              }
              header('Location: admin.php?action=rooms');
            }else{ //update the room
              $params['idroom'] = Input::get('mode');
              if($dbconn->query("update rooms set name=:name, seats=:seats where idroom=:idroom;", $params)->error()) {
                throw new Exception("Error");
              }
              header('Location: admin.php?action=rooms');
            }
          }else{
            echo "All fields must be filled seats must be (numeric) > 0!";
          }
        }
      }

      //list all rooms 
      $headtable = true;
      foreach($dbconn->query('SELECT * FROM rooms ORDER BY name')->results() as $r) { 
        if($headtable){
          $headtable = false;
          echo '<table style="width: 100%; margin-bottom:10px;" border=1>'; 
          echo '<tr><td>Name</td><td>Seats</td><td></td><td></td></tr>';
        }
        echo '<tr>'; 
        echo '<td>'.$r->name.'</td><td>'.$r->seats.'</td>';
        echo '<td><a href="admin.php?action=rooms&del='.$r->idroom.'">Cancel</a></td>';
        echo '<td><a href="admin.php?action=rooms&edit='.$r->idroom.'">Edit</a></td>';
        echo '</tr>'; 
      }
      if($headtable){
        echo "There isn't any room!<br>";
      }else{
        echo '</table>'; 
      }

      // insert or edit form
      if(Input::get('add') || Input::get('edit')){ 
        $name = ''; $seats = '';
        if(Input::get('edit')){
          foreach($dbconn->query('SELECT * FROM rooms where idroom=:i', array('i' => Input::get('edit')))->results() as $r) {
            $name = $r->name;
            $seats = $r->seats;
          }
          if(!$name){
            header('Location: admin.php?action=rooms');
          }
        }

        ?>
        <form style="text-align:right;" action="admin.php?action=rooms" method="post">
          <table style="width: 50%; margin:20px auto 15px;">
          <tr><td>Title:</td><td><input type="text" name="name" size=50 value="<?php echo $name; ?>"></td></tr>
          <tr><td>Seats:</td><td><input type="text" name="seats" size=50 value="<?php echo $seats; ?>"></td></tr>
          <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
          <input type='hidden' name='mode' value="<?php echo $name ? Input::get('edit') : 'add'; ?>">
          <tr><td></td><td style="text-align:left;"><input type="submit" value="<?php echo $name ? 'Edit' : 'Add'; ?>"></td></tr>
          <table>
        </form>
      <?php
      }else{
        echo '<br><center><a href="admin.php?action=rooms&add=t">Add a room</a></center><br>';
      }

      break;
    case 'shows':
      $filmlist = array();
      $roomlist = array();
      //prepare an array with films and one with rooms

      foreach($dbconn->query('SELECT idfilm, title FROM films')->results() as $r) { 
        $filmlist[$r->idfilm] = $r->title;
      }
      foreach($dbconn->query('SELECT idroom, name FROM rooms')->results() as $r) { 
        $roomlist[$r->idroom] = $r->name;
      }

      if(Input::get('del')){ //must delete a show
        if($dbconn->query("DELETE FROM bookings WHERE idshow = :i;", array('i' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=films');
        }
        if($dbconn->query("DELETE FROM shows WHERE idshow=:i;", array('i' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=shows');
        }
      }

      if(Input::exists()){//add or edit a show to the database
        if(Token::check(Input::get('token'))){
          $validate = new Validate();
          $validation = $validate->check($_POST, array(
            'day' => array('required' => true),
            'hour' => array('required' => true),
            'price' => array('required' => true),
            'idfilm' => array('required' => true),
            'idroom' => array('required' => true)
          ));

          $d = DateTime::createFromFormat('Y-m-d', Input::get('day'));

          if($validation->passed() && is_numeric(Input::get('hour')) && Input::get('hour')<24 && is_numeric(Input::get('price')) && $d && $d->format('Y-m-d') == Input::get('day')){
            $params = array(
              'day' => Input::get('day'),
              'hour' => Input::get('hour'),
              'price' => Input::get('price'),
              'idfilm' => Input::get('idfilm'),
              'idroom' => Input::get('idroom')
            );
            if(strcmp(Input::get('mode'), 'add') == 0){ //add new room
              if($dbconn->query("select new_show(:day, :hour, :price, :idfilm, :idroom);", $params)->error()) {
                throw new Exception("Error");
              }
              header('Location: admin.php?action=shows');
            }else{ //update the show
              $params['idshow'] = Input::get('mode');
              if($dbconn->query("update shows set day=:day, hour=:hour, price=:price, idfilm=:idfilm, idroom=:idroom where idshow=:idshow;", $params)->error()) {
                throw new Exception("Error");
              }
              header('Location: admin.php?action=shows');
            }
          }else{
            echo "All fields must be filled, hour and price must be numeric, day must be valid!";
          }
        }
      }

      //list all shows 
      $headtable = true;
      foreach($dbconn->query('SELECT * FROM shows ORDER BY day')->results() as $r) { 
        if($headtable){
          $headtable = false;
          echo '<table style="width: 100%; margin-bottom:10px;" border=1>'; 
          echo '<tr><td>Day</td><td>Hour</td><td>Price</td><td>Film</td><td>Room</td><td></td><td></td></tr>';
        }
        echo '<tr>'; 
        echo '<td>'.$r->day.'</td><td>'.$r->hour.'</td><td>'.$r->price.'</td><td>'.$filmlist[$r->idfilm].'</td><td>'.$roomlist[$r->idroom].'</td>';
        echo '<td><a href="admin.php?action=shows&del='.$r->idshow.'">Cancel</a></td>';
        echo '<td><a href="admin.php?action=shows&edit='.$r->idshow.'">Edit</a></td>';
        echo '</tr>'; 
      }
      if($headtable){
        echo "There isn't any show!<br>";
      }else{
        echo '</table>'; 
      }

      // insert or edit form
      if(Input::get('add') || Input::get('edit')){ 
        $day = ''; $hour = ''; $price = ''; $idfilm = ''; $idroom = '';
        if(Input::get('edit')){
          foreach($dbconn->query('SELECT * FROM shows where idshow=:i', array('i' => Input::get('edit')))->results() as $r) {
            $day = $r->day;
            $hour = $r->hour;
            $price = $r->price;
            $idfilm = $r->idfilm;
            $idroom = $r->idroom;
          }
          if(!$day){
            header('Location: admin.php?action=shows');
          }
        }

        ?>
        <form style="text-align:right;" action="admin.php?action=shows" method="post">
          <table style="width: 50%; margin:20px auto 15px;">
          <tr><td>Day (yyyy-mm-dd):</td><td><input type="text" name="day" size=50 value="<?php echo $day; ?>"></td></tr>
          <tr><td>Hour:</td><td><input type="text" name="hour" size=50 value="<?php echo $hour; ?>"></td></tr>
          <tr><td>Price:</td><td><input type="text" name="price" size=50 value="<?php echo $price; ?>"></td></tr>
          <tr><td>Film:</td><td><select name="idfilm"><?php
            foreach ($filmlist as $id => $title) {
              $s = $id == $idfilm ? "selected" : "";
              echo '<option value="'.$id.'" '.$s.'>'.$title.'</option>';
            }?>
          </select></td></tr>
          <tr><td>Room:</td><td><select name="idroom"><?php
            foreach ($roomlist as $id => $name) {
              $s = $id == $idroom ? "selected" : "";
              echo '<option value="'.$id.'" '.$s.'>'.$name.'</option>';
            }?>
          </select></td></tr>
          <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
          <input type='hidden' name='mode' value="<?php echo $day ? Input::get('edit') : 'add'; ?>">
          <tr><td></td><td style="text-align:left;"><input type="submit" value="<?php echo $day ? 'Edit' : 'Add'; ?>"></td></tr>
          <table>
        </form>
      <?php
      }else{
        echo '<br><center><a href="admin.php?action=shows&add=t">Add a show</a></center><br>';
      }

      break;
    case 'bookings':
      $userlist = array();
      //prepare an array with users

      foreach($dbconn->query('SELECT login FROM users')->results() as $r) { 
        array_push($userlist, $r->login);
      }


      //get a show description: film day hour
      $showlist = array();
      $showcompletelist = array();
      foreach($dbconn->query('SELECT s.idshow, s.day, s.hour, f.title FROM shows s, films f
        WHERE s.idfilm = f.idfilm GROUP BY s.idshow, s.day, s.hour, f.title')->results() as $r) { 
        $showlist[$r->idshow] = $r->title.' '.$r->day.' '.$r->hour;
        $showcompletelist[$r->idshow] = array($r->title, $r->day, $r->hour);
      }

      //view all bookings and add the possibility to add some
      if(Input::get('del')){ //must delete a film
        if($dbconn->query("DELETE FROM bookings WHERE idbook=:idbook;", array('idbook' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=bookings');
        }
      }
      
      if(Input::exists()){//add or edit a film to the database
        if(Token::check(Input::get('token'))){
          $validate = new Validate();
          $validation = $validate->check($_POST, array(
            'seatsnumber' => array('required' => true),
            'idshow' => array('required' => true),
            'userlogin' => array('required' => true)
          ));
          $seatsfree = 0;
          foreach($dbconn->query('SELECT r.seats, sum(b.seatsnumber) FROM rooms r LEFT JOIN shows s ON r.idroom=s.idroom LEFT JOIN bookings b ON s.idshow=b.idshow
            WHERE s.idshow=:idshow GROUP BY r.seats', array('idshow' => Input::get('idshow')))->results() as $r) { 
            $seatsocc = $r->sum;
            $seatsfree = $r->seats - $seatsocc;
          }

          //control the seatsnumber
          if($validation->passed()){
            if(is_numeric(Input::get('seatsnumber')) && Input::get('seatsnumber')<5 && Input::get('seatsnumber')>0 && Input::get('seatsnumber')<=$seatsfree){ 
              $params = array(
                'seatsnumber' => Input::get('seatsnumber'),
                'idshow' => Input::get('idshow'), 
                'userlogin' => Input::get('userlogin')
              );
              if(strcmp(Input::get('mode'), 'add') == 0){ //add new film
                if($dbconn->query("select new_book(:seatsnumber, :idshow, :userlogin);", $params)->error()) {
                  throw new Exception("Error");
                }
                header('Location: admin.php?action=bookings');
              }else{ //update the book
                $params['idbook'] = Input::get('mode');
                if($dbconn->query("update bookings set seatsnumber=:seatsnumber, idshow=:idshow, userlogin=:userlogin where idbook=:idbook;", $params)->error()) {
                  throw new Exception("Error");
                }
                header('Location: admin.php?action=bookings');
              }
            }else{
              echo "<br>Error: max 4 but less than free spaces<br>";
            }
          }else{
            echo "<br>All fields must be filled!<br>";
          }
          
        }
      }

      //list all bookings 
      $headtable = true;
      foreach($dbconn->query('SELECT * FROM bookings ORDER BY userlogin')->results() as $r) { 
        if($headtable){
          $headtable = false;
          echo '<table style="width: 100%; margin-bottom:10px;" border=1>'; 
          echo '<tr><td>Seats Number</td><td>User</td><td>Film</td><td>Day</td><td>Hour</td><td></td><td></td></tr>';
        }
        echo '<tr>'; 
        echo '<td>'.$r->seatsnumber.'</td><td>'.$r->userlogin.'</td><td>'.$showcompletelist[$r->idshow][0].'</td><td>'.$showcompletelist[$r->idshow][1].'</td><td>'.$showcompletelist[$r->idshow][2].'</td>';
        echo '<td><a href="admin.php?action=bookings&del='.$r->idbook.'">Cancel</a></td>';
        echo '<td><a href="admin.php?action=bookings&edit='.$r->idbook.'">Edit</a></td>';
        echo '</tr>'; 
      }
      if($headtable){
        echo "There are no registered bookings!<br>";
      }else{
        echo '</table>'; 
      }

      // insert or edit form
      if(Input::get('add') || Input::get('edit')){ 
        $seatsnumber = ''; $idshow = ''; $userlogin = ''; 
        if(Input::get('edit')){
          foreach($dbconn->query('SELECT * FROM bookings where idbook=:i', array('i' => Input::get('edit')))->results() as $r) {
            $seatsnumber = $r->seatsnumber;
            $idshow = $r->idshow;
            $userlogin = $r->userlogin;
          }
          if(!$seatsnumber){
            header('Location: admin.php?action=bookings');
          }
        }

        ?>
        <form style="text-align:right;" action="admin.php?action=bookings" method="post">
          <table style="width: 50%; margin:20px auto 15px;">
          <tr><td>Seats number (max 4):</td><td><select name="seatsnumber">
          <?php
            for ($i=1; $i<=4; $i++) {
              $s = $i == $seatsnumber ? "selected" : "";
              echo '<option value="'.$i.'" '.$s.'>'.$i.'</option>';
            }
          ?>
          </select></td></tr>
          <tr><td>Show:</td><td><select name="idshow"><?php
            foreach ($showlist as $id => $desc) {
              $s = $id == $idshow ? "selected" : "";
              echo '<option value="'.$id.'" '.$s.'>'.$desc.'</option>';
            }?>
          </select></td></tr>
          <tr><td>User:</td><td><select name="userlogin"><?php
            foreach ($userlist as $id) {
              $s = strcmp($id, $userlogin) == 0 ? "selected" : "";
              echo '<option value="'.$id.'" '.$s.'>'.$id.'</option>';
            }?>
          </select></td></tr>
          <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
          <input type='hidden' name='mode' value="<?php echo $seatsnumber ? Input::get('edit') : 'add'; ?>">
          <tr><td></td><td style="text-align:left;"><input type="submit" value="<?php echo $seatsnumber ? 'Edit' : 'Add'; ?>"></td></tr>
          <table>
        </form>
      <?php
      }else{
        echo '<br><center><a href="admin.php?action=bookings&add=t">Add a booking</a></center><br>';
      }

      break;
    case 'users':
      //view all users and add the possibility to add some
      if(Input::get('del')){ //must delete a user
        if($dbconn->query("DELETE FROM bookings WHERE userlogin=:login;", array('login' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=users');
        }

        if($dbconn->query("DELETE FROM users WHERE login=:login;", array('login' => Input::get('del')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=users');
        }
      }

      if(Input::get('gr')){ //change group
        if($dbconn->query('update users set "group"=:v where login=:l;', array('v'=>Input::get('v'), 'l'=>Input::get('gr')))->error()) {
          echo "Error!";
        }else{
          header('Location: admin.php?action=users');
        }
      }
      
      if(Input::exists()){//add or edit a user to the database
        if(Token::check(Input::get('token'))){
          $validate = new Validate();
          $validation = $validate->check($_POST, array(
            'login' => array(
              'required' => true,
              'min' => 2,
              'max' => 20
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
            $salt = Hash::salt(32);
            $params = array(
              'nlogin' => Input::get('login'),
              'fullname' => Input::get('fullname'),
              'address' => Input::get('address'), 
              'salt' => $salt,
              'pass' => Hash::make(Input::get('pass'), $salt)
            );
            
            if(strcmp(Input::get('mode'), 'add') == 0){ //add new user

              if($dbconn->query("select new_user(:nlogin, :pass, :salt, :fullname, :address)", $params)->error()) {
                throw new Exception("Error");
              }
              header('Location: admin.php?action=users');
            }else{ //update the user
              $params['login'] = Input::get('mode');
              $params['salt'] = Input::get('salt');
              $params['pass'] = Hash::make(Input::get('pass'), Input::get('salt'));
              
              if($dbconn->query("update users set fullname=:fullname, address=:address, salt=:salt, login=:nlogin where login=:login;", $params)->error()) {
                throw new Exception("Error");
              }

              if(Input::get('pass')){
                if($dbconn->query("update users set password=:pass where login=:login;", $params)->error()) {
                  throw new Exception("Error");
                }
              }

              header('Location: admin.php?action=users');
            }
          }else{
            echo "All fields must be filled!";
          }
        }
      }

      //list all users 
      $headtable = true;
      foreach($dbconn->query('SELECT * FROM users ORDER BY login')->results() as $r) { 
        if($headtable){
          $headtable = false;
          echo '<table style="width: 100%; margin-bottom:10px;" border=1>'; 
          echo '<tr><td>Login</td><td>Full name</td><td>Address</td><td>Group</td><td></td><td></td></tr>';
        }
        echo '<tr>'; 
        echo '<td>'.$r->login.'</td><td>'.$r->fullname.'</td><td>'.$r->address.'</td>';
        if($r->group == 1)
          echo '<td><a href="admin.php?action=users&gr='.$r->login.'&v=2">Make admin</a></td>';
        else
          echo '<td><a href="admin.php?action=users&gr='.$r->login.'&v=1">Make user</a></td>';
        echo '<td><a href="admin.php?action=users&del='.$r->login.'">Cancel</a></td>';
        echo '<td><a href="admin.php?action=users&edit='.$r->login.'">Edit</a></td>';
        echo '</tr>'; 
      }
      if($headtable){
        echo "There isn't any user!<br>";
      }else{
        echo '</table>'; 
      }

      // insert or edit form
      if(Input::get('add') || Input::get('edit')){ 
        $fullname = ''; $address = ''; $login = ''; $salt = '';
        if(Input::get('edit')){
          foreach($dbconn->query('SELECT * FROM users where login=:i', array('i' => Input::get('edit')))->results() as $r) {
            $fullname = $r->fullname;
            $address = $r->address;
            $login = $r->login;
            $salt = $r->salt;
          }
          if(!$fullname){
            header('Location: admin.php?action=users');
          }
        }

        ?>
        <form style="text-align:right;" action="admin.php?action=users" method="post">
          <table style="width: 50%; margin:20px auto 15px;">
          <tr><td>Login:</td><td><input type="text" name="login" size=50 value="<?php echo $login; ?>"></td></tr>
          <tr><td>Full name:</td><td><input type="text" name="fullname" size=50 value="<?php echo $fullname; ?>"></td></tr>
          <tr><td>Address:</td><td><input type="text" name="address" size=50 value="<?php echo $address; ?>"></td></tr>
          <tr><td>Password:</td><td><input type="password" name="pass" size=50 value=""></td></tr>
          <input type="hidden" name="salt" value="<?php echo $salt; ?>">
          <input type='hidden' name='token' value="<?php echo Token::generate(); ?>">
          <input type='hidden' name='mode' value="<?php echo $fullname ? Input::get('edit') : 'add'; ?>">
          <tr><td></td><td style="text-align:left;"><input type="submit" value="<?php echo $fullname ? 'Edit' : 'Add'; ?>"></td></tr>
          <table>
        </form>
      <?php
      }else{
        echo '<br><center><a href="admin.php?action=users&add=t">Add new user</a></center><br>';
      }
      break;
  }

}

include 'includes/footer.php';
?>