<?php
/**
 * base command class
 *
 * This class should be overridden by any other classes which are commands for the system
 * The main important function is run() which is called and passed any options from the command line
 *
 * @author Dan Klassen <dan@triplei.ca>
 * @package Base
 * @since  0.1
 */
class Command {

  /**
   * name of the Command
   * @var string
   */
  private $name;
  /**
   * description of the command
   * @var string
   */
  private $description;
  /**
   * a help message to be displayed
   *
   * at the moment this is not used at all
   * @var string
   */
  private $help;

  /**
   * name of the package this command is running against
   *
   * this is derived from the command line option --pkg=(*.)
   * @var string
   */
  private $package;

  public function __construct() {
    $this->pkg = null;
  }

  /**
   * the main magic should happen here
   * @param  array  $options options passed in from the command line
   */
  public function run($options = array()){
    //over ride this as it will be called
  }

  /**
   * gets the name of the command
   * @return string the name of the command
   */
  public function getName() {
    return $this->name;
  }

  /**
   * gets the description of the command
   * @return string the description of the command
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * gets the help for the command
   * @return string the help for the command
   */
  public function getHelp() {
    return $this->help;
  }

  /**
   * set the package namespace for this command
   * @param string $pkg
   */
  public function setPackage($pkg=null) {
    $this->pkg = $pkg;
  }

  /**
   * get the package for this intance
   * @return string
   */
  public function getPackage() {
    return $this->pkg;
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
   * populated with the various commands when instantiated
   *
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
