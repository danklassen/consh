<?php
/**
 * helper class to take a set of options and turn it into a column string
 *
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Helpers
 * @since 0.1.1
 */
class columnInfo {

  public $name;
  public $type;
  public $extra;
  public $size;

  public function __construct($info) {
    $this->extra = array();
    $data = explode(":", $info);
    $this->name = $data[0];
    $this->setType($data[1]);

  }

  public function getXMLObject() {
    $field = new SimpleXMLElement("<field />");
    $field->addAttribute('name', $this->name);
    $field->addAttribute('type', $this->type);
    if ($this->size) {
      $field->addAttribute('size', $this->size);
    }
    $field = $this->addExtras($field);
    return $field;
  }

  public function __toString() {
    $xml = $this->getXMLObject();
    return str_replace("<?xml version=\"1.0\"?>\n", "", $xml->asXML());
  }

  protected function addExtras($field) {
    foreach($this->extra as $key => $value) {
      $field->addChild($key, $value);
    }
    return $field;
  }

  public function setType($type) {
    $type = strtolower($type);

    switch ($type) {
      case 'id':
        $this->type = 'I';
        $this->addExtra('key');
        $this->addExtra('unsigned');
        $this->addExtra('autoincrement');
        break;

      case 'boolean':
      case 'i2':
        $this->type = 'I2';
        $this->addExtra('unsigned');
        break;

      case 'int':
      case 'i':
        $this->type = "I";
        break;

      case 'created':
        $this->addExtra('deftimestamp');
      case 't':
      case 'timestamp':
        $this->type = 'T';
        break;

      case 'd':
      case 'date':
        $this->type = 'D';
        break;

      case 'text':
      case 'x2':
        $this->type = 'X2';
        break;

      //strings (also default)
      case 'string':
      case 'c':
      default:
        $this->type = 'C';
        $this->size = 255;
        break;
    }
  }

  public function addExtra($key, $value = null) {
    $this->extra[$key] = $value;
  }
}

class tableInfo {
  public $columns;
  public $name;

  public function __construct($name = '') {
    $this->columns = array();
    $this->name = $name;
  }

  public function addColumn($column) {
    $this->columns[] = $column;
  }

  public function __toString() {
    $schema = new SimpleXMLElement("<schema />");
    $schema->addAttribute('version', "0.3");
    $table = $schema->addChild('table');
    $table->addAttribute('name', $this->name);
    foreach($this->columns as $column) {
      $columnXML = $table->addChild('field');
      $columnXML->addAttribute('name', $column->name);
      $columnXML->addAttribute('type', $column->type);
      if($column->size) {
        $columnXML->addAttribute('size', $column->size);
      }
      foreach($column->extra as $key => $value) {
        $columnXML->addChild($key, $value);
      }
    }
    $dom = dom_import_simplexml($schema)->ownerDocument;
    $dom->formatOutput = true;
    $xml = $dom->saveXML();
    return str_replace("<?xml version=\"1.0\"?>\n", "", $xml);
  }
}