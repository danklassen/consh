<?php

//SSH settings
define('REMOTE_HOST', 'remote-host.com');
define('REMOTE_USER', 'remote-user');
define('REMOTE_HOME_PATH', '/home/remote/path/');
define('REMOTE_DOC_ROOT', REMOTE_HOME_PATH . 'public_html/');
define('REMOTE_PASS', '');																				//at the moment only key based authentication works
define('REMOTE_USE_KEY', true);
define('REMOTE_PUB_KEY_PATH', '/home/username/.ssh/id_rsa.pub');
define('REMOTE_PRIV_KEY_PATH', '/home/username/.ssh/id_rsa');
define('REMOTE_PORT', 22);

//Remote database settings
define('REMOTE_DB_HOST', 'localhost');				//usually localhost... remember this is being executed on the remote server
define('REMOTE_DB_USER', 'remoteMySqlUser');
define('REMOTE_DB_PASS', 'uber-strong-password');
define('REMOTE_DB_NAME', 'databaseName');