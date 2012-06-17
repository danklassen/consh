<?php
/**
 * base command class
 *
 * @author Dan Klassen <dan@triplei.ca>
 * @package Base
 * @since  0.1
 */
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

/**
 * helper class to list commands
 *
 * useful for outputting a list of all commands on the system
 * @since  0.1
 * @package  Base
 */
class CommandList {

  /**
   * populated with the various commands
   * @var array
   */
  private $commands;

  /**
   * loads a list of commands
   *
   * loops through the CONSH_COMMANDS_DIR directory recursively looking for commands to load
   */
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

  /**
   * get an array of all the commands
   * @return array an array of all the commands on the system
   */
  public function getCommands() {
    return $this->commands;
  }

  /**
   * get commands in a directory
   * @param string $dir directory to look in
   */
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

  /**
   * load a command
   * @param  string $path path the command is in
   * @param  string $file name of the file
   * @return null the command is added to the list of commands
   */
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
