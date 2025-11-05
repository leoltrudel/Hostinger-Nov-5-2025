<?php
include_once 'dbh.inc.php';
include_once 'session.inc.php';

///////////////////// Product

$productFavorites = "
    SELECT fav_name, favorites.fav_id f, favorites_item.product_id t, product_name
    FROM favorites 
    RIGHT JOIN favorites_item 
    ON favorites.fav_id = favorites_item.fav_id
    INNER JOIN product
    ON product.prod_id = favorites_item.product_id
    WHERE owner_id = " . $userid . "
    ORDER BY fav_name;";

$result1 = mysqli_query($conn, $productFavorites);
$resultCheck1 = mysqli_num_rows($result1);

list($favID, $favName, $productID, $productName) = productType($result1, $resultCheck1);

function productType($result1, $resultCheck1)
{
    $favID = [];
    $favName = [];
    $productID = [];
    $productName = [];
    
    if($resultCheck1 > 0) {
        while($row = mysqli_fetch_assoc($result1)) {
            $favID[] = $row['f'];
            $favName[] = $row['fav_name'];
            $productID[] = $row['t'];
            $productName[] = $row['product_name'];
        }
    }
    return array($favID, $favName, $productID, $productName);
}

$productIDsByFavID = array();
for ($i = 0; $i < count($favID); $i++) {
    $fav = $favName[$i]; 
    $prod = $productID[$i];
    if (!isset($productIDsByFavID[$fav])) {
        $productIDsByFavID[$fav] = array();
    }
    $productIDsByFavID[$fav][] = $prod;
}