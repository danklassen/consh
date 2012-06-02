<?php

function getInput($msg){
  fwrite(STDOUT, "$msg: ");
  $varin = trim(fgets(STDIN));
  return $varin;
}

function show_help() {
	$msg =<<<EOL
Sorry, the command you entered was not recognized. Currently implented commands are:
db:pull\t\tpull the remote database
files:pull\tpull the files directory from the remote server

EOL;
	print $msg;
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
	$path = __DIR__ . "/commands/" . convertClassNameToPath($className).".php";
	require($path);
}

function debug($msg) {
	print $msg."\n";
}