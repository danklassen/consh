<?php

define('C5_ENVIRONMENT_ONLY', true);
define('C5_DIR', __DIR__ . '/../../');

require('functions.php');
require('command.php');
require('remote_settings.php');
require(__DIR__.'/../../config/site.php');
require('local_db.php');
require('ssh.php');