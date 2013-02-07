<?php
/**
 * Rsync's the remote files/ directory to the local one
 *
 * This will delete local files which do not exist on the remote host
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1
 */
class FilesPull extends Command
{
	public $exclude_file = "";
	
    /**
     * sets the name, description, and help
     *
     * @return null
     */
    public function __construct()
    {
        $this->name = "Files:Pull";
        $this->description = "Pull remote files locally";
        $this->help = "Pull remote site's file locally to your /files dir";
        
        // Look for an exclude files.
        // See here for recommended excludes: http://www.concrete5.org/documentation/installation/moving_a_site/
        $exclude_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'rsync_exclude.txt';
        if (file_exists($exclude_file)) {
	   		$this->exclude_file = $exclude_file;
	   	}
    }

    /**
     * does the magic
     *
     * if the constant FILES_PULL_RSYNC_COMMAND is defined, then that command will be run instead of the default
     * be default the command is rysnc -avz --delete user@host:doc_root/files local_instal/files/
     *
     * hooks from this method are:
     * before_files_pull
     * -- remote files are pulled down --
     * after_files_pull
     *
     * @param array $options not used
     *
     * @return boolean
     */
    public function run($options = array())
    {
        output("Pulling remote files");
        $to_dir = C5_DIR . "/" . "files/";
        Hook::fire('before_files_pull');
        $rsync_options = "";
        if ($this->exclude_file != "") {
        	$rsync_options.= " --exclude-from '".$this->exclude_file."' ";
        	output("These Files will be ignored:\n".file_get_contents($this->exclude_file));
        }
        if (!defined('FILES_PULL_RSYNC_COMMAND')) {
            $command = 'rsync -az '.$rsync_options.' --delete '. REMOTE_USER . '@' . REMOTE_HOST . ':' . REMOTE_DOC_ROOT . "files/ " . $to_dir;
        } else {
            $command = FILES_PULL_RSYNC_COMMAND;
        }
        $output = shell_exec($command);
        shell_exec('chmod 777 files/ files/cache');
        Hook::fire('after_files_pull');
        output("Done", 'success');
        return true;
    }
}