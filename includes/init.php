<?php
define('C5_ENVIRONMENT_ONLY', true);
define('C5_DIR', getcwd());
define('CONSH_DIR', __DIR__."/..");
define('CONSH_CONFIG', C5_DIR."/consh/settings.php");
define('CONSH_COMMANDS_DIR', CONSH_DIR."/commands/");
define('DEBUG', true);

require('functions.php');
require('command.php');
if(file_exists(CONSH_CONFIG)) {
	require(CONSH_CONFIG);
}
require(C5_DIR.'/config/site.php');
require('local_db.php');
require('ssh.php');