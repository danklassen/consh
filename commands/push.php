<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class Push extends Command
{

    public function __construct()
    {
        $this->name = "Push";
        $this->description = "Push local git repo to remote and then deploy";
        $this->help = "Push and deploy code";
    }

    public function run($options = array())
    {
        $push = new GitPush();
        $push->run($options);
        $deploy = new Deploy();
        $deploy->run($options);
    }
}
