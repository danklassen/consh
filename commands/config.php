<?php

class Config extends Command {

	public function __construct() {
    $this->name = "Config";
    $this->description = "Create a configuration file";
    $this->help = "";
  }

	public function run($options = array()) {
		if (file_exists(CONSH_CONFIG)) {
			debug("Config file already exists");
			return;
		} else {
			output('Please provide the remote host information');
			$remote_host = getInput('Remote host (ie: domain.com)');
			$remote_user = getInput('Remote username (ie: user)');
			$remote_path = getInput("Remote home folder (default: /home/{$remote_user}/)", "/home/{$remote_user}/");
			$remote_doc_root = getInput('Remote document root relative to home folder (default: public_html/)', 'public_html/');
			output('SSH Connection Info');
			$remote_ssh_pub_key = getInput('SSH Public Key File (defaults to ~/.ssh/id_rsa.pub)', '~/.ssh/id_rsa.pub');
			$remote_ssh_priv_key = getInput('SSH Private Key File (defaults to ~/.ssh/id_rsa)', '~/.ssh/id_rsa');
			$remote_ssh_port = getInput("SSH Port (default: 22)", 22);
			output('Remote Mysql Connection Info');
			$remote_mysql_host = getInput('Remote mysql host (usually localhost)', 'localhost' );
			$remote_mysql_user = getInput("Remote mysql user (default: $remote_user}", $remote_user);
			$remote_mysql_pass = getInput('Remote mysql password');
			$remote_mysql_name = getInput("Remote mysql database name (default: $remote_user)", $remote_user);

			if(!is_dir(C5_DIR."/consh")) {
				mkdir(C5_DIR."/consh");
			}

			$home = getenv("HOME");
			$remote_ssh_pub_key = str_replace("~", $home, $remote_ssh_pub_key);
			$remote_ssh_priv_key = str_replace("~", $home, $remote_ssh_priv_key);

			$data = <<<EOF
<?php
//SSH settings
define('REMOTE_HOST', '$remote_host');
define('REMOTE_USER', '$remote_user');
define('REMOTE_HOME_PATH', '$remote_path');
define('REMOTE_DOC_ROOT', REMOTE_HOME_PATH . '$remote_doc_root');
define('REMOTE_PASS', '');																				//at the moment only key based authentication works
define('REMOTE_USE_KEY', true);
define('REMOTE_PUB_KEY_PATH', '$remote_ssh_pub_key');
define('REMOTE_PRIV_KEY_PATH', '$remote_ssh_priv_key');
define('REMOTE_PORT', $remote_ssh_port);

//Remote database settings
define('REMOTE_DB_HOST', '$remote_mysql_host');				//usually localhost... remember this is being executed on the remote server
define('REMOTE_DB_USER', '$remote_mysql_user');
define('REMOTE_DB_PASS', '$remote_mysql_pass');
define('REMOTE_DB_NAME', '$remote_mysql_name');
EOF;
			file_put_contents(CONSH_CONFIG, $data);

			output(CONSH_CONFIG. " has been saved with your settings");
		}
	}
}