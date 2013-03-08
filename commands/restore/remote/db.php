<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class RestoreRemoteDb extends Command
{

    public function __construct()
    {
        $this->name = "Restore:Remote:DB";
        $this->description = "Restore a db backup to the remote server";
        $this->help = "Restore a db backup to the remote server. This is a potentially destructive command";
        $this->parameters = array('name' => 'The name which identifies the database backup to restore from');
    }

    public function run($options = array())
    {
        if (count($options) != 1) {
            $this->showRestoreOptions();
            return false;
        }

        $dir = getLocalDBBackupDir();
        $version = array_shift($options);
        $file_name = $dir . '/' . $version;

        if (!file_exists($file_name)) {
            output("File: {$version} could not be found", 'error');
            return false;
        }
        output('WARNING: this will wipe out your remote database. Make sure you have any necessary backups', 'warning');
        $confirm = getInput("Are you sure you want to push {$version} to the remote server? (y/n)");
        if ($confirm !='y') {
            output("User Canceled");
            return false;
        }
        $ssh = new SSH();
        debug('copying file to remote server');
        if (!$ssh->sendFile($file_name, REMOTE_HOME_PATH . $version)) {
            output("file could not be sent. stopping import", 'error');
            return false;
        }
        debug('sent file to ' . REMOTE_HOME_PATH . $version);
        $command = sprintf("mysql -h %s -u %s -p%s %s < %s", REMOTE_DB_HOST, REMOTE_DB_USER, REMOTE_DB_PASS, REMOTE_DB_NAME, REMOTE_HOME_PATH . $version);
        debug("importing to remote database");
        $ssh->runCommand($command);
        $ssh->rmRemoteFile(REMOTE_HOME_PATH . $version);
        output('done', 'success');
        return true;
    }

    protected function showRestoreOptions()
    {
        $dir = getLocalDBBackupDir();
        if ($handle = opendir($dir)) {
            output("backup files:");
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    output($entry);
                }
            }
            closedir($handle);
        } else {
            output("Could not open {$dir} for processing", 'error');
        }
    }

}