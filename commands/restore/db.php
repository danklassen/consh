<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class RestoreDb extends Command
{

    public function __construct()
    {
        $this->name = "Restore:DB";
        $this->description = "Restore a db backup to the local server";
        $this->help = "Restore a previous backup to the local installation";
        $this->parameters = array('name' => 'The name to identify the database backup');
    }

    public function run($options = array())
    {
        if (count($options) != 1) {
            $this->showRestoreOptions();
            return false;
        }

        $dir = getLocalDBBackupDir();
        $version = array_shift($options);
        $file_name = $dir . '/' . $version;

        if (!file_exists($file_name)) {
            output("File: {$version} could not be found", 'error');
            return false;
        }

        output('WARNING: this will wipe out your local database. Make sure you have any necessary backups', 'warning');
        $confirm = getInput("Are you sure you want to restore your local database to the backup labelled {$version}? (y/n)");
        if ($confirm !='y') {
            output("User Canceled");
            return false;
        }

        output("Importing {$version}");
        $sql = file($file_name);
        $db = new LocalDB();
        $templine = '';

        $size = count($sql);
        $current = 0;
        foreach ($sql as $line) {
            $current++;
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }
            $templine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                $db->execute($templine);
                showStatus($current, $size);
                $templine = '';
            }
        }
        output('done', 'success');
        return true;
    }

    protected function showRestoreOptions()
    {
        $dir = getLocalDBBackupDir();
        if ($handle = opendir($dir)) {
            output("backup files:");
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    output($entry);
                }
            }
            closedir($handle);
        } else {
            output("Could not open {$dir} for processing", 'error');
        }
    }

}