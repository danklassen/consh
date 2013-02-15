<?php
/**
 * provides an interface to execute various operations on the remote server
 * @package  Base
 * @author  Dan Klassen <dan@triplei.ca>
 */
class SSH {
    /**
     * the ssh2 connection
     * @var resource
     */
    private $connection;
    /**
     * whether or not this instance is currently connected
     * @var boolean
     */
    private $connected;

    /**
     * the constructor
     *
     * at the moment defaults to not being connected
     */
    public function __construct() {
        $this->connected = false;
    }

    /**
     * connect to the remote server
     */
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

    /**
     * get a connection to the remote server
     *
     * if we are already connect re-use the connection, otherwise one is established
     * @return mixed the connection to the remote server
     */
    public function getConnection() {
        if (!$this->connected) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * close the connection
     */
    public function closeConnection() {
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
      if (!empty($error)) {
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

    public function sendFile($local_path, $remote_path)
    {
        $con = $this->getConnection();
        if (ssh2_scp_send($con, $local_path, $remote_path)) {
            return true;
        } else {
            die('could not send file to remote server');
        }
    }
}