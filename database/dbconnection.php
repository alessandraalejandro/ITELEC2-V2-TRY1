<?php

class Database {

    // private variables, can only be accessed in this class and file.
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port; // add for changed ports

    // public variables, can be accessed all over the system. It can be used all over the codes.
    public $conn;

    public function __construct(){ // Constructs a server connection

        // the database can neither be a localhost or a live server, in case it is needed to be uploaded

        // it calls the server that you are using through your machine. This are the 3 options for the local server, any one of this can connect our database to our loacl server.
        // $_SERVER - contains information about the server enviroment and the client request. Including data like headers, paths, and script locations.
        if($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1' || $_SERVER['SERVER_ADDR'] === '192.168.1.72'){
            // this is the oop approach of declaring a variable
            $this->host = 'localhost';
            $this->db_name = 'itelec2-try';
            $this->port = '3307'; // add this for changed ports (from 3306 to 3307)
            $this->username = 'root'; 
            $this->password = '';

        }

        // we can edit our site in our local machine while the database is in a web host server
        // this is for when it detect that we are at a web host server
        else{
            $this->host = 'localhost';
            $this->db_name = '';
            $this->username = '';
            $this->password = '';

        }
        

    }

    // a public function can be accessed from anywhere outside the class, meaning you can call this function from other classes or scripts.
    public function dbConnection(){ // this function establishes datbase connection

        // $this - is an object instance It's used within the class to access its properties and methods.
        // conn: an instance variable that will hold the database connection object.
        // = null: meaning that no connection is established yet.
        $this->conn = null;

        // try-catch is for handling exceptions
        try{
            // PDO (PHP Data Objects) - used to interact with database in PHP, with a consistent API.
            // mysqli is prone to SQL injection compared to PDO which is more secured.
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            
            // set error reporting mode for PDO to throw exceptions when errors occur. 
            // setAttribute - is a function in PHP, used to set and attrivute for a PDO connection or statement. 
            // PDO::ATTR_ERRMODE - Error reporting mode of PDO. 
            // PDO::ERRMODE_EXCEPTION - Throws a PDOException if an error occurs.
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception){ //PDOException - represents an error by PDO.
            echo "Connection Error: " . $exception->getMessage();   // getMessage() - retrieves the error message.
        }

        return $this->conn; // returns the db connection status

    }

}

?>