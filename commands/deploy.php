<?php
/**
 * push the current code from master to the remote server
 *
 * @package Commands
 * @author  Dan Klassen <dan@triplei.ca>
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
        if (!defined('DEPLOY_STRATEGY')) {
            define('DEPLOY_STRATEGY', 'git_pull');
        }

        require CONSH_DIR . '/helpers/deploy_strategies/' . DEPLOY_STRATEGY . '.php';
        $deploy_class = camelize(DEPLOY_STRATEGY. '_deploy_strategy');
        $deploy = new $deploy_class();
        Hook::fire('before_deploy');
        Hook::fire('before_deploy_'.$deploy_class);
        $deploy->deploy();
        Hook::fire('before_deploy_'.$deploy_class);
        Hook::fire('after_deploy');
    }
}