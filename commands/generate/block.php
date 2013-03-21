<?php
/**
 * Generates a block
 *
 * run the command consh Generate:Block block_name bID:id title:string body:text page_id:page
 * to generate a custom block.
 *
 * The first parameter is the block's handle. This is used to determine the block's folder, name, and database table.
 * The rest of the parameters are used to create the db.xml and the form for add/edit operations. These are passed in the field_name:field_type format
 * Current valid field_types are:
 *  * id -> auto increment. The field name for this should be bID to keep concrete5 happy
 *  * string -> generic text field
 *  * text -> text area
 *  * page -> int db field and a page selector field
 *  * image -> int db field and image picker field
 *  * file -> ind db field and file picker field
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
        $this->help = "Generate a simple block from the passed in parameters";
        $this->parameters = array(
            'name' => 'The name of the block to create. Example: my_new_block',
            'fields' => 'The rest of the paramaters are the fields in the form of field_name:type. Current field types are id, text, textarea, wysiwyg, page, image, file'
        );
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
        $name = array_shift($options);
        $new_block_path = $this->copyScaffold($name);
        $this->parseScaffold($new_block_path, $name, $options);
        return true;
    }

    protected function copyScaffold($block_name)
    {
        $target_folder = C5_DIR . "/blocks/" . $block_name;
        $skeleton_folder = CONSH_DIR . "/skeletons/block";
        shell_exec("cp -dpr {$skeleton_folder} $target_folder");
        return $target_folder;
    }

    protected function parseScaffold($path, $name, $fields)
    {
        $table_name = "bt".camelize($name);
        $controller_name = camelize($name) . "BlockController";
        $block_name = ucwords(str_replace("_", ' ', $name));

        $controller_path = $path . "/controller.php";
        $wysiwyg_path = $path . "/wysiwyg.php";
        $db_xml_path = $path . "/db.xml";
        $form_path = $path. "/_form.php";

        $controller_str = file_get_contents($controller_path);

        // if we are dealing with a wysiwyg, inject the necessary stuff to the controller to handle saving
        $wysiwyg_field_name = $this->hasWysiwyg($fields);
        $wysiwyg_contents = '';
        if ($wysiwyg_field_name != false) {
            $wysiwyg_contents = file_get_contents($wysiwyg_path);
            $wysiwyg_contents = str_replace("{{wysiwyg_field}}", $wysiwyg_field_name, $wysiwyg_contents);
        }
        $controller_str = str_replace("{{WysiwygContent}}", $wysiwyg_contents, $controller_str);
        unlink($wysiwyg_path);  //cleanup folder

        $controller_str = str_replace("{{ExtraControllerMethods}}", "", $controller_str);

        $controller_str = str_replace(array("{{ControllerName}}", "{{TableName}}", "{{BlockName}}"), array($controller_name, $table_name, $block_name), $controller_str);
        $controller_handle = fopen($controller_path, "w+");
        fwrite($controller_handle, $controller_str);

        $db_xml_str = $this->generateDbXml($table_name, $fields);
        $db_xml_handle = fopen($db_xml_path, 'w+');
        fwrite($db_xml_handle, $db_xml_str);

        $form_str = $this->generateForm($fields);
        $form_handle = fopen($form_path, "w+");
        fwrite($form_handle, $form_str);
    }

    protected function generateDbXml($table_name, $fields)
    {
        include_once CONSH_DIR . '/helpers/dbXml.php';
        $table = new tableInfo($table_name);
        $has_id_column = false;
        foreach ($fields as $column) {
            $columnXML = new columnInfo($column);
            if ($columnXML->name == 'bID') {
                $has_id_column = true;
            }
            $table->addColumn($columnXML);
        }
        if (!$has_id_column) {
            $columnXML = new columnInfo('bID:id');
            $table->addColumn($columnXML);
        }

        return $table->__toString();
    }

    protected function generateForm($fields)
    {
        $form_header = "<?php defined('C5_EXECUTE') or die('Access Denied.');\n\$form = Loader::helper('form');\n";
        $helpers = array();
        $form_str = "";

        $templates_path = CONSH_DIR . '/skeletons/fields/';
        foreach ($fields as $field) {
            $data = explode(":", $field);
            $field_name = $data[0];
            $field_type = $data[1];
            $template = '';
            if ($field_type == 'id') {
                continue;
            }

            switch ($field_type) {
            case 'file':
                $helpers['concrete/asset_library'] = 'file';
                $template = 'file';
                break;
            case 'image':
                $helpers['concrete/asset_library'] = 'file';
                $template = 'image';
                break;
            case 'page':
                $helpers['form/page_selector'] = 'page_selector';
                $template = 'page';
                break;
            case 'textarea':
            case 'text':
                $template = 'textarea';
                break;
            case 'wysiwyg':
            case 'editor':
                $template = 'wysiwyg';
                break;
            case 'string':
            case 'c':
            default:
                $template = 'text';
                break;
            }

            $template = $templates_path . $template . '.php';
            $form_str .= $this->parseFieldTemplate($field_name, $template)."\n";
        }

        foreach ($helpers as $helper => $var_name) {
            $form_header .= "\${$var_name}_form_helper = Loader::helper('$helper');\n";
        }

        $form_header .= "\n?>\n";
        return $form_header . $form_str;
    }

    protected function parseFieldTemplate($field_name, $template_path)
    {
        $field_label = ucwords(str_replace("_", ' ', $field_name));
        $template = file_get_contents($template_path);
        return str_replace(array('{field_name}', '{field_title}'), array($field_name, $field_label), $template);
    }

    protected function hasWysiwyg($fields)
    {
        foreach($fields as $field) {
            $data = explode(":", $field);
            $field_type = $data[1];
            if ($field_type == 'wysiwyg' || $field_type == 'editor') {
                return $data[0];
            }
        }
        return false;
    }
}