<?php

function getInput($msg, $default = ''){
  fwrite(STDOUT, "$msg: ");
  $varin = trim(fgets(STDIN));
  if (empty($varin)) {
  	$varin = $default;
  }
  return $varin;
}

function show_help() {
	$list = new CommandList();
  $commands = $list->getCommands();
	output("Current Commands:");
	foreach($commands as $command) {
    output(str_pad($command->name, 20) . $command->description);
  }
}

function convertCommandToObject($cmd) {
	$className = str_replace(' ', '', ucwords(str_replace(':', ' ', $cmd)));
	return new $className();
}

function convertClassNameToPath($className) {
	preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $className, $matches);
  $ret = $matches[0];
  foreach ($ret as &$match) {
    $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
  }
  return implode('/', $ret);
}

function __autoload($className) {
	$path = CONSH_COMMANDS_DIR . convertClassNameToPath($className).".php";
	require($path);
}

function debug($msg) {
	if(DEBUG) {
		print $msg."\n";
	}
}

function output($msg, $type = '') {
	if($type != '') {
    $colorCli = new CliColors();
    if(strtolower($type) == 'error') {
      $msg = $colorCli->getColoredString($msg, 'red');
    } else if (strtolower($type) == 'warning') {
      $msg = $colorCli->getColoredString($msg, 'yellow');
    } else if (strtolower($type) == 'success') {
      $msg = $colorCli->getColoredString($msg, 'green');
    }
	}
	print $msg."\n";
}

function checkConfig($argv = array()) {
	if(file_exists(CONSH_CONFIG)) {
		require(CONSH_CONFIG);
	} else if ((count($argv) < 2) || $argv[1] != 'config') {
		output("Please run 'consh config' to configure consh");
		exit;
	}
}

/**
  * from http://brian.moonspot.net/status_bar.php.txt
  */
function showStatus($done, $total, $size=30) {

    static $start_time;

    // if we go over our bound, just ignore it
    if($done > $total) return;

    if(empty($start_time)) $start_time=time();
    $now = time();

    $perc=(double)($done/$total);

    $bar=floor($perc*$size);

    $status_bar="\r[";
    $status_bar.=str_repeat("=", $bar);
    if($bar<$size){
      $status_bar.=">";
      $status_bar.=str_repeat(" ", $size-$bar);
    } else {
       $status_bar.="=";
    }

    $disp=number_format($perc*100, 0);

    $status_bar.="] $disp%  $done/$total";

    $rate = ($now-$start_time)/$done;
    $left = $total - $done;
    $eta = round($rate * $left, 2);

    $elapsed = $now - $start_time;

    $status_bar.= " remaining: ".number_format($eta)." sec.  elapsed: ".number_format($elapsed)." sec.";

    echo "$status_bar  ";

    flush();

    // when done, send a newline
    if($done == $total) {
        echo "\n";
    }

}
