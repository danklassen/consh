<?php
/**
 * displays the current consh version
 *
 * @author Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class Version extends Command {

	public function __construct() {
		$this->name = "Version";
		$this->description = "Displays the consh version";
		$this->help = "";
	}

	public function run() {
		output(CONSH_VERSION);
	}
}