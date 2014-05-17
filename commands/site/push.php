<?php
/**
 * Rsync's the remote files/ directory to the local one
 *
 * This will delete local files which do not exist on the remote host
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.2.1
 */
class SitePush extends Command
{

    /**
     * sets the name, description, and help
     *
     * @return null
     */
    public function __construct()
    {
        $this->name = "Site:Push";
        $this->description = "Push local site to the remote server (files and database)";
        $this->help = "Push your local site to the remote site";
    }

    /**
     * does the magic
     *
     * hooks from this method are:
     * before_site_push
     * -- local files are pushed to remote server --
     * after_site_push
     *
     * @param array $options not used
     *
     * @return boolean
     */
    public function run($options = array())
    {
        Hook::fire('before_site_push');

        $rsync_options = "";

        $push = new DbPush();
        $push->run($options);

        $push = new FilesPush();
        $push->run($options);

        Hook::fire('after_site_push');
        return true;
    }
}