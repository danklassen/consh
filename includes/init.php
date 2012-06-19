<?php
/**
 * C5_DIR directory of the concrete5 install
 * @package  Base
 * @since  0.1
 */
define('C5_DIR', getcwd());

/**
 * the consh directory
 * @package  Base
 * @since  0.1
 */
define('CONSH_DIR', __DIR__."/..");

/**
 * where the config settings reside
 * @package  Base
 * @since  0.1
 */
define('CONSH_CONFIG', C5_DIR."/consh/settings.php");

/**
 * the folder where the commands start
 * @package  Base
 * @since  0.1
 */
define('CONSH_COMMANDS_DIR', CONSH_DIR."/commands/");

/**
 * do we want debugging output?
 * @package  Base
 * @since  0.1
 */
define('DEBUG', true);

/**
 * the current version of consh
 * @package  Base
 * @since  0.1
 */
define('CONSH_VERSION', 0.1);

require('functions.php');
require('cli_colors.php');
require('command.php');
checkConfig($argv);

$args = array();
if (count($argv) < 2) {
	show_help();
	exit;
} else if (count($argv) > 2) {
  $args = array_slice($argv, 2);
}
$userCommand = $argv[1];

if (!file_exists(C5_DIR.'/config/site.php')) {
	output("Please make sure the file ".C5_DIR.'/config/site.php exists', 'warning');
	output("This does not look like a concrete5 install", 'error');
	die();
}
require(C5_DIR.'/config/site.php');
require('local_db.php');
require('ssh.php');