<?php
/**
 * Generates a template for an existing block
 *
 * @author    Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class GenerateBlockTemplate extends Command
{

    /**
     * sets the name, description, and help
     */
    public function __construct()
    {
        parent::__construct();
        $this->name = "Generate:Block:Template";
        $this->description = "Generates a template for an existing block";
        $this->help = "Copy the base template for a block into the proper location";
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
        if (count($options) != 2) {
            output("Please call this command with two parameters. 1: block name 2: template name", 'error');
            return false;
        }
        $block_name = $options[0];
        $template_name = $options[1];
        $source_file = '';
        if (file_exists(C5_DIR . "/blocks/" . $block_name . "/view.php")) {
            $source_file = C5_DIR . "/blocks/" . $block_name . "/view.php";
        } else if (file_exists(C5_CONCRETE_DIR . "/blocks/" . $block_name . "/view.php")) {
            $source_file = C5_CONCRETE_DIR . "/blocks/" . $block_name . "/view.php";
        }

        if (empty($source_file)) {
            output("Source file for $block_name was not found", 'error');
            return false;
        }

        if ($template_name == 'view') {
            $destination_folder = C5_DIR . "/blocks/" . $block_name . "/";
        } else {
            $destination_folder = C5_DIR . "/blocks/" . $block_name . "/templates/";
        }
        $destination_file = $destination_folder . $template_name . ".php";
        if (!is_dir($destination_folder)) {
            shell_exec('mkdir -p '. $destination_folder);
        }
        shell_exec("cp $source_file $destination_file");
        output("created file: $destination_file", 'success');
    }

}