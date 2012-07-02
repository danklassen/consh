<?php
/**
 * Generates a model file
 *
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1.1
 */
class GenerateModel extends Command {

  /**
   * sets the name, description, and help
   */
  public function __construct() {
    $this->name = "Generate:Model";
    $this->description = "Generates a skeleton model";
    $this->help = "Generate an empty model";
  }

  /**
   * does the magic
   *
   * @param  array $options not used at all
   * @return boolean
   * @todo  code this
   */
  public function run($options) {
    output("Do stuff here", 'error');
    return true;
  }
}