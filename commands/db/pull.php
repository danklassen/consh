<?php

class DbPull extends Command {

  public function __construct() {
    $this->name = "DB:Pull";
    $this->description = "Pull remote database";
    $this->help = "Pull and import the remote database to the local setup";
  }

  public function run($options) {
    $ssh = new SSH();
    debug("Pulling remote database");
    $file_name = 'db_' . time() . '.sql';
    $remote_file = REMOTE_HOME_PATH.$file_name;
    debug("remote file: {$remote_file}");
    $local_file = C5_DIR."{$file_name}";
    $ssh->runCommand('mysqldump -h ' . REMOTE_DB_HOST . ' -u ' . REMOTE_DB_USER . ' -p'.REMOTE_DB_PASS . ' ' . REMOTE_DB_NAME. " > " . $remote_file);
    debug('Pulling file locally');
    $ssh->scp($remote_file, $local_file);
    $ssh->rmRemoteFile($remote_file);
    debug('Done');
    $sql = file($local_file);
    $db = new LocalDB();
    debug("Importing to local database");
    $templine = '';

    $size = count($sql);
    $current = 0;
    foreach ($sql as $line) {
      $current++;
      // Skip it if it's a comment
      if (substr($line, 0, 2) == '--' || $line == '') {
        continue;
      }
      $templine .= $line;
      if (substr(trim($line), -1, 1) == ';') {
        $db->execute($templine);
        showStatus($current, $size);
        $templine = '';
      }
    }
    print "\n";
    debug("Done");
    return true;
  }
}