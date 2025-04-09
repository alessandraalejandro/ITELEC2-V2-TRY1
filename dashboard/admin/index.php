<?php

require_once 'authentication/admin-class.php'; 
// require_once: is used to embed php code from another file. If not found a fatal error is thrown and the system stops. if the file is already included it will not include again.

$admin = new ADMIN();
if(!$admin->isUserLoggedIn()){ // the user will be redirected to the sign in page, if the user is not logged in.
    $admin->redirect('../../');
}

$stmt = $admin->runQuery("SELECT * FROM user WHERE id = :id");
$stmt->execute(array(":id" => $_SESSION['adminSession'])); // uses the stored information inside the session
$user_data = $stmt->fetch(PDO::FETCH_ASSOC); // returns a single row from the database based on the query

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ADMIN DASHBOARD</title>
</head>
<body>
    <h1>WELCOME <?php echo $user_data['email'] ?> </h1>
    <button><a href="authentication/admin-class.php?admin_signout">SIGN OUT</a></button>
</body>
</html>