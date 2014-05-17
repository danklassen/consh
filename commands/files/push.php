<?php
/**
 * Rsync's the remote files/ directory to the local one
 *
 * This will delete local files which do not exist on the remote host
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.2.1
 */
class FilesPush extends Command
{

    /**
     * sets the name, description, and help
     *
     * @return null
     */
    public function __construct()
    {
        $this->name = "Files:Push";
        $this->description = "Push local files to the remote server";
        $this->help = "Push your local /files directory to the remote site";
    }

    /**
     * does the magic
     *
     * if the constant FILES_PUSH_RSYNC_COMMAND is defined, then that command will be run instead of the default
     * be default the command is rysnc -az --delete local_instal/files/ user@host:doc_root/files
     *
     * hooks from this method are:
     * before_files_push
     * -- local files are pushed to remote server --
     * after_files_push
     *
     * @param array $options not used
     *
     * @return boolean
     */
    public function run($options = array())
    {
        output('WARNING: this will overwrite the remote site\'s files with your local ones. Make sure you have any necessary backups', 'warning');
        $confirm = getInput("Are you sure you want to push your local files to the remote server? (y/n)");
        if ($confirm !='y') {
            output("User Canceled");
            return false;
        }
        output("Pushing remote files");
        $local_dir = C5_DIR . "/" . "files/";
        Hook::fire('before_files_push');

        $rsync_options = "";

        // Look for an rsync exclude file
        // See here for recommended excludes: http://www.concrete5.org/documentation/installation/moving_a_site/
        if (! file_exists(CONSH_RSYNC_EXCLUDE_FILE)) {
            output("Rsync exclude file not found at ".CONSH_RSYNC_EXCLUDE_FILE."\n","error");
        } else {
            $rsync_options.= " --exclude-from '".CONSH_RSYNC_EXCLUDE_FILE."' ";
            output("These files will be ignored:\n".file_get_contents(CONSH_RSYNC_EXCLUDE_FILE));
        }

        if (!defined('FILES_PUSH_RSYNC_COMMAND')) {
            $command = 'rsync -avz '.$rsync_options.' --delete ' . $local_dir . " " . REMOTE_USER . '@' . REMOTE_HOST . ':' . REMOTE_DOC_ROOT . "files/";
        } else {
            $command = FILES_PUSH_RSYNC_COMMAND;
        }
        $output = shell_exec($command);
        Hook::fire('after_files_push');
        output("Done", 'success');
        return true;
    }
}