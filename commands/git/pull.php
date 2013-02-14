<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class GitPull extends Command
{

    public function __construct()
    {
        $this->name = "Git:Pull";
        $this->description = "Pull remote repo locally";
        $this->help = "Pulls down git changes";
    }

    public function run($options = array())
    {
        output('pulling from ' .DEPLOY_REMOTE . ' ' . DEPLOY_BRANCH . ' to local');
        Hook::fire('before_git_pull');
        $output = shell_exec('git pull ' . DEPLOY_REMOTE . ' ' . DEPLOY_BRANCH);
        Hook::fire('after_git_pull');
        output($output);
    }
}
