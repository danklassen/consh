<?php
require_once 'deploy_strategy.php';

class GitPullDeployStrategy implements DeployStrategy
{
    public function prepare()
    {
        return true;
    }

    public function deploy()
    {
        $ssh = new SSH();
        output("pulling " . DEPLOY_REMOTE . "/" . DEPLOY_BRANCH . " on remote server");
        $ssh->runCommand('cd ' . REMOTE_DOC_ROOT);
        $console_output = $ssh->runCommand('cd '.REMOTE_DOC_ROOT . ' && git pull ' . DEPLOY_REMOTE . ' ' . DEPLOY_BRANCH);
        output($console_output);
        output("Done");
        return true;
    }

    public function cleanup()
    {
        return true;
    }
}