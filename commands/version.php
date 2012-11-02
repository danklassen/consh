<?php
/**
 * displays the current consh version
 *
 * @author Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.1
 */
class Version extends Command
{

    /**
     * sets the name, description, and help
     */
    public function __construct()
    {
        $this->name = "Version";
        $this->description = "Displays the consh version";
        $this->help = "";
    }

    public function run($options = array())
    {
        output(CONSH_VERSION);
    }
}