<?php
require_once 'core/init.php';
$user = new User();
if(!$user->isLoggedIn())
  header('Location: index.php');

include 'includes/header.php';

if(Input::exists('get')){
  $idfilm = Input::get('film');
  $day = Input::get('day');
  $hour = Input::get('hour');
  $dbconn = DB::getInstance();
  
  echo "You are booking the show of ".$day." at ".$hour."! ";
  $seatsfree = 0;
  foreach($dbconn->query('SELECT r.seats, sum(b.seatsnumber), s.idshow
              FROM (rooms r LEFT JOIN shows s ON r.idroom=s.idroom LEFT JOIN bookings b ON s.idshow=b.idshow), films f
              WHERE f.idfilm=s.idfilm AND s.day=:day AND s.hour=:hour AND f.idfilm=:idfilm
              GROUP BY r.seats, s.idshow;', array('day'=>$day, 'hour'=>$hour, 'idfilm' =>$idfilm))->results() as $r) { 
    $seatsocc = $r->sum;
    $seatsfree = $r->seats - $seatsocc;
    $idshow = $r->idshow; 
  }

  echo 'There are '.$seatsfree.' free seats!<br><br>';
  if(Input::exists()){
    if(Token::check(Input::get('token'))){
      $nseats = Input::get('nseats');

      if (is_numeric($nseats) && $nseats<5 && $nseats>0 && $nseats<=$seatsfree) {

        $params = array(
          'nseats' => $nseats,
          'idshow' => $idshow,
          'userlogin' => escape($user->data()->login)
        );

        if($dbconn->query("select new_book(:nseats, :idshow, :userlogin)", $params)->error()) {
          throw new Exception("Error");
        }
        header('Location: bookings.php');
      }
      else
        echo '<br>Error: max 4 but less than free spaces<br>';
    }
  }


  if ($seatsfree>0) {
    echo '
      Select the number of seats you want to book<br>
      <form action="book.php?film='.$idfilm.'&day='.$day.'&hour='.$hour.'" method="post">
        (max: 4)    <select name="nseats">';
            for ($i=1; $i<=4 && $i<=$seatsfree; $i++) {
              echo '<option value="'.$i.'">'.$i.'</option>';
            }
    echo '
          </select>
        <input type="hidden" name="token" value='.Token::generate().'>
        <input type="submit" value="Book now">
      </form>';
  }
}
include 'includes/footer.php';

?>
