<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class GitPush extends Command {

	public function __construct() {
		$this->name = "Git:Push";
		$this->description = "Push local git repo to origin master and pull remote from origin master";
		$this->help = "Push and pull code";
	}

	public function run($options = array()) {
		output('pushing to origin master from local');
		$output = shell_exec('git push origin master');
		debug($output);
		output('pulling from origin master from remote');
		$ssh = new SSH();
		$output = $ssh->runCommand("cd " . REMOTE_DOC_ROOT . " && git pull origin master");
		output("Done", 'success');
	}
}