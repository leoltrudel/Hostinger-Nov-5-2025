<?php

if(isset($_POST["submit"])) {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["useruid"];
    $pwd = $_POST["pwd"];
    $pwdrepeat = $_POST["pwdrepeat"];

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    if(emptyInputsSignup($name, $email, $username, $pwd, $pwdrepeat) !== false) {
        header("location: ../signup.php?error=emptyinput");
        exit();
    }

    if(invalidUid($username) !== false) {
        header("location: ../signup.php?error=invaliduid");
        exit();
    }

    if(invalidEmail($email) !== false) {
        header("location: ../signup.php?error=invalidemail");
        exit();
    }

    if(pwdMatch($pwd, $pwdrepeat) !== false) {
        header("location: ../signup.php?error=passwordmismatch");
        exit();
    }

    if(uidExists($conn, $username, $email) !== false) {
        header("location: ../signup.php?error=usernametaken");
        exit();
    }

    createUser($conn, $name, $pwd, $email, $username);

}
else {
    header("location: ../signup.php");
    exit();
}

/*

This is the orginal, PDO signup page

if($_SERVER["REQUEST_METHOD"] === "POST") {

$username = $_POST["username"];
$pwd = $_POST["pwd"];
$email = $_POST["email"];

try {
    
    require_once 'dbh.inc.php';
    require_once 'signup_model.inc.php';
    require_once 'signup_contr.inc.php';

    // ERROR HANDLERS
    $errors = [];

    if(is_input_empty($username, $pwd, $email)) {
        $errors["empty_input"] = "Fill in all fields!";
    }
    if(is_email_invalid($email)) {
        $errors["invalid_email"] = "Invalid email used";
    }
    if(is_username_taken($pdo, $username)) {
        $errors["username_taken"] = "Username already taken!";
    }
    if(is_email_registered($pdo, $email)) {
        $errors["email_used"] = "Email already registered!";
    }

    require_once 'config_session.inc.php';

    if($errors) {
        $_SESSION["errors_signup"] = $errors;

        $signupData = [
            "username" => $username,
            "email" => $email
        ];
        $_SESSION["signup_data"] = $signupData;

        header("Location: ../index.php");
        die();
    }

    create_user($pdo, $pwd, $username, $email);   
    header("Location: ../index.php?signup=success");

    $pdo = null;
    $stmt = null;

    die(); 

} catch (PDOEXCEPTION $e) {
    die("Query failed: " . $e->getMessage());
}

} else {
    header("Location: ../index.php");
    die();
}
*/