<?php

require_once __DIR__.'/../../../database/dbconnection.php';
include_once __DIR__.'/../../../config/settings-configuration.php';

class ADMIN{

    private $conn; // for the database connection
    public function __construct(){ 
        $database = new Database(); // Database is the name od the class in dbconnection.php
        $this->conn = $database->dbConnection();    // this accesses the dbConnection function in the Database class for connecting the database to the system
    }

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

        $stmt = $this->runQuery('INSERT INTO user (username, email, password) VALUES (:username, :email, :password)');
        $exec = $stmt->execute(array(
            ":username" => $username,
            ":email" => $email,
            ":password" => $hash_password
        ));

        if($exec){
            echo "<script>alert('Admin Added Successfully.'); window.location.href = '../../../index.php';</script>";
            exit;
        }else{
            echo "<script>alert('Invalid CSRF Token.'); window.location.href = '../../../index.php';</script>";
            exit;
        }

    }

    public function adminSignin($email, $password, $csrf_token){
        try{
            if (!isset($csrf_token) || !hash_equals($_SESSION['csrf_token'], $csrf_token)){
                echo "<script>alert('Invalid CSRF Token.'); window.location.href = '../../../index.php';</script>";
                exit;
            }
            unset($_SESSION['csrf_token']);

            $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->execute(array(":email" => $email));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if($stmt->rowCount() == 1 && $userRow['password'] == md5($password)){
                $activty = "Has Successfully Signed In";
                $user_id = $userRow['id'];
                $this->logs($activity, $user_id);

                $_SESSION['adminSession'] = $user_id;
                echo "<script>alert('Welcome.'); window.location.href = '../index.php';</script>";
                exit;
            }else{
                echo "<script>alert('Invalid Credentials.'); window.location.href = '../../../index.php';</script>";
                exit;
            }

        }catch (PDOException $ex){
            echo $ex->getMessage();

        }
        
        
    }

    public function adminSignout(){
        unset($_SESSION['adminSession']);
        echo "<script>alert('Sign Out Successfully.'); window.location.href = '../../../index.php';</script>";
        exit;
    }

    public function logs($activty, $user_id){
        $stmt = $this->conn->prepare("INSERT INTO logs (user_id, activity) VALUES (:user_id, :activity)");
        $stmt->execute(array(":user_id" => $user_id, ":activity" => $activity));
    }

    public function isUserLoggedIn(){

        if(isset($_SESSION['adminSession'])){
            return true;
        }
    }

    public function redirect(){
        echo "<script>alert('Admin must log in first.'); window.location.href = '../../../index.php';</script>";
        exit;
    }

    public function runQuery($sql){
        $stmt = $this->conn->prepare($sql);
        return $stmt;
    }
}



if (isset($_POST['btn-signup'])){
    // gives access to the input of the user from the input fields in the Sign Up form 
    $csrf_token = trim($_POST['csrf_token']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $addAdmin = new ADMIN();
    $addAdmin->addAdmin($csrf_token, $username, $email, $password);
}

if(isset($_POST['btn-signin'])){
    $csrf_token = trim($_POST['csrf_token']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $adminSignin = new ADMIN();
    $adminSignin->adminSignin($email, $password, $csrf_token);
}

if(isset($_GET['admin_signout'])){
    $adminSignout = new ADMIN();
    $adminSignout->adminSignout();
}

?>