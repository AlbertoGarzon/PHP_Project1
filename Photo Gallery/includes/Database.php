<?php
require_once("config.php");

//this class will contain MySQL specific functions
class MySQLDatabase
{
    private $connection;
    
    //so that when object is instantiated, there is already a connection to db
    public function __construct()
    {
        $this->open_connection();
    }
    public function open_connection()
    {
        $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if(mysqli_connect_errno())
        {
            die("Database connection failed: " .
                mysqli_connect_error().
                " (". mysqli_connect_errno(). ")");
        }
    }

    public function affected_rows()
    {
        $this->connection->affected_rows;
    }

    public function close_connection()
    {
        if(isset($this->connection))
        {
            mysqli_close($this->connection);
            unset($this->connection);
        }
    }
//this method will allow for queries to be executed
    public function query($sql)
    {
        $result = mysqli_query($this->connection, $sql);
        $this->confirm_query($result);
        return $result;
    }

    private function confirm_query($result)
    {
        if(!$result)
        {
            echo mysqli_error($this->connection)."<br /> ";
            die("Database query failed.");
        }
    }

//escapes SQL unfriendly strings
    public function mysql_prep($string)
    {
        $escaped_string =
        mysqli_real_escape_string($this->connection, $string);
        return $escaped_string;
    }

//retrieves a row from the mysql database
    public function fetch_array($result_set)
    {
        return mysqli_fetch_array($result_set);
    }

    public function insert_id()
    {
        $id = mysqli_insert_id($this->connection);
        return $id;
    }
}

$database = new MySQLDatabase();

?>