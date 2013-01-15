<?php
/**
 * C5_DIR directory of the concrete5 install
 * @package  Base
 * @since  0.1
 */
define('C5_DIR', getcwd());

define('C5_CONCRETE_DIR', C5_DIR."/concrete");

/**
 * the consh directory
 * @package  Base
 * @since  0.1
 */
define('CONSH_DIR', dirname(__FILE__)."/..");

/**
 * where the config settings reside
 * @package  Base
 * @since  0.1
 */
define('CONSH_CONFIG', C5_DIR . "/consh/settings.php");

/**
 * the folder where the commands start
 * @package  Base
 * @since  0.1
 */
define('CONSH_COMMANDS_DIR', CONSH_DIR . "/commands/");

/**
 * local commands folder
 * @package  Base
 * @since  0.2
 */
define('CONSH_COMMANDS_LOCAL_DIR', CONSH_DIR . "/commands-local/");

/**
 * the folder which houses the skeletons
 *
 * @package  @base
 * @since  0.2
 */
define('CONSH_SKELETON_DIR', CONSH_DIR . "/skeletons/");

/**
 * the folder which houses the skeletons local to this installation
 *
 * @package  @base
 * @since  0.2
 */
define('CONSH_SKELETON_LOCAL_DIR', CONSH_DIR . "/skeletons-local/");

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
define('CONSH_VERSION', 0.2);

/**
 * allow us to include some concrete5 files
 * @var boolean
 * @package Base
 */
define('C5_EXECUTE', true);


require 'functions.php';
require 'cli_colors.php';
require 'command.php';
require CONSH_DIR.'/models/hook.php';
require CONSH_DIR."/models/theme_skeleton.php";
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

// see if there was a passed in --pgk=package_name option passed in and set it as a variable
// unset it from the $args array though so it doesn't get picked up by the commands themselves
$pkg = preg_grep("/^--pkg=/", $args);
if (!empty($pkg)) {
    $keys = array_keys($pkg);
    unset($args[$keys[0]]);
    $pkg = str_replace("--pkg=", "", array_shift($pkg));
}

require C5_DIR.'/config/site.php';
require 'local_db.php';
require 'ssh.php';
