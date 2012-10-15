<?php
/**
 * Rsync's the remote files/ directory to the local one
 *
 * This will delete local files which do not exist on the remote host
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1
 */
class FilesPull extends Command {

	/**
	 * sets the name, description, and help
	 */
	public function __construct() {
		$this->name = "Files:Pull";
		$this->description = "Pull remote files locally";
		$this->help = "Pull remote site's file locally to your /files dir";
	}

	/**
	 * does the magic
	 * @param  array $options not used
	 * @return boolean
	 */
	public function run($options) {
		debug("Pulling remote files");
		$to_dir = C5_DIR . "/" . "files/";
		$output = shell_exec('rsync -az --delete '.REMOTE_USER.'@'.REMOTE_HOST.':~/public_html/files/ '. $to_dir);
		shell_exec('chmod 777 files/ files/cache');
		debug($output);
		debug("Done");
		return true;
	}
}