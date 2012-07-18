<?php

/**
 * get input from the user.
 *
 * Displays a message and returns the user's input
 * @package Base
 * @since  0.1
 * @param  string $msg message to prompt the user
 * @param  string $default default value to return if the user does not enter anything
 * @return string either the user's input or the passed in default if nothing was entered
 */
function getInput($msg, $default = ''){
  fwrite(STDOUT, "$msg: ");
  $varin = trim(fgets(STDIN));
  if (empty($varin)) {
  	$varin = $default;
  }
  return $varin;
}

/**
 * show a list of available commands
 * @package Base
 * @since  0.1
 * @return void
 */
function show_help() {
	$list = new CommandList();
  $commands = $list->getCommands();
	output("Current Commands:");
	foreach($commands as $command) {
    output(str_pad($command->name, 20) . $command->description);
  }
}

/**
 * convert a command like db:pull to the object new DbPull();
 * @package Base
 * @since  0.1
 * @param  string $cmd command to load such as db:pull
 * @return Object the instantiated object for the command such as DbPull
 */
function convertCommandToObject($cmd) {
	$className = str_replace(' ', '', ucwords(str_replace(':', ' ', $cmd)));
	return new $className();
}

function camelize($str = null) {
  $r1 = ucwords(str_replace(array('_', '-', '/'), ' ', $str));
  $r2 = str_replace(' ', '', $r1);
  return $r2;
}

/**
 * convert a classname like DbPull to the full path to the file such as db/pull
 * @package Base
 * @since  0.1
 * @param  string $className name of the class to convert to a path
 * @return string path of the file relative to the commands directory
 */
function convertClassNameToPath($className) {
	preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $className, $matches);
  $ret = $matches[0];
  foreach ($ret as &$match) {
    $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
  }
  return implode('/', $ret);
}

/**
 * function to load classes that haven't been defined yet.
 *
 * This loads from the commands/command_type/command.php
 * @package Base
 * @since  0.1
 * @param  string $className name of the class to load
 * @return void
 */
function __autoload($className) {
	$path = CONSH_COMMANDS_DIR . convertClassNameToPath($className).".php";
  if (file_exists($path)) {
	 require($path);
  } else {
    output('Command not found', 'error');
    debug("{$path} should define {$className}");
    die();
  }
}

/**
 * output a message if debugging is enabled
 * @package Base
 * @since  0.1
 * @param  string $msg message to display
 * @return void
 */
function debug($msg) {
	if(DEBUG) {
		output($msg, 'debug');
	}
}

/**
 * output a message to the console
 * @package Base
 * @param  string $msg  message to display
 * @param  string $type type of message (ie error, warning, success). Used for color coding the output
 * @since  0.1
 * @return void
 */
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

/**
 * check to see if the config file exists.
 *
 * If no config file is present then output a message with instructions to create one
 * @package Base
 * @param  array  $argv array of options from the command line.
 * @since  0.1
 * @return void
 */
function checkConfig($argv = array()) {
	if(file_exists(CONSH_CONFIG)) {
		require(CONSH_CONFIG);
	} else if ((count($argv) < 2) || $argv[1] != 'config') {
		output("Please run 'consh config' to configure consh", 'error');
		exit;
	}
}

/**
  * display a progress bar
  *
  * code is from http://brian.moonspot.net/status_bar.php.txt
  * @package Base
  * @since  0.1
  * @param int $done amount of progress
  * @param  int $total total amount of work to do
  * @param  int $size size of the progress bar
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
