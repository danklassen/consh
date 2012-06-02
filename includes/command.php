<?php

class Command {
	private $name;
	private $description;
	private $help;

	public function run($options = array()){
		//over ride this as it will be called
	}

	public function register() {

	}

	public function getName() {
		return $this->name;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getHelp() {
		return $this->help;
	}
}
