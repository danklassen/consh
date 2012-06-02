<?php

class FilesPull extends Command {

	public function __construct() {
		$this->name = "Files:Pull";
		$this->description = "Pull remote files locally";
		$this->help = "Pull remote site's file locally to your /files dir";
	}

	public function run($options) {
		debug("Pulling remote files");
		$to_dir = C5_DIR . "files/";
		$output = shell_exec('rsync -avz '.REMOTE_USER.'@'.REMOTE_HOST.':~/public_html/files/ '. $to_dir);
		debug($output);
		debug("Done");
		return true;
	}
}