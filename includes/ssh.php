<?php
/**
 * provides an interface to execute various operations on the remote server
 * @package  Base
 * @author  Dan Klassen <dan@triplei.ca>
 */
class SSH {
	private $connection;
	private $connected;

	public function __construct() {
		$this->connected = false;
	}

	public function connect() {
		if (!$this->connected) {
			debug('connecting');
			$this->connection = ssh2_connect(REMOTE_HOST, REMOTE_PORT);
			if (REMOTE_USE_KEY) {
				ssh2_auth_pubkey_file($this->connection, REMOTE_USER, REMOTE_PUB_KEY_PATH, REMOTE_PRIV_KEY_PATH);
			}
			$this->connected = true;
		}
	}

	public function getConnection() {
		if (!$this->connected) {
			$this->connect();
		}
		return $this->connection;
	}

	public function close() {
		debug('closing');
		$this->getConnection();
		$this->runCommand('exit');
		$this->connection = null;
		$this->connected = false;
	}

	public function runCommand($cmd) {
	  if (!($stream = ssh2_exec($this->getConnection(), $cmd))) {
	    throw new Exception('SSH command failed');
	  }
	  $err_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
	  stream_set_blocking($err_stream, true);
	  $error = stream_get_contents($err_stream);
	  if(!empty($error)) {
	  	output($error, 'Error');
	  	return false;
	  }

	  stream_set_blocking($stream, true);
	  $data = "";
	  while ($buf = fread($stream, 4096)) {
	  	$data .= $buf;
	  }
	  fclose($stream);
	  return $data;
  }

	public function scp($remote_path, $local_path) {
		$con = $this->getConnection();
		if (ssh2_scp_recv($con, $remote_path, $local_path)) {
			return true;
		} else {
			die('could not copy file from remote server');
		}
	}

	public function rmRemoteFile($remote_file) {
		$con = $this->getConnection();
		$this->runCommand('rm '.$remote_file);
	}
}