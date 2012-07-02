<?php
/**
 * Generates a table's db.xml file
 *
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1.1
 */
class GenerateTable extends Command {

  /**
   * sets the name, description, and help
   */
  public function __construct() {
    $this->name = "Generate:Table";
    $this->description = "Generates a db.xml file for the passed in attributes";
    $this->help = "Generate a db.xml file";
  }

  /**
   * does the magic
   *
   * @param  array $options not used at all
   * @return string
   */
  public function run($options) {
    $name = array_shift($options);
    require_once(CONSH_DIR . '/helpers/dbXml.php');
    $table = new tableInfo($name);
    foreach($options as $column) {
      $columnXML = new columnInfo($column);
      $table->addColumn($columnXML);
    }
    print $table;
    return true;
  }
}
