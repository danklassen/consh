<?php
/**
 * Pulls the remote database and imports it locally
 *
 * This will overwrite all local data with that from the remote database!
 *
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1
 */
class DbPull extends Command {

  /**
   * sets the name, description, and help
   */
  public function __construct() {
    $this->name = "DB:Pull";
    $this->description = "Pull remote database";
    $this->help = "Pull and import the remote database to the local setup";
  }

  /**
   * does the magic
   * @param  array $options not used at all
   * @return boolean
   */
  public function run($options) {
    $ssh = new SSH();
    output("Pulling remote database");
    $file_name = 'db_' . time() . '.sql';
    $remote_file = REMOTE_HOME_PATH.$file_name;
    debug("remote file: {$remote_file}");
    $local_file = C5_DIR."{$file_name}";
    $ssh->runCommand('mysqldump -h ' . REMOTE_DB_HOST . ' -u ' . REMOTE_DB_USER . ' -p'.REMOTE_DB_PASS . ' ' . REMOTE_DB_NAME. " > " . $remote_file);
    output('Pulling file locally');
    $ssh->scp($remote_file, $local_file);
    $ssh->rmRemoteFile($remote_file);
    output('Done', 'success');
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
    unlink($local_file);
    output("Done", 'success');
    return true;
  }
}