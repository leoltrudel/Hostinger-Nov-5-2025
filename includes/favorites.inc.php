<?php
//require_once 'dbh.inc.php';
//require_once 'header.inc.php';
require_once 'session.inc.php';

$sqlgrp = "SELECT company.com_id AS grpID
           FROM company
           INNER JOIN join_user_grp
           ON company.com_id = join_user_grp.grp_id
           WHERE join_user_grp.user_id = ?";
$stmt = mysqli_prepare($conn, $sqlgrp);

if ($stmt === false) {
    die('Prepare failed: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $userid);
mysqli_stmt_execute($stmt);
$sqlgrpResult = mysqli_stmt_get_result($stmt);

if (!empty($sqlgrpResult)) {
    while ($row = mysqli_fetch_assoc($sqlgrpResult)) {
        $grpID = $row['grpID'];
    }
}
mysqli_stmt_close($stmt);

$allAssignedUCIDs = "SELECT DISTINCT fav_id FROM favorites_item WHERE uc_id IS NULL";
$stmt = mysqli_prepare($conn, $allAssignedUCIDs);

if ($stmt === false) {
    die('Prepare failed: ' . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$allAssignedUCIDsResult = mysqli_stmt_get_result($stmt);
$allAssignedUCIDsResultCheck = mysqli_num_rows($allAssignedUCIDsResult);
$assignedFavUCIDs = [];

if ($allAssignedUCIDsResultCheck > 0) {
    while ($row = mysqli_fetch_assoc($allAssignedUCIDsResult)) {
        $assignedFavUCIDs[] = $row['fav_id'];
    }
} else {
    $assignedFavUCIDs = array(1);
}
mysqli_stmt_close($stmt);

$allAssignedTechIDs = "SELECT DISTINCT fav_id FROM favorites_item WHERE tech_id IS NULL";
$stmt = mysqli_prepare($conn, $allAssignedTechIDs);

if ($stmt === false) {
    die('Prepare failed: ' . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$allAssignedTechIDsResult = mysqli_stmt_get_result($stmt);
$allAssignedTechIDsResultCheck = mysqli_num_rows($allAssignedTechIDsResult);

if ($allAssignedTechIDsResultCheck > 0) {
    while ($row = mysqli_fetch_assoc($allAssignedTechIDsResult)) {
        $assignedFavTechIDs[] = $row['fav_id'];
    }
} else {
    $assignedFavTechIDs = array(1);
}
mysqli_stmt_close($stmt);

$allAssignedVendorIDs = "SELECT DISTINCT fav_id FROM favorites_item WHERE vendor_id IS NULL";
$stmt = mysqli_prepare($conn, $allAssignedVendorIDs);

if ($stmt === false) {
    die('Prepare failed: ' . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$allAssignedVendorIDsResult = mysqli_stmt_get_result($stmt);
$allAssignedVendorIDsResultCheck = mysqli_num_rows($allAssignedVendorIDsResult);

if ($allAssignedVendorIDsResultCheck > 0) {
    while ($row = mysqli_fetch_assoc($allAssignedVendorIDsResult)) {
        $assignedFavVendorIDs[] = $row['fav_id'];
    }
} else {
    $assignedFavVendorIDs = array(1);
}
mysqli_stmt_close($stmt);

$allAssignedProductIDs = "SELECT DISTINCT fav_id FROM favorites_item WHERE product_id IS NULL";
$stmt = mysqli_prepare($conn, $allAssignedProductIDs);

if ($stmt === false) {
    die('Prepare failed: ' . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$allAssignedProductIDsResult = mysqli_stmt_get_result($stmt);
$allAssignedProductIDsResultCheck = mysqli_num_rows($allAssignedProductIDsResult);

if ($allAssignedProductIDsResultCheck > 0) {
    while ($row = mysqli_fetch_assoc($allAssignedProductIDsResult)) {
        $assignedFavProductIDs[] = $row['fav_id'];
    }
} else {
    $assignedFavProductIDs = array(1);
}
mysqli_stmt_close($stmt);
//$assignedFavIDs = implode(',', $assignedFavIDs);
//echo 'Assigned from favorites_item table: <strong>'.$assignedFavIDs.'</strong><br>';

$allUnassignedIDs = "SELECT fav_id FROM favorites";
$stmt = mysqli_prepare($conn, $allUnassignedIDs);

if ($stmt === false) {
    die('Prepare failed: ' . mysqli_error($conn));
}

mysqli_stmt_execute($stmt);
$allUnassignedIDsResult = mysqli_stmt_get_result($stmt);
$allUnassignedIDsResultCheck = mysqli_num_rows($allUnassignedIDsResult);

if ($allUnassignedIDsResultCheck > 0) {
    while ($row = mysqli_fetch_assoc($allUnassignedIDsResult)) {
        $unassignedFavIDs[] = $row['fav_id'];
    }
}
mysqli_stmt_close($stmt);

if (!empty($unassignedFavIDs)) {
    $allUCIDs = array_diff($unassignedFavIDs, $assignedFavUCIDs);
    $allUCIDs = implode(',', $allUCIDs);
    $allTechIDs = array_diff($unassignedFavIDs, $assignedFavTechIDs);
    $allTechIDs = implode(',', $allTechIDs);
    $allVendorIDs = array_diff($unassignedFavIDs, $assignedFavVendorIDs);
    $allVendorIDs = implode(',', $allVendorIDs);
    $allProductIDs = array_diff($unassignedFavIDs, $assignedFavProductIDs);
    $allProductIDs = implode(',', $allProductIDs);
}
/*
if (!empty($assignedFavTechIDs)) {
    $allTechIDs = array_diff($unassignedFavIDs, $assignedFavTechIDs);
    $allTechIDs = implode(',', $allTechIDs);
}

if (!empty($assignedFavVendorIDs)) {
    $allVendorIDs = array_diff($unassignedFavIDs, $assignedFavVendorIDs);
    $allVendorIDs = implode(',', $allVendorIDs);
}

if (!empty($assignedFavProductIDs)) {
    $allProductIDs = array_diff($unassignedFavIDs, $assignedFavProductIDs);
    $allProductIDs = implode(',', $allProductIDs); 
}
*/

// DEBUG ALL OF THIS!!!!!
if (!empty($allUCIDs)) {
    // Prepare the SQL statement
    $usecaseFavorites = "
    SELECT fav_name, favorites.fav_id
    FROM favorites 
    WHERE favorites.fav_id IN (" . $allUCIDs . ")
    AND owner_id = ?
    GROUP BY fav_name
    ORDER BY fav_name;";

    $stmt = mysqli_prepare($conn, $usecaseFavorites);

    if ($stmt === false) {
        die('Prepare failed: ' . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $userid);
    mysqli_stmt_execute($stmt);
    $result1 = mysqli_stmt_get_result($stmt);
    $resultCheck1 = mysqli_num_rows($result1);
    mysqli_stmt_close($stmt);
}

$ucFavoritesList = [];

function usecaseArrayDropdown($result1, $resultCheck1)
{
    if ($resultCheck1 > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
            $ucFavoritesList[] = [$row['fav_name'], $row['fav_id']];
        }
        return $ucFavoritesList;
    } else {
        return [];
    }
}
//var_dump($ucFavoritesList);

if (!empty($allTechIDs)) {
    $techstackFavorites = "
    SELECT fav_name, favorites.fav_id
    FROM favorites 
    WHERE favorites.fav_id IN (" . $allTechIDs . ")
    AND owner_id IN (" . $userid . ")
    GROUP BY fav_name
    ORDER BY fav_name;";

    $result2 = mysqli_query($conn, $techstackFavorites);
    $resultCheck2 = mysqli_num_rows($result2);
}


function techstackArrayDropdown($result2, $resultCheck2)
{
    if ($resultCheck2 > 0) {
        while ($row = mysqli_fetch_assoc($result2)) {
            $techFavoritesList[] = [$row['fav_name'], $row['fav_id']];
        }
        return $techFavoritesList;
    } else {
        return [];
    }
}

if (!empty($allVendorIDs)) {
    $vendorFavorites = "
    SELECT fav_name, favorites.fav_id
    FROM favorites 
    WHERE favorites.fav_id IN (" . $allVendorIDs . ")
    AND owner_id = " . $userid . "
    GROUP BY fav_name
    ORDER BY fav_name;";

    $result3 = mysqli_query($conn, $vendorFavorites);
    $resultCheck3 = mysqli_num_rows($result3);
}

function vendorArrayDropdown($result3, $resultCheck3)
{
    if ($resultCheck3 > 0) {
        while ($row = mysqli_fetch_assoc($result3)) {
            $vendorFavoritesList[] = [$row['fav_name'], $row['fav_id']];
        }
        return $vendorFavoritesList;
    } else {
        return [];
    }
}

if (!empty($allProductIDs)) {
    $productFavorites = "
    SELECT fav_name, favorites.fav_id
    FROM favorites 
    WHERE favorites.fav_id IN (" . $allProductIDs . ")
    AND owner_id = " . $userid . "
    GROUP BY fav_name
    ORDER BY fav_name;";

    $result90 = mysqli_query($conn, $productFavorites);
    $resultCheck90 = mysqli_num_rows($result90);
}

function productArrayDropdown($result90, $resultCheck90)
{
    if ($resultCheck90 > 0) {
        while ($row = mysqli_fetch_assoc($result90)) {
            $productFavoritesList[] = [$row['fav_name'], $row['fav_id']];
        }
        return $productFavoritesList;
    } else {
        return [];
    }
}


/*
$sql = "SELECT join_favorites_user.user_id userID,
    join_favorites_user.fav_id listIDTech,
    favorites_item.tech_id techID,
    favorites.fav_name favName
    FROM favorites_item
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN join_favorites_user
    ON favorites.fav_id = join_favorites_user.fav_id
    WHERE join_favorites_user.user_id = 34
    AND favorites_item.tech_id IS NOT NULL
    ORDER BY favName";

$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if($resultCheck > 0) {
    $listIDTech = [];
    while($row = mysqli_fetch_assoc($result)) {
        $listIDTech[] = $row['listIDTech'].'<br>';
    }
    $listIDTech = array_unique($listIDTech);
}

$listIDTechCleaned = array_map(function($value) {
    return str_replace('<br>', '', $value);
}, $listIDTech);

$listIDTechString = implode(',', $listIDTechCleaned);
*/
// List Name
$userFavorites = "SELECT join_user_grp.user_id, join_user_grp.grp_id, user.username
    FROM join_user_grp
    INNER JOIN user
    ON join_user_grp.user_id = user.id
    WHERE grp_id = $grpID
    ORDER BY username";

//$user_id = []; // Initialize $user_id as an empty array
//$username = []; // Initialize $username as an empty array

$result5 = mysqli_query($conn, $userFavorites);
$resultCheck5 = mysqli_num_rows($result5);

$userFavorites = "SELECT join_user_grp.user_id, join_user_grp.grp_id, user.username
    FROM join_user_grp
    INNER JOIN user
    ON join_user_grp.user_id = user.id
    WHERE grp_id = $grpID
    ORDER BY username";

//$user_id = []; // Initialize $user_id as an empty array
//$username = []; // Initialize $username as an empty array

$result6 = mysqli_query($conn, $userFavorites);
$resultCheck6 = mysqli_num_rows($result6);

$userFavorites = "SELECT join_user_grp.user_id, join_user_grp.grp_id, user.username
    FROM join_user_grp
    INNER JOIN user
    ON join_user_grp.user_id = user.id
    WHERE grp_id = $grpID
    ORDER BY username";

//$user_id = []; // Initialize $user_id as an empty array
//$username = []; // Initialize $username as an empty array

$result7 = mysqli_query($conn, $userFavorites);
$resultCheck7 = mysqli_num_rows($result7);

$userFavorites = "SELECT join_user_grp.user_id, join_user_grp.grp_id, user.username
    FROM join_user_grp
    INNER JOIN user
    ON join_user_grp.user_id = user.id
    WHERE grp_id = $grpID
    ORDER BY username";

//$user_id = []; // Initialize $user_id as an empty array
//$username = []; // Initialize $username as an empty array

$result8 = mysqli_query($conn, $userFavorites);
$resultCheck8 = mysqli_num_rows($result8);
/*
if ($resultCheck5 > 0) {
    while ($row = mysqli_fetch_assoc($result5)) {
        $user_id[] = $row['user_id'];
        $username[] = $row['username'];
    }
}

print_r($user_id); */
/*
$sql = "SELECT join_user_grp.user_id, join_user_grp.grp_id, user.username
    FROM join_user_grp
    INNER JOIN user
    ON join_user_grp.user_id = user.id
    WHERE grp_id = 1
    ORDER BY username";

$result5 = mysqli_query($conn, $sql);
$resultCheck5 = mysqli_num_rows($result5);
*/
$userFavoritesList = [];
function userArrayDropdown($result6, $resultCheck6)
{
    if ($resultCheck6 > 0) {
        while ($row = mysqli_fetch_assoc($result6)) {
            $userFavoritesList[] = [$row['user_id'], $row['username']];
        }
        return $userFavoritesList;
    } else {
        return [];
    }
}

/*
echo $allUCIDs;
echo '<br>';
print_r($allUCIDs);
echo '<br>';
var_dump($allUCIDs);
*/

if (!empty($allUCIDs)) {
    $usecaseFavorites = "
    SELECT fav_name, favorites.fav_id
    FROM favorites 
    WHERE  favorites.fav_id IN (" . $allUCIDs . ")
    AND owner_id = " . $userid . "
    GROUP BY fav_name
    ORDER BY fav_name;";

    $result9 = mysqli_query($conn, $usecaseFavorites);
    $resultCheck9 = mysqli_num_rows($result9);
}

function usecaseArrayDropdown1($result1, $resultCheck1)
{
    if ($resultCheck1 > 0) {
        while ($row = mysqli_fetch_assoc($result1)) {
            $techFavoritesList[] = [$row['fav_name'], $row['fav_id']];
        }
        return $techFavoritesList;
    } else {
        return [];
    }
}

$sqlCoworkers = "SELECT DISTINCT fav_name, fav_id
FROM favorites
WHERE favorites.owner_id = $userid";
$resultSQLCoworkers = mysqli_query($conn, $sqlCoworkers);
$resultCheckSQLCoworkers = mysqli_num_rows($resultSQLCoworkers);

function coworkerArrayDropdown($resultSQLCoworkers, $resultCheckSQLCoworkers)
{
    if ($resultCheckSQLCoworkers > 0) {
        while ($row = mysqli_fetch_assoc($resultSQLCoworkers)) {
            $coworkerList[] = array_unique([$row['fav_id'], $row['fav_name']]);
        }
        return $coworkerList;
    } else {
        return [];
    }
}

$sqlCoworkers1 = "SELECT DISTINCT fav_name, fav_id
FROM favorites
WHERE favorites.owner_id = $userid";
$resultSQLCoworkers1 = mysqli_query($conn, $sqlCoworkers1);
$resultCheckSQLCoworkers1 = mysqli_num_rows($resultSQLCoworkers1);

function coworkerArrayDropdown1($resultSQLCoworkers1, $resultCheckSQLCoworkers1)
{
    if ($resultCheckSQLCoworkers1 > 0) {
        while ($row = mysqli_fetch_assoc($resultSQLCoworkers1)) {
            $coworkerList1[] = array_unique([$row['fav_id'], $row['fav_name']]);
        }
        return $coworkerList1;
    } else {
        return [];
    }
}



//print_r($validateList);