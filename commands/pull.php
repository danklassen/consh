<?php
/**
 * runs both the db:pull and files:pull commands
 *
 * @author    Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.2
 */
class Pull extends Command
{

    public function __construct()
    {
        $this->name = "Pull";
        $this->description = "Pull a remote database and files";
        $this->help = "Pulls the remote database and files localy. This is a destructive operation.";
    }

    public function run($options = array())
    {
        $db = new DbPull();
        $db->run($options);
        $files = new FilesPull();
        $files->run($options);
    }
}