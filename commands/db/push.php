<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class DbPush extends Command
{

    public function __construct()
    {
        $this->name = "DB:Push";
        $this->description = "Push local database";
        $this->help = "Push and import the local database to the remote server";
    }

    public function run($options = array())
    {
        output('WARNING: this will wipe out your remote database. Make sure you have any necessary backups', 'warning');
        $confirm = getInput("Are you sure you want to push your local database to the remote server? (y/n)");
        if ($confirm !='y') {
            output("User Canceled");
            return false;
        }
        $ssh = new SSH();
        output("Exporting local database");
        $file_name = 'db_'.time().'.sql';
        $file_path = C5_DIR . '/' . $file_name;
        $local_db = new LocalDB();
        $local_db->exportDB($file_name);
        debug('copying file to remote server');
        if (!$ssh->sendFile($file_name, REMOTE_HOME_PATH . $file_name)) {
            output("file could not be sent. stopping import", 'error');
            return false;
        }
        debug('sent file to ' . REMOTE_HOME_PATH . $file_name);
        $command = sprintf("mysql -h %s -u %s -p%s %s < %s", REMOTE_DB_HOST, REMOTE_DB_USER, REMOTE_DB_PASS, REMOTE_DB_NAME, REMOTE_HOME_PATH . $file_name);
        debug("importing to remote database");
        $ssh->runCommand($command);
        $ssh->rmRemoteFile(REMOTE_HOME_PATH . $file_name);
        unlink($file_path);
        output('done', 'success');
        return true;
    }

}