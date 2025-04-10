<?php

require_once __DIR__.'/../../../database/dbconnection.php';  // require_once: is used to embed php code from another file. Throws a fatal error and the system stops. the file is only included(require once).
include_once __DIR__.'/../../../config/settings-configuration.php'; // include_once: used to embed php code from another file. If te file is not found, a warning is shown and the program continues to run. if the file was already included previously, this statement will not include it again.

// the __DIR__ will "hardwire" the absolute path
// a relative path specifies the location of a file or directory relative to the current working directory. it uses . (current directory) and .. (parent directory) to navigate the file system
// an absolute path starts from the root, and it provides a complete and unambiguous location, regardless of the current working directory.

class ADMIN{

    private $conn; // for the database connection
    public function __construct(){ 
        $database = new Database(); // Database is the name od the class in dbconnection.php
        $this->conn = $database->dbConnection();    // this accesses the dbConnection function in the Database class for connecting the database to the system
    }



    // Sign up or Register function
    public function addAdmin($csrf_token, $username, $email, $password){

        // checks if the email already exists in the database, to prevent duplicattion
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute(array(":email" => $email));
        
        // if the row count has existing email, it will show an error
        // it will check if the user email is existing
        if($stmt->rowCount() > 0){ // counts the number of rows 
            echo "<script>alert('Email already exists.'); window.location.href = '../../../';</script>";
            exit;
        }
        
        // verify csrf first, before executing the insert into database
        if (!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)){
            echo "<script>alert('Invalid CSRF Token.'); window.location.href = '../../../'; </script>";
            exit;
        }

        // to regenerate csrf token
        unset($_SESSION['csrf_token']);
        
        //$hash_password = password_hash($password, PASSWORD_DEFAULT);
        $hash_password = md5($password);

        // uses the runQuery function to insert the values into the database
        $stmt = $this->runQuery('INSERT INTO user (username, email, password) VALUES (:username, :email, :password)');
        $exec = $stmt->execute(array(
            ":username" => $username,
            ":email" => $email,
            ":password" => $hash_password
        ));

        if($exec){
            echo "<script>alert('Admin Added Successfully.'); window.location.href = '../../../';</script>";
            exit;
        }else{
            echo "<script>alert('Invalid CSRF Token.'); window.location.href = '../../../';</script>";
            exit;
        }

    }



    // Sign in function
    public function adminSignin($email, $password, $csrf_token){
        try{
            if(!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)){
                echo "<script>alert('Invalid CSRF Token.'); window.location.href = '../../../';</script>";
                exit;
            }
            unset($_SESSION['csrf_token']);

            $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(array(":email" => $email));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount() == 1 && $userRow['password'] == md5($password)){
                $activity = "Has Successfully Signed In";
                $user_id = $userRow['id'];
                $this->logs($activity, $user_id);

                $_SESSION['adminSession'] = $user_id; // stores the user_id value in session
                echo "<script>alert('Welcome.'); window.location.href = '../';</script>";
                exit;
            }else{
                echo "<script>alert('Invalid Credentials.'); window.location.href = '../../../';</script>";
                exit;
            }

        }catch (PDOException $ex){
            echo $ex->getMessage();

        }
        
        
    }



    public function adminSignout(){
        unset($_SESSION['adminSession']); // used to remove a specific session variable from the session. it unregisters the session variable.
        echo "<script>alert('Sign Out Successfully.'); window.location.href = '../../../';</script>";
        exit;
    }



    public function logs($activity, $user_id){
        $stmt = $this->conn->prepare("INSERT INTO logs (user_id, activity) VALUES (:user_id, :activity)");
        $stmt->execute(array(":user_id" => $user_id, ":activity" => $activity));
    }



    public function isUserLoggedIn(){

        if(isset($_SESSION['adminSession'])){
            return true;
        }

    }



    public function redirect(){
        echo "<script>alert('Admin must log in first.'); window.location.href = '../../../';</script>";
        exit;
    }



    // prepares the querys for execution 
    public function runQuery($sql){
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
}



// handles the submission forms. It processes data from an HTML form
if (isset($_POST['btn-signup'])){
    // gives access to the input of the user from the input fields in the Sign Up form 
    $csrf_token = trim($_POST['csrf_token']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $addAdmin = new ADMIN();
    $addAdmin->addAdmin($csrf_token, $username, $email, $password);
}

// $_POST - Contains data sent to the script via on HTML form using the POST method
if(isset($_POST['btn-signin'])){
    $csrf_token = trim($_POST['csrf_token']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $adminSignin = new ADMIN();
    $adminSignin->adminSignin($email, $password, $csrf_token);
}

// $_GET - Contains data sent to the script via URL
if(isset($_GET['admin_signout'])){
    $adminSignout = new ADMIN();
    $adminSignout->adminSignout();
}

?>