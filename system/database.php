<?php
class Database
{
    private string $servername = '127.0.0.1';
    private string $username   = 'root';
    private string $password   = '';
       private string  $dbname     = 'app_campo';
    private ?\mysqli $conn     = null;

    /**
     * Establece y devuelve la conexión MySQLi.
     *
     * @return \mysqli
     */
    public function connect(): \mysqli
    {
        if ($this->conn === null) {
            $this->conn = new \mysqli(
                $this->servername,
                $this->username,
                $this->password,
                $this->dbname
            );

            if ($this->conn->connect_error) {
                die('Connection failed: ' . $this->conn->connect_error);
            }
        }

        return $this->conn;
    }
}