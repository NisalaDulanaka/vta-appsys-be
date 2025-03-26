<?php

/**
 * A simple DB class with functions to interact and automate repetitive database interactions
 */
class DB
{
    /**
     * Represents the current connection
     */
    private static ?mysqli $conn = null;
    private static string $host;
    private static string $dataBase;
    private static string $userName;
    private static string $password;

    public static function connect()
    {
        self::$host = getenv('HOST');
        self::$dataBase = getenv('DB');
        self::$userName = getenv('USER_NAME');
        self::$password = getenv('PASSWORD');

        if (empty(self::$host)) return; // If env is not set return

        //Establish database connections
        self::$conn = new mysqli(self::$host, self::$userName, self::$password, self::$dataBase);

        if (self::$conn->connect_error) {

            exit(0);
        }
    }

    /**
     * Gives access to the current database connection object
     * @return mysqli The current database connection
     */
    public static function getConnection(): mysqli
    {
        return self::$conn;
    }

    /**
     * Executes a simple select query
     * @param string $query The select query
     * @param int $type The type of the resultset
     * @return array The resultset
     */
    public static function select(string $query, int $type = MYSQLI_ASSOC): array
    {
        $results = [];

        try {
            $results = mysqli_fetch_all(self::$conn->query($query), $type);
        } catch (Exception $e) {
            //Do something
        }

        return $results;
    }

    /**
     *  Executes multiple queries at once and frees results sets
     *  Use for insert update and delete related multi queries
     *  @param string $query The multi query to be executed
     *  @return bool Whether the query was successful or not
     */
    public static function multiQuery(string $query): bool
    {
        if (self::$conn->multi_query($query)) {
            // Iterate through all result sets and free them
            do {
                if ($r = self::$conn->store_result()) {
                    $r->free();
                }
            } while (self::$conn->more_results() && self::$conn->next_result());

            return true;
        } else {
            // Handle error
            return false;
        }
    }

    /**
     *  Executes multiple queries at once and frees result sets
     *  Use for multi queries which produce result sets
     *  @param string $query The multi query to be executed
     *  @return array Associative array representing the resultset
     */
    public static function multiSelect(String $query, int $type = 0): array
    {
        $results = [];
        if (self::$conn->multi_query($query)) {
            // Iterate through all result sets and free them
            do {
                if ($r = self::$conn->store_result()) {
                    if ($type === 0) {
                        $results = $r->fetch_all();
                    } else {
                        $results = $r->fetch_all($type);
                    }
                    $r->free();
                }
            } while (self::$conn->more_results() && self::$conn->next_result());
        }

        return $results;
    }
}
