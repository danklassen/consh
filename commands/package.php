<?php
/**
 * runs both the db:pull and files:pull commands
 *
 * @author    Dan Klassen <dan@triplei.ca>
 * @package Commands
 * @since 0.2
 */
class Package extends Command
{

    public function __construct()
    {
        $this->name = "Package";
        $this->description = "Package up a site";
        $this->help = "Pulls from live and packages up a site and assets";
        $this->parameters = array(
            'name' => 'Name of the file to create. IE: my_size.zip',
        );
    }

    public function run($options = array())
    {
        Hook::fire('before_package');

        $zip_name = array_shift($options);
        $pull = new Pull();
        $pull->run($options);

        $db_file_name = 'db_'.time().'.sql';
        $local_db = new LocalDB();
        $local_db->exportDB($db_file_name);

        $command = "zip -r -dd -q {$zip_name} {$db_file_name} " . PACKAGE_BASE_FILES . " " .PACKAGE_EXTRA_FILES;
        $output = shell_exec($command);
        output($output);
        shell_exec("rm {$db_file_name}");
        output("{$zip_name} saved", 'success');
        Hook::fire('after_package');
    }
}