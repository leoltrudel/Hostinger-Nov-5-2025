<?php
include_once 'dbh.inc.php';
include_once 'session.inc.php';

$sql = "SELECT grp_id
FROM join_user_grp
WHERE user_id = " . $userid . ";";

$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

$grpID = [];
if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $grpID = $row['grp_id'];
    }
}

///////////////////// Techstacks
$sql = "SELECT techstack_name.tech_id,
    techstack_name.techstack_name,
    favorites.fav_id favorite, 
    favorites.fav_name, 
    favorites_item.uc_id favs, 
    join_favorites_user.user_id,
    join_user_grp.grp_id,
    join_user_grp.user_id
    FROM techstack_name
    INNER JOIN favorites_item
    ON techstack_name.tech_id = favorites_item.tech_id
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN join_favorites_user
    ON favorites.fav_id = join_favorites_user.fav_id
    INNER JOIN j_join_favorites_user_adder
    ON join_favorites_user.jfu_id = j_join_favorites_user_adder.jfu_id
    INNER JOIN user
    ON j_join_favorites_user_adder.adder_id = user.id
    INNER JOIN join_user_grp
    ON user.id = join_user_grp.user_id
    WHERE join_user_grp.user_id 
    IN (" . $userid . ")
    AND join_user_grp.grp_id
    IN (" . $grpID . ")
    AND favorites_item.uc_id IS NOT NULL
    GROUP BY favs";

$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);


$listNameArray = [];
if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $listNameArray[$row['favorite']] = $row['fav_name'];
    }
}

// Created By
$sql = "SELECT techstack_name.tech_id,
    techstack_name.techstack_name,
    favorites.fav_id favorite, 
    favorites_item.uc_id favs, 
    favorites_item.created_by,
    user.username,
    join_user_grp.grp_id,
    join_user_grp.user_id
    FROM techstack_name
    INNER JOIN favorites_item
    ON techstack_name.tech_id = favorites_item.tech_id
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN user
    ON favorites.created_id = user.id
    INNER JOIN join_user_grp
    ON user.id = join_user_grp.user_id
    WHERE join_user_grp.grp_id
    IN (" . $grpID . ")
    AND favorites_item.tech_id IS NOT NULL
    GROUP BY favs";

$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

$techCreatedByArray = [];
if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $techCreatedByArray[$row['favorite']] = $row['username'];
    }
}

// Created on (date)
$sql = "SELECT techstack_name.tech_id,
    techstack_name.techstack_name,
    favorites.fav_id favorite, 
    favorites_item.uc_id favs, 
    favorites_item.created_by,
    favorites.created_at,
    user.username
    FROM techstack_name
    INNER JOIN favorites_item
    ON techstack_name.tech_id = favorites_item.uc_id
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN user
    ON favorites.created_id = user.id
    INNER JOIN join_user_grp
    ON user.id = join_user_grp.user_id";

$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

$techCreatedAtArray = [];
if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $techCreatedAtArray[$row['favorite']] = $row['created_at'];
    }
}

// Added by
$sql = "SELECT techstack_name.tech_id,
    techstack_name.techstack_name,
    favorites.fav_id favorite, 
    favorites_item.uc_id favs, 
    favorites_item.created_by,
    favorites.created_at,
    j_join_favorites_user_adder.adder_id,
    user.username
    FROM techstack_name
    INNER JOIN favorites_item
    ON techstack_name.tech_id = favorites_item.uc_id
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN join_favorites_user
    ON favorites.fav_id = join_favorites_user.fav_id
    INNER JOIN j_join_favorites_user_adder
    ON join_favorites_user.jfu_id = j_join_favorites_user_adder.jfu_id
    INNER JOIN user
    ON j_join_favorites_user_adder.adder_id = user.id
    INNER JOIN join_user_grp
    ON user.id = join_user_grp.user_id
    WHERE join_user_grp.grp_id
    IN (" . $grpID . ")
    AND favorites_item.tech_id IS NOT NULL
    GROUP BY favs";

$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

$techSharedByArray = [];
if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $techSharedByArray[$row['favorite']] = $row['username'];
    }
}



$techstackFavorites = "
    SELECT fav_name, favorites.fav_id f, favorites_item.tech_id t, techstack_name
    FROM favorites 
    RIGHT JOIN favorites_item 
    ON favorites.fav_id = favorites_item.fav_id
    INNER JOIN techstack_name
    ON techstack_name.tech_id = favorites_item.tech_id
    WHERE owner_id = " . $userid . "
    ORDER BY fav_name;";

$result2 = mysqli_query($conn, $techstackFavorites);
$resultCheck2 = mysqli_num_rows($result2);

list($favID, $favName, $techID, $techName) = techType($result2, $resultCheck2);

function techType($result2, $resultCheck2)
{
    $favID = [];
    $favName = [];
    $techID = [];
    $techName = [];

    if ($resultCheck2 > 0) {
        while ($row = mysqli_fetch_assoc($result2)) {
            $favID[] = $row['f'];
            $favName[] = $row['fav_name'];
            $techID[] = $row['t'];
            $techName[] = $row['techstack_name'];
        }
    }
    return array($favID, $favName, $techID, $techName);
}

$techIDsByFavID = array();
for ($i = 0; $i < count($favID); $i++) {
    $fav = $favName[$i];
    $tech = $techID[$i];
    if (!isset($techIDsByFavID[$fav])) {
        $techIDsByFavID[$fav] = array();
    }
    $techIDsByFavID[$fav][] = $tech;
}
