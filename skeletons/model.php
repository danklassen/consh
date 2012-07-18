<?php defined('C5_EXECUTE') or die(_("Access Denied."));

class {{ClassName}} extends ADOdb_Active_Record {
  public $_table = '{{tableName}}';

  /**
   * default constructor
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * save the current instance to the database
   */
  public function save() {
    $is_new = false;
    if( !$this->id ) {
      $this->created_at = date("Y-m-d G:i:s");
      $is_new = true;
    }
    $this->updated_at = date("Y-m-d G:i:s");
    $value = parent::save();
    return $value;
  }

  /**
   * set data for the current instance
   * @param array $data an associative array of attr => value to set
   */
  public function setData($data = array()) {
    foreach($data as $key => $value) {
      $this->$key = $value;
    }
  }

  /**
   * load up a {{ClassName}} by it's id
   * @param integer $id id of the {{ClassName}} item to load
   * @return {{ClassName}}
   */
  public static function _find($id) {
    if(!$id || (intval($id) == 0)) {
      return false;
    }
    $item = new {{ClassName}}();
    $item->load('id=?', array($id));
    return $item;
  }

  /**
   * get all the {{ClassName}} {{ClassName}} in the database
   * @return array an array of ClassName items
   */
  public static function getAll() {
    $db = Loader::db();
    $rs = $db->Execute('select id from {{tableName}} order by created_at DESC');
    $items = array();

    foreach ($rs as $row) {
      $items[] = {{ClassName}}::_find($row['id']);
    }
    return $items;
  }

  /**
   * determine if this {{ClassName}} is valid
   * @return boolean
   * @todo implement this if necessary
   */
  public function isValid() {
    return true;
  }

  /**
   * add an error to the stack
   * @param string $message message to display
   * @param string $column the attribute this error is in reference to
   */
  public function addError($message, $column = null) {
    if(!isset($this->_errors) || !is_array($this->_errors)) {
      $this->_errors = array();
    }
    if(!is_null($column)) {
      $this->_errors[$column] = $message;
    } else {
      $this->_errors[] = $message;
    }
  }

}