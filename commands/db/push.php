<?php
/**
 * @author  Dan Klassen <dan@triplei.ca>
 * @package Commands
 */
class DbPush extends Command {

	public function __construct() {
		$this->name = "DB:Push";
		$this->description = "Push local database";
		$this->help = "Push and import the local database to the remote server";
	}

	public function run($options) {
		$confirm = getInput("Are you sure you want to push your local database to the remote server? (y/n)");
		if($confirm !='y') {
			output("Bailing out");
			return false;
		}
		die("not yet implemented");
		$ssh = new SSH();
		debug("Pulling remote database");
		$file_name = 'db_' . time() . '.sql';
		$remote_file = REMOTE_HOME_PATH.$file_name;
		debug("remote file: {$remote_file}");
		$local_file = C5_DIR."{$file_name}";
		$ssh->runCommand('mysqldump -h ' . REMOTE_DB_HOST . ' -u ' . REMOTE_DB_USER . ' -p'.REMOTE_DB_PASS . ' ' . REMOTE_DB_NAME. " > " . $remote_file);
		debug('Pulling file locally');
		$ssh->scp($remote_file, $local_file);
		$ssh->rmRemoteFile($remote_file);
		debug('Done');
		$sql = file_get_contents($local_file);
		$db = new LocalDB();
		debug("Importing to local database");
		$res = $db->execute($sql);
		debug("Done");
		return true;
	}

}