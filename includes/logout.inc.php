<?php
session_start();
session_unset();
session_destroy();

header("location: ../signup.php");
exit();






/*
Old code for PDO method
session_start();
session_unset();
session_destroy();

header("Location: ../index.php");
die();
*/