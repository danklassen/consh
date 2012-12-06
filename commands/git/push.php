<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class GitPush extends Command
{

    public function __construct()
    {
        $this->name = "Git:Push";
        $this->description = "Push local git repo to remote";
        $this->help = "Push code";
    }

    public function run($options = array())
    {
        output('pushing to ' .DEPLOY_REMOTE . ' ' . DEPLOY_BRANCH . ' from local');
        Hook::fire('before_git_push');
        $output = shell_exec('git push ' . DEPLOY_REMOTE . ' ' . DEPLOY_BRANCH);
        Hook::fire('after_git_push');
        output($output);
    }
}
