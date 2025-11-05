<?php
include_once 'dbh.inc.php';
include_once 'session.inc.php';

///////////////////// Vendor

$vendorFavorites = "
    SELECT fav_name, favorites.fav_id f, favorites_item.vendor_id t, com_name
    FROM favorites 
    RIGHT JOIN favorites_item 
    ON favorites.fav_id = favorites_item.fav_id
    INNER JOIN company
    ON company.com_id = favorites_item.vendor_id
    WHERE owner_id = " . $userid . "
    ORDER BY fav_name;";

$result1 = mysqli_query($conn, $vendorFavorites);
$resultCheck1 = mysqli_num_rows($result1);

list($favID, $favName, $vendorID, $vendorName) = vendorType($result1, $resultCheck1);

function vendorType($result1, $resultCheck1)
{
    $favID = [];
    $favName = [];
    $vendorID = [];
    $vendorName = [];
    
    if($resultCheck1 > 0) {
        while($row = mysqli_fetch_assoc($result1)) {
            $favID[] = $row['f'];
            $favName[] = $row['fav_name'];
            $vendorID[] = $row['t'];
            $vendorName[] = $row['com_name'];
        }
    }
    return array($favID, $favName, $vendorID, $vendorName);
}

$vendorIDsByFavID = array();
for ($i = 0; $i < count($favID); $i++) {
    $fav = $favName[$i]; 
    $vendor = $vendorID[$i];
    if (!isset($vendorIDsByFavID[$fav])) {
        $vendorIDsByFavID[$fav] = array();
    }
    $vendorIDsByFavID[$fav][] = $vendor;
}