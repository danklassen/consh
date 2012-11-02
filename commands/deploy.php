<?php
/**
 * push the current code from master to the remote server
 *
 * @author    Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class Deploy extends Command
{

    public function __construct()
    {
        $this->name = "Deploy";
        $this->description = "Deploy the code from origin master to the remote server";
        $this->help = "Deploy the code from origin master to the remote server";
    }

    public function run($options = array())
    {
        $ssh = new SSH();
        output("pulling origin/master on remote server");
        $ssh->runCommand('cd '.REMOTE_DOC_ROOT);
        $console_output = $ssh->runCommand('cd '.REMOTE_DOC_ROOT.' && git pull '.DEPLOY_REMOTE.' '.DEPLOY_BRANCH);
        output($console_output);
        output("Done");
        return true;
    }
}