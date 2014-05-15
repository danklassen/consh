<?php
/**
 * Backs up the remote database and saves it locally
 *
 * This will overwrite all local data with that from the remote database!
 *
 * @package Commands
 * @author  Dan Klassen <dan@triplei.ca>
 * @since   0.2
 */
class BackupDb extends Command
{

    /**
     * sets the name, description, and help
     *
     * @return null
     */
    public function __construct()
    {
        $this->name = "Backup:DB";
        $this->description = "Backup the remote database locally";
        $this->help = "Pull down a timestamped copy of the remote database. The file will be saved in your LOCAL_BACKUP_DIR (" . C5_DIR . "/remote_backups/ by default)";
        $this->parameters = array('name' => 'Name of the backup. This is appended to the filename');
    }

    /**
     * does the magic
     *
     * order of operations:
     * * create an SSH connection to the remote host
     * * run mysql dump on remote host
     * * scp the file locally
     * * remove the remote file
     *
     * @param array $options not used at all
     *
     * @return boolean
     */
    public function run($options = array())
    {
        Hook::fire('before_db_backup');
        $ssh = new SSH();
        $file_name = time() . '.sql';
        if (count($options)) {
            $file_name = time() . '-' . camelize($options[0]) . '.sql';
        }
        $remote_file = REMOTE_HOME_PATH . $file_name;
        $mysqldump_command = 'mysqldump -h ' . REMOTE_DB_HOST . ' -u ' . REMOTE_DB_USER . ' -p'.addslashes(REMOTE_DB_PASS) . ' ' . REMOTE_DB_NAME. " > " . $remote_file;
        if (defined('USE_GZIP_COMPRESSION') && USE_GZIP_COMPRESSION) {
            $file_name.=".gz";
            $remote_file.=".gz";
            $mysqldump_command = 'mysqldump -h ' . REMOTE_DB_HOST . ' -u ' . REMOTE_DB_USER . ' -p'.addslashes(REMOTE_DB_PASS) . ' ' . REMOTE_DB_NAME. " | gzip > " . $remote_file;
        }
        output("Backing up the remote database");
        if (!defined('LOCAL_BACKUP_DIR')) {
            define('LOCAL_BACKUP_DIR', C5_DIR . "/remote_backups/");
        }
        if (!is_dir(LOCAL_BACKUP_DIR)) {
            mkdir(LOCAL_BACKUP_DIR);
        }
        $local_file = LOCAL_BACKUP_DIR."{$file_name}";
        $ssh->runCommand($mysqldump_command);
        $ssh->scp($remote_file, $local_file);
        $ssh->rmRemoteFile($remote_file);
        output(sprintf('remote database was backed up to %s', $file_name));
        output('Done', 'success');
        Hook::fire('after_db_backup');
        return true;
    }
}