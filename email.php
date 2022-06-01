      
      
 <?php
if(file_get_contents('email_running.txt') == ''){ // here the script will work
$file = fopen('email_running.txt', 'w+');
fwrite($file, 'running');
fclose($file);
}else{
echo 'could not run';
exit(); // if it find 'run' in run.txt the script will stop
}

ignore_user_abort(true);
set_time_limit(0);
flush();
/////////////do stuff
while (true) {
  if(file_get_contents('email_running.txt') == 'stop') die();


  sleep(10); // sleep 10ms to unload the CPU
  $existingGames = array();
  $currGames = file("current_games2.txt");
  foreach($currGames  as $line ){
    if($line == '') continue;
      file_put_contents('email_log.txt',PHP_EOL.$line, FILE_APPEND);
    $game = file_get_contents("games/".$line.".txt");
    $game = json_decode($game);
    $p = $game->players[$game->whosTurn]->name;
      file_put_contents('email_log.txt',PHP_EOL.$p."'s turn", FILE_APPEND);
    $pemail = '';
    if(strtolower($p) == 'charlie') $pemail = 'charliewynn+onitama@gmail.com';
    if(strtolower($p) == 'brittany') $pemail = 'brittanyroll+onitama@gmail.com';
    if(strtolower($p) == 'guy') $pemail = 'guygoh+onitama@gmail.com';
    $continue = true;
    $time = time()*1000;
    if($time-$game->lastTurn<5*60*1000) { 
      file_put_contents('email_log.txt',PHP_EOL.$time."-".$game->lastTurn, FILE_APPEND);     
      file_put_contents('email_log.txt',PHP_EOL."hasn't been 5 minutes", FILE_APPEND);
      $continue = false;//been less than 5 mins since turn
    }

    //if it's emailed since the last turn
    if($continue && $game->lastEmailed > $game->lastTurn && $time-$game->lastEmailed < 12*60*60*1000) {
      file_put_contents('email_log.txt',PHP_EOL."has emailed, and has'nt been 24 hours", FILE_APPEND);
      $continue = false;
    }
    if($continue) {
      file_put_contents('email_log.txt',PHP_EOL.'Emailing: '.$pemail, FILE_APPEND);

    if($pemail != '') {
     mail($pemail, "your turn in onitama", "play <a href='http://cwynn.com/onitama/onitama.html?gid=".$line."&p=".$game->whosTurn."'>here</a>");
       $game->lastEmailed = $time;
      file_put_contents('email_log.txt',PHP_EOL.'Set Time: '.$time, FILE_APPEND);

      file_put_contents("games/".$line.".txt", json_encode($game));
    }
}
    if($game->isOver) { }
    else if($pemail == '') {}
    else {
      array_push($existingGames, $line);
    }
  }

  file_put_contents('current_games.txt', implode(PHP_EOL, $existingGames ));

}
 
////////////stop stuff

$file = fopen('email_running.txt', 'w+');
fwrite($file, ''); //will delete run word for the next try ;)
fclose('email_running.txt');

?>