<?php
/**
 * LocalDB Connection
 *
 * establish a connection an execute sql on the local database. The connection
 * is based off of the details in config/site.php
 *
 * @package  Base
 * @author Dan Klassen <dan@triplei.ca>
 */
class LocalDB {
    private $connection;
    private $connected = false;

    /**
     * constructor
     *
     * defaults to not being connected
     */
    public function __construct()
    {
        $this->connected = false;
    }

    /**
     * get a connection to the local database
     *
     * if there is already an active connection, this will re-use it
     *
     * @return resource
     */
    public function getConnection()
    {
        if (!$this->connected) {
            $this->connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
            mysql_select_db(DB_DATABASE, $this->connection);
            $this->connected = true;
        }
        return $this->connection;
    }

    /**
     * executes the passed in sql
     *
     * there is no data sanitization or security checks. Use at your own risk
     *
     * @param string $sql sql to execute
     *
     * @return result the result of the query
     */
    public function execute($sql)
    {
        $con = $this->getConnection();
        return mysql_query($sql, $con);
    }

    /**
     * dumps the contets of the local database out to a file
     *
     * @param string $file_name the name of the file to save the data to. This should be the full path name
     *
     * @return the result of the shell_exec command
     */
    public function exportDB($file_name)
    {
        $db_password = DB_PASSWORD;
        if (!empty($db_password)) {
            $db_password = " -p".DB_PASSWORD;
        }
        $command = 'mysqldump -h ' . DB_SERVER . ' -u ' . DB_USERNAME . $db_password . ' ' . DB_DATABASE. " > " . $file_name;
        return shell_exec($command);
    }
}