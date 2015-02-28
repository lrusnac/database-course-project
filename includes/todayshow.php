<?php
  $dbconn = DB::getInstance();
  echo '<h1>Today shows</h1>'; 

  $results = $dbconn->query('SELECT DISTINCT f.* FROM shows s, films f WHERE s.idfilm = f.idfilm AND s.day=current_date')->results();

  foreach($results as $r) {
    echo "Title: ".$r->title;
    echo "<br>Genre: ".$r->genre;
    echo "<br>Cast members: ".$r->castmembers;
    echo "<br>Running time: ".$r->runningtime." minutes";
    echo "<br>Directed by: ".$r->directedby;
    echo "<br>Shows: ";
    foreach($dbconn->query('SELECT s.day, s.hour FROM shows s WHERE s.day=current_date AND s.idfilm=:idfilm ORDER BY s.hour', array('idfilm' => $r->idfilm))->results() as $z) { 
    ?>
      <a href="book.php?film=<?php echo $r->idfilm; ?>&day=<?php echo $z->day; ?>&hour=<?php echo $z->hour; ?>"><?php echo $z->hour; ?>  </a>
    <?php
    }
    echo '<br><br>';
  }

?>