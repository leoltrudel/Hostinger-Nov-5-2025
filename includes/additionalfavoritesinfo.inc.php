<?php
include_once 'dbh.inc.php';
include_once 'session.inc.php';

$sql = "SELECT username FROM user WHERE id = $userid";
$usernameResult = mysqli_query($conn, $sql);
$usernameResultCheck = mysqli_num_rows($usernameResult);

if ($usernameResultCheck > 0) {
    while ($row = mysqli_fetch_assoc($usernameResult)) {
        $listCreator = $row['username'];
    }
}

$sql = "SELECT created_at, fav_name FROM favorites";
$createdResult = mysqli_query($conn, $sql);
$createdResultCheck = mysqli_num_rows($createdResult);

$createdDates = array();

if ($createdResultCheck > 0) {
    while ($row = mysqli_fetch_assoc($createdResult)) {
        $createdDates[$row['fav_name']] = $row['created_at']; // Associate created_at with the list name 
    }
}
