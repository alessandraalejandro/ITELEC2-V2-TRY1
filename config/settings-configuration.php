<?php

// a session is used to store information across multiple pages
// session_start(); this function starts a new session or resumes the existing session. 
// This function must be called at the beginning of the script when working with session variables like $_SESSION
session_start();

// Error Reporting
ini_set('display_errors', 1); // Errors will be displayed on the screen.
ini_set('display_startup_errors', 1); // Displays errors that occur during the PHP startup sequence.
error_reporting(E_ALL); // E_ALL - a constant, reports all types of errors(notices, warnings, and fatal errors)
// ini_set() - a function that sets the configuration options in PHP.
// error_reporting() - this function sets the level of error reporting.

// CSRF (Cross Site Request Forgery) TOKEN
if(empty($_SESSION['csrf_token'])){ // checks if the CSRF token is empty. 
    $csrf_token = bin2hex(random_bytes(32)); //generates a unique token
    $_SESSION['csrf_token'] = $csrf_token; //stores the generated token in the session
}else{
    $csrf_token = $_SESSION['csrf_token']; // it uses the existing token
}

?>