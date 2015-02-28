<?php
require_once 'core/init.php';
$user = new User();
if(!$user->isLoggedIn())
  header('Location: index.php');

include 'includes/header.php';


$dbconn = DB::getInstance();
echo "<h1>These are your bookings</h1><br>";
$headtable = true;

foreach($dbconn->query('SELECT f.title, r.name, b.seatsnumber, s.day, s.hour, s.price, b.idbook
            FROM rooms r, shows s, bookings b, films f
            WHERE s.idshow=b.idshow AND r.idroom=s.idroom AND f.idfilm=s.idfilm AND b.userlogin=:login
            ORDER BY s.day, s.hour;', array('login'=>escape($user->data()->login)))->results() as $r) { 
  if($headtable){
    $headtable = false;
    echo '<table style="width: 100%; margin-bottom:10px;" border=1>'; 
    echo '<tr><td>Title</td><td>Room</td><td>Seats (number)</td><td>Day</td><td>Hour</td><td>Price</td><td></td></tr>';
  }
  echo '<tr>'; 
  echo '<td>'.$r->title.'</td><td>'.$r->name.'</td><td>'.$r->seatsnumber.'</td><td>'.$r->day.'</td><td>'.$r->hour.'</td><td>'.$r->price.'</td>';
  echo '<td><a href="bookings.php?del='.$r->idbook.'">Cancel</a></td>';
  echo '</tr>'; 
}
if($headtable){
  echo "You have no registered bookings!<br>";
}else{
  echo '</table>'; 
}

if(Input::exists('get')){
  $params = array(
    'idbook' => Input::get('del')
  );

  if(!$dbconn->query('SELECT * FROM bookings WHERE idbook=:idbook;', $params)->results()){
    echo "You can't delete this book";
  }else{

    if($dbconn->query("DELETE FROM bookings WHERE idbook=:idbook", $params)->error()) {
      print_r(DB::getInstance()->errorInfo());
      throw new Exception("Error");
    }
  
    header('Location: bookings.php');
  }
}


include 'includes/footer.php';
?>