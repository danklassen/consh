<?php
/**
 * Generates a block
 *
 * @author    Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1.1
 */
class GenerateBlock extends Command
{

    /**
     * sets the name, description, and help
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = "Generate:Block";
        $this->description = "Generates a skeleton block";
        $this->help = "Generate a simple block";
    }

    /**
     * does the magic
     *
     * @param array $options not used at all
     *
     * @return boolean
     */
    public function run($options = array())
    {
        $name = $options[0];
        $new_block_path = $this->copyScaffold($name);
        $this->parseScaffold($new_block_path, $name);
        return true;
    }

    protected function copyScaffold($block_name)
    {
        $target_folder = C5_DIR . "/blocks/" . $block_name;
        $skeleton_folder = CONSH_DIR . "/skeletons/block";
        shell_exec("cp -dpr {$skeleton_folder} $target_folder");
        return $target_folder;
    }

    protected function parseScaffold($path, $name)
    {
        $table_name = "bt".camelize($name);
        $controller_name = camelize($name) . "BlockController";

        $controller_path = $path . "/controller.php";
        $db_xml_path = $path . "/db.xml";

        $controller_str = file_get_contents($controller_path);
        $controller_str = str_replace(array("{{ControllerName}}", "{{TableName}}"), array($controller_name, $table_name), $controller_str);
        $controller_handle = fopen($controller_path, "w+");
        fwrite($controller_handle, $controller_str);

        $db_xml_str = file_get_contents($db_xml_path);
        $db_xml_str = str_replace("{{TableName}}", $table_name, $db_xml_str);
        $db_xml_handle = fopen($db_xml_path, 'w+');
        fwrite($db_xml_handle, $db_xml_str);
    }
}