<?php
/**
 * Generates a model file
 *
 * @author    Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1.1
 */
class GenerateModel extends Command
{

    /**
     * sets the name, description, and help
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = "Generate:Model";
        $this->description = "Generates a skeleton model";
        $this->help = "Generate an empty model";
    }

    /**
     * does the magic
     *
     * @param    array $options not used at all
     * @return boolean
     * @todo    code this
     */
    public function run($options = array())
    {
        $name = $options[0];
        require_once(CONSH_DIR . '/helpers/dbXml.php');
        $table = new GenerateTable();
        $table->setPackage($this->getPackage());
        $table_data = $table->run($options);
        $table_name = $table->getTableName();
        $contents = $this->getScaffold($name, $table_name);
        if (file_put_contents($this->getOutputFileName($name), $contents)) {
            output($this->getOutputFileName($name). " was written", 'success');
            $pkg = $this->getPackage();
            output("Now add the following to your db.xml file");
            output($table_data);
        } else {
            output($this->getOutputFileName($name). " could not be written", 'error');
        }
        return true;
    }

    protected function getScaffold($model_name, $table_name)
    {
        $model_name = camelize($model_name);
        $model_str = file_get_contents(CONSH_DIR . "/skeletons/model.php");
        return str_replace(array("{{ClassName}}", "{{tableName}}"), array($model_name, $table_name), $model_str);
    }

    protected function getOutputFileName($model_name)
    {
        $dir = '';
        if ($this->getPackage()) {
            $dir = C5_DIR . "/packages/".$this->getPackage() . "/models/";
        } else {
            $dir = C5_DIR . "/models/";
        }
        return $dir . $model_name . ".php";
    }

    protected function updatePackageDBXML($content)
    {
        $file = C5_DIR . "/packages/".$this->getPackage() . "/db.xml";
        if (file_exists($file) && file_get_contents($file)) {
            $table_xml = new SimpleXMLElement($content);
            $file_xml = new SimpleXMLElement(file_get_contents($file));
            $children = $table_xml->children();
            debug($children->asXML());
            $file_xml->addChild($table_xml->children());
            $file_xml->asXML($file);
        } else {
            file_put_contents($file, $content);
        }
    }
}