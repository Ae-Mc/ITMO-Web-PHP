<?php
abstract class MySQLBase {
    public mysqli $mysql;

    function __construct(?mysqli $mysql = null) {
        if (is_null($mysql)) {
            $this->mysql = new mysqli(
                "192.168.1.32",
                "student",
                "12345678#aA",
                "studing");
            if ($this->mysql->connect_errno) {
                echo "Error! Can't connect to database";
                exit;
            }
        } else {
            $this->mysql = $mysql;
        }

        $this->_initQueries();
    }

    abstract function _initQueries();

    function _prepareQuery(string $query): mysqli_stmt|false {
        $preparedQuery = $this->mysql->prepare($query);
        if($preparedQuery == false) {
            echo "Prepare of query \"$query\"<br>failed with error: {$this->mysql->error}";
        }
        return $preparedQuery;
    }
}
?>
