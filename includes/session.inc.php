<?php
include_once 'dbh.inc.php';
session_start();

if (!empty($_SESSION["id"])) {
    $username = $_SESSION["useruid"];
    $userid = $_SESSION["id"];
    $sql = "SELECT id, com_id, com_name, email 
    FROM user
    INNER JOIN join_user_grp
    ON user.id = join_user_grp.user_id
    INNER JOIN company
    ON join_user_grp.grp_id = company.com_id
    WHERE id = $userid";
    $result = mysqli_query($conn, $sql);

    $grpID = [];
    $grpName = [];
    if (!empty($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $uid = $row['id'];
            $grpID = $row['com_id'];
            $grpName = $row['com_name'];
            $email = $row['email'];
        }
    }

    $sql = "SELECT id FROM user
    INNER JOIN join_user_grp
    ON user.id = join_user_grp.user_id
    WHERE grp_id = $grpID";
    $result = mysqli_query($conn, $sql);

    $userGrpArray = [];
    if (!empty($sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $userGrpArray[] = $row['id'];
        }
    }

    $userGrpString = '(' . implode(',', $userGrpArray) . ')';
}
