<?php
include_once 'dbh.inc.php';
include_once 'session.inc.php';

///////////////////// Use Case

$sql = "SELECT grp_id
FROM join_user_grp
WHERE user_id = " . $userid . ";";

$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

$grpID = [];
if($resultCheck > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $grpID = $row['grp_id'];
    }
}

//var_dump($grpID);

$ucstackFavorites = "
    SELECT join_user_grp.grp_id useThatID, join_user_grp.user_id flaca, fav_name, favorites.fav_id f, favorites_item.uc_id t, use_case_name, favorites.created_at c
    FROM join_user_grp
    INNER JOIN user
    ON join_user_grp.user_id = user.id
    INNER JOIN join_favorites_user
    ON user.id = join_favorites_user.user_id
    INNER JOIN favorites
    ON join_favorites_user.fav_id = favorites.fav_id
    INNER JOIN favorites_item 
    ON favorites.fav_id = favorites_item.fav_id
    INNER JOIN use_case
    ON use_case.uc_id = favorites_item.uc_id
    WHERE join_user_grp.grp_id = " . $grpID . " 
    AND join_user_grp.user_id = " . $userid . "
    GROUP BY f
    ORDER BY fav_name;";

    // this needs to be changed to grp ID, not user ID.
    // The userID will be used to identify a group, and the grpID will be used as the variable. 
    // You need to write another query above this one, that does these steps. 

$result1 = mysqli_query($conn, $ucstackFavorites);
$resultCheck1 = mysqli_num_rows($result1);
/*
if($resultCheck1 > 0) {
    while($row = mysqli_fetch_assoc($result1)) {
        $favID[] = $row['t'];
    }
}
*/

//var_dump($favID);

list($favID, $favName, $ucID, $ucName, $ucCreateDate, $idOfUser) = ucType($result1, $resultCheck1);

function ucType($result1, $resultCheck1)
{
    $favID = [];
    $favName = [];
    $ucID = [];
    $ucName = [];
    $ucCreateDate = [];
    $idOfUser = [];
    
    if($resultCheck1 > 0) {
        while($row = mysqli_fetch_assoc($result1)) {
            $favID[] = $row['f'];
            $favName[] = $row['fav_name'];
            $ucID[] = $row['t'];
            $ucName[] = $row['use_case_name'];
            $ucCreateDate[] = $row['c'];
            $idOfUser[] = $row['useThatID'];
        }
    }
    return array($favID, $favName, $ucID, $ucName, $ucCreateDate, $idOfUser);
}

$usecaseIDsByFavID = array();
for ($i = 0; $i < count($favID); $i++) {
    $favoriteID = $favID[$i];
    $fav = $favName[$i]; 
    $uc = $ucID[$i];
    $create = $ucCreateDate[$i];
    if (!isset($usecaseIDsByFavID[$fav])) {
        $usecaseIDsByFavID[$fav] = array();
    }
    $usecaseIDsByFavID[$fav][] = $uc;
}

//echo '<br><br>';
var_dump($favID);