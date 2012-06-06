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

class CommandList {

  private $commands;

  public function __construct() {
    $this->commands = array();
    if ($dh = opendir(CONSH_COMMANDS_DIR)) {
      while (($file = readdir($dh)) !== false) {
        if(filetype(CONSH_COMMANDS_DIR . $file) == 'dir' && $file != '.' && $file != '..') {
          $this->getDirCommands(CONSH_COMMANDS_DIR . $file);
        } else {
          $this->loadCommand(CONSH_COMMANDS_DIR, $file);
        }
      }
      closedir($dh);
    }
  }

  public function getCommands() {
    return $this->commands;
  }

  private function getDirCommands($dir) {
    if ($dh = opendir($dir)) {
      while (($file = readdir($dh)) !== false) {
        if(is_dir($dir . $file) && $file != '.' && $file != '..') {
          $this->getDirCommands($dir . $file);
        } else {
          $this->loadCommand($dir, $file);
        }
      }
    }
  }

  private function loadCommand($path, $file) {
    if($file == '.' || $file == "..") {
      return;
    }
    $file = str_replace(".php", '', $file);
    $string = str_replace(CONSH_COMMANDS_DIR, '', $path.'/'.$file);
    $className = str_replace(' ', '', ucwords(str_replace('/', ' ', $string)));
    $object = new $className();
    $this->commands[] = $object;
  }

}
