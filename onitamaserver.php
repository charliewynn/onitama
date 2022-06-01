  <?php
  $gid = $_POST['gid'];
  $filename  = dirname(__FILE__).'/games/'.$gid.'.txt';
 
  // store new message in the file
  $game = isset($_POST['game']) ? $_POST['game'] : '';
  if ($game != '')
  {
    if(!file_exists($filename)) {
     echo 'new game '.$gid.'-'.file_exists('current_games2.txt');
      file_put_contents('current_games2.txt',PHP_EOL.$gid, FILE_APPEND);
    }
    file_put_contents($filename,$game);
    die();
  }
  // infinite loop until the data file is not modified
  $lastmodif    = isset($_POST['timestamp']) ? $_POST['timestamp'] : 0;

  $currentmodif = filemtime($filename)*1000;
  if(!$currentmodif)
    die("Game Not Found");
  
  if($lastmodif != 0)
  {
    while ($currentmodif <= $lastmodif) // check if the data file has been modified
    {
      usleep(200000); //
      clearstatcache();
      $currentmodif = filemtime($filename)*1000;
    }
  }
 
  // return a json array
  $response = array();
  $response['game']       = file_get_contents($filename);
  $response['timestamp'] = $currentmodif;
  echo json_encode($response);
  flush();
 
  ?>