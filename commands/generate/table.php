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
   * name of the table
   * @var string
   */
  protected $table_name;

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
    $this->table_name = $name;
    require_once(CONSH_DIR . '/helpers/dbXml.php');
    $table = new tableInfo($name);
    foreach($options as $column) {
      $columnXML = new columnInfo($column);
      $table->addColumn($columnXML);
    }
    return $table;
  }

  /**
   * get the name of the table for this instance
   * @return string
   */
  public function getTableName() {
    return $this->table_name;
  }
}
