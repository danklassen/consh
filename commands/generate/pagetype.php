<?php


class GeneratePagetype extends Command
{
    /**
     * sets the name, description, and help
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = "Generate:Pagetype";
        $this->description = "Generates a pagetype controller";
        $this->help = "Generate an empty pagetype controller";
        $this->params = array('name' => 'The name of the page type to create');
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
        if (count($options) != 1) {
            output("this command expects exactly 1 parameter: the name of the page type", 'error');
            return false;
        }
        $name = array_shift($options);
        $controller_name = camelize($name);

        $str = <<<EOS
<?php
/**
 * custom page type controller
 */
class {{PageTypeName}}PageTypeController extends Controller
{

}
EOS;
        $str = str_replace("{{PageTypeName}}", $controller_name, $str);

        if (!is_dir(C5_DIR . '/controllers/page_types/')) {
            mkdir(C5_DIR . '/controllers/page_types/');
        }
        file_put_contents(C5_DIR . "/controllers/page_types/" . $name . ".php", $str);

        output("wrote file: " . C5_DIR . "/controllers/page_types/" . $name . ".php", 'success');
        return true;
    }
}