<?php
include_once 'session.inc.php';
include_once 'favorites.inc.php';
include_once 'dbh.inc.php';
/*
$server = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "myfirstdatabase";
*/

/*
//Use Case Dropdown
echo 'Use Case Dropdown<br>';
echo '<select name="usecaseFavoritesList" id="usecaseFavoritesList">';
if (!empty($result1)) {
    $usecaseFavoritesList = techstackArrayDropdown($result1, $resultCheck1);
}

if(!empty($usecaseFavoritesList)) {
    foreach($usecaseFavoritesList as $favorite) {
        echo "<option value='$favorite[1]'>$favorite[0]</option>";
    }
    echo '</select>';
}

echo '<br><br>'; */



//echo '<br><br>';



//echo '<br><br>';



$conn = mysqli_connect($server, $dbUsername, $dbPassword, $dbName);

if (!$conn) {
    die("Connection failed" . mysqli_connect_error());
}
// Fetching all favorite IDs for a specific owner
$favIDAll = "SELECT DISTINCT fav_id FROM favorites WHERE owner_id = 35";
$resultFavIDAll = mysqli_query($conn, $favIDAll);

$allFavsArray = [];

// Store the fav_id into the array
if (!empty($resultFavIDAll)) {
    while ($row = mysqli_fetch_assoc($resultFavIDAll)) {
        $allFavsArray[] = $row['fav_id'];
    }
}

// Create a comma-separated string of all fav_id values
$allFavsArrayString = implode(',', $allFavsArray);

// Fetching fav_ids that meet certain conditions
$favIDType = "SELECT DISTINCT fav_id FROM favorites_item WHERE fav_id IN ($allFavsArrayString) AND uc_id IS NULL";
$resultFavIDType = mysqli_query($conn, $favIDType);

$favIDTypeArray = [];

if (!empty($resultFavIDType)) {
    while ($row = mysqli_fetch_assoc($resultFavIDType)) {
        $favIDTypeArray[] = $row['fav_id'];
    }
}

// Convert the array back to an array of fav_id
$finalUCResult = array_diff($allFavsArray, $favIDTypeArray);

$newfavs = [];

// No need for mysqli_fetch_assoc here as $finalUCResult is an array
if (!empty($finalUCResult)) {
    foreach ($finalUCResult as $fav_id) {
        $newfavs[] = $fav_id;
    }
}

//print_r($newfavs);

$sqlUsecase = "SELECT DISTINCT uc_id, use_case_name FROM use_case ORDER BY use_case_name";
$resultSQLUsecase = mysqli_query($conn, $sqlUsecase);

$sqlTechstack = "SELECT DISTINCT tech_id, techstack_name FROM techstack_name ORDER BY techstack_name";
$resultSQLTechstack = mysqli_query($conn, $sqlTechstack);

$sqlProduct = "SELECT DISTINCT prod_id, product_name FROM product ORDER BY product_name";
$resultSQLProduct = mysqli_query($conn, $sqlProduct);

$sqlVendor = "SELECT DISTINCT com_id, com_name FROM company ORDER BY com_name";
$resultSQLVendor = mysqli_query($conn, $sqlVendor);

$ucID = [];
$ucName = [];

if ($resultSQLUsecase && mysqli_num_rows($resultSQLUsecase) > 0) {
    while ($row = mysqli_fetch_assoc($resultSQLUsecase)) {
        $ucID[] = $row['uc_id'];
        $ucName[] = $row['use_case_name'];
    }
}

$techstackID = [];
$techstackName = [];

if ($resultSQLTechstack && mysqli_num_rows($resultSQLTechstack) > 0) {
    while ($row = mysqli_fetch_assoc($resultSQLTechstack)) {
        $techstackID[] = $row['tech_id'];
        $techstackName[] = $row['techstack_name'];
    }
}

$productID = [];
$productName = [];

if ($resultSQLProduct && mysqli_num_rows($resultSQLProduct) > 0) {
    while ($row = mysqli_fetch_assoc($resultSQLProduct)) {
        $productID[] = $row['prod_id'];
        $productName[] = $row['product_name'];
    }
}

$vendorID = [];
$vendorName = [];

if ($resultSQLVendor && mysqli_num_rows($resultSQLVendor) > 0) {
    while ($row = mysqli_fetch_assoc($resultSQLVendor)) {
        $vendorID[] = $row['com_id'];
        $vendorName[] = $row['com_name'];
    }
}

function listSelectionDropdown($resultSQL, $resultCheckSQL)
{
    $list = [];
    if ($resultCheckSQL > 0) {
        while ($row = mysqli_fetch_assoc($resultSQL)) {
            $list[] = [$row['fav_id'], $row['fav_name']];
        }
    }
    return $list;
}

$sqlMinusListUC = "SELECT DISTINCT fav_id FROM favorites_item WHERE uc_id IS NULL";
$sqlMinusListTech = "SELECT DISTINCT fav_id FROM favorites_item WHERE tech_id IS NULL";
$sqlMinusListProd = "SELECT DISTINCT fav_id FROM favorites_item WHERE product_id IS NULL";
$sqlMinusListVend = "SELECT DISTINCT fav_id FROM favorites_item WHERE vendor_id IS NULL";

$resultSQLMinusListUC = mysqli_query($conn, $sqlMinusListUC);
if (!empty($resultSQLMinusListUC)) {
    while ($row = mysqli_fetch_assoc($resultSQLMinusListUC)) {
        $ucMinusList[] = $row['fav_id'];
    }
}

$resultSQLMinusListTech = mysqli_query($conn, $sqlMinusListTech);
if (!empty($resultSQLMinusListTech)) {
    while ($row = mysqli_fetch_assoc($resultSQLMinusListTech)) {
        $techMinusList[] = $row['fav_id'];
    }
}

$resultSQLMinusListProd = mysqli_query($conn, $sqlMinusListProd);
if (!empty($resultSQLMinusListProd)) {
    while ($row = mysqli_fetch_assoc($resultSQLMinusListProd)) {
        $prodMinusList[] = $row['fav_id'];
    }
}
/*
print_r($favIDTypeArray);
echo '<br><br>';
print_r($prodMinusList);
echo '<br><br>';
*/
$resultSQLMinusListVend = mysqli_query($conn, $sqlMinusListVend);
if (!empty($resultSQLMinusListVend)) {
    while ($row = mysqli_fetch_assoc($resultSQLMinusListVend)) {
        $vendMinusList[] = $row['fav_id'];
    }
}

//$ucListRefine = array_intersect();

/*
print_r($ucMinusList);
print_r($techMinusList);
print_r($prodMinusList);
print_r($vendMinusList);
*/
$sqlList = "SELECT DISTINCT fav_name, fav_id 
    FROM favorites 
    WHERE favorites.owner_id = 35";
$resultSQL = mysqli_query($conn, $sqlList);
$resultCheckSQL = mysqli_num_rows($resultSQL);

// Create a switch statement here too
// Do this by creating four queries that can be subtracted from the $listVariable value
$listVariable = listSelectionDropdown($resultSQL, $resultCheckSQL);

$listTypeArray = ["Use Cases", "Techstacks", "Products", "Vendors"];

// Initialize selected values to maintain across submissions
$selectedTypeValue = isset($_POST['selectListType']) ? $_POST['selectListType'] : null;
$selectedListValue = isset($_POST['selectList1']) ? $_POST['selectList1'] : null;
$selectedDropdownValue = isset($_POST['useCaseDropdown']) ? $_POST['useCaseDropdown'] : (
    isset($_POST['techstackDropdown']) ? $_POST['techstackDropdown'] : (
        isset($_POST['productDropdown']) ? $_POST['productDropdown'] : (
            isset($_POST['vendorDropdown']) ? $_POST['vendorDropdown'] : null
        )
    )
);

//echo '<form action="#" method="post">';

// First Dropdown
echo '<label for="selectListType"><i>What Kind of Items Would You Like To Add?</i></label><br>';
echo '<select name="selectListType" id="selectListType" style="width: 200px;">';
echo '<option value="" disabled ' . ($selectedTypeValue ? '' : 'selected') . '>Select One...</option>';
foreach ($listTypeArray as $type) {
    $selected = ($selectedTypeValue === $type) ? 'selected' : '';
    echo "<option value='{$type}' {$selected}>{$type}</option>";
}
//print_r($selectedTypeValue);
echo '</select>';

if ($selectedTypeValue) {
    echo '<br><br>';
    echo '<label for="selectList1"><i>Which List Would You Like To Add Them To?</i></label><br>';
    echo '<select name="selectList1" id="selectList1" style="width: 200px;">';

    switch ($selectedTypeValue) {
        case 'Use Cases':
            // Use Case dropdown
            echo '<option value="" disabled ' . ($selectedListValue ? '' : 'selected') . '>Select One...</option>';
            if (!empty($result1)) {
                $usecaseFavoritesList = usecaseArrayDropdown($result1, $resultCheck1);
            }

            if (!empty($usecaseFavoritesList)) {
                foreach ($usecaseFavoritesList as $favorite) {
                    $selected = ($selectedListValue == $favorite[1]) ? 'selected' : '';
                    echo "<option value='{$favorite[1]}' {$selected}>{$favorite[0]}</option>";
                }
            }
            break;

        case 'Techstacks':
            // Techstack dropdown
            echo '<option value="" disabled ' . ($selectedListValue ? '' : 'selected') . '>Select One...</option>';
            if (!empty($result2)) {
                $techstackFavoritesList = techstackArrayDropdown($result2, $resultCheck2);
            }

            if (!empty($techstackFavoritesList)) {
                foreach ($techstackFavoritesList as $favorite) {
                    $selected = ($selectedListValue == $favorite[1]) ? 'selected' : '';
                    echo "<option value='{$favorite[1]}' {$selected}>{$favorite[0]}</option>";
                }
            }
            break;

        case 'Vendors':
            // Vendor dropdown
            echo '<option value="" disabled ' . ($selectedListValue ? '' : 'selected') . '>Select One...</option>';
            if (!empty($result3)) {
                $vendorFavoritesList = vendorArrayDropdown($result3, $resultCheck3);
            }

            if (!empty($vendorFavoritesList)) {
                foreach ($vendorFavoritesList as $favorite) {
                    $selected = ($selectedListValue == $favorite[1]) ? 'selected' : '';
                    echo "<option value='{$favorite[1]}' {$selected}>{$favorite[0]}</option>";
                }
            }
            break;

        case 'Products':
            // Product dropdown
            echo '<option value="" disabled ' . ($selectedListValue ? '' : 'selected') . '>Select One...</option>';
            if (!empty($result4)) {
                $productsFavoritesList = productArrayDropdown($result90, $resultCheck90);
            }

            if (!empty($productsFavoritesList)) {
                foreach ($productsFavoritesList as $favorite) {
                    $selected = ($selectedListValue == $favorite[1]) ? 'selected' : '';
                    echo "<option value='{$favorite[1]}' {$selected}>{$favorite[0]}</option>";
                }
            }
            break;

        default:
            echo '<option value="" disabled>No options available</option>';
            break;
    }
    echo '</select>';
}

//print_r($listVariable);


if ($selectedListValue) {
    echo '<br><br>';

    // Function to generate dropdowns
    function generateDropdown($idArray, $nameArray, $label, $dropdownName, $selectedValue)
    {
        echo '<label for="' . htmlspecialchars($dropdownName) . '"><i>Choose a ' . htmlspecialchars($label) . '</i></label><br>';
        echo '<select name="' . htmlspecialchars($dropdownName) . '" id="' . htmlspecialchars($dropdownName) . '" style="width: 200px;">';
        echo '<option value="" disabled ' . ($selectedValue ? '' : 'selected') . '>Select a ' . htmlspecialchars($label) . '...</option>';
        for ($i = 0; $i < count($idArray); $i++) {
            $selected = ($selectedValue == $idArray[$i]) ? 'selected' : ''; // Preserve selected value
            echo '<option value="' . htmlspecialchars($idArray[$i]) . '" ' . $selected . '>' . htmlspecialchars($nameArray[$i]) . '</option>';
        }
        echo '</select>';
    }

    // Initialize a refresh flag
    $refreshPage = false;

    // Generate the appropriate dropdown based on the selected type
    switch ($selectedTypeValue) {
        case 'Use Cases':
            if (!empty($ucID)) {
                generateDropdown($ucID, $ucName, 'Use Case', 'useCaseDropdown', $selectedDropdownValue);

                // Execute additional code when the Submit button is clicked
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['useCaseDropdown'])) {
                    $techIds = [$_POST['useCaseDropdown']];
                    foreach ($techIds as $tid) {
                        $chkQ = "SELECT * FROM favorites_item 
                             WHERE fav_id = $selectedListValue AND uc_id = $tid";
                        $res = mysqli_query($conn, $chkQ);
                        if (empty(mysqli_fetch_assoc($res))) {
                            $insQ = "INSERT INTO favorites_item (fav_id, uc_id, created_by) 
                                 VALUES ($selectedListValue, $tid, $userid);";
                            mysqli_query($conn, $insQ);

                            $sqlList = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) 
                                    VALUES (NOW(), $selectedListValue, $userid, $userid)";
                            $result = mysqli_query($conn, $sqlList);

                            $jfu_idRecall = "SELECT MAX(jfu_id) AS max_jfu_id 
                                         FROM join_favorites_user 
                                         WHERE adder_id = $userid";
                            $jfuresult = mysqli_query($conn, $jfu_idRecall);
                            if ($jfuresult) {
                                $row = mysqli_fetch_assoc($jfuresult);
                                $jfuid = $row['max_jfu_id'];
                            }
                            $jfuid = intval($jfuid);
                            $adderSQL = "INSERT INTO j_join_favorites_user_adder (jfu_id, adder_id) 
                                     VALUES ($jfuid, $userid)";
                            mysqli_query($conn, $adderSQL);

                            // Set the refresh flag
                            $refreshPage = true;
                        }
                    }
                }
            } else {
                echo '<p>No Use Cases available.</p>';
            }
            break;

        case 'Techstacks':
            if (!empty($techstackID)) {
                generateDropdown($techstackID, $techstackName, 'Techstack', 'techstackDropdown', $selectedDropdownValue);

                // Execute additional code when the Submit button is clicked
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['techstackDropdown'])) {
                    $techIds = [$_POST['techstackDropdown']];
                    foreach ($techIds as $tid) {
                        $chkQ = "SELECT * FROM favorites_item 
                             WHERE fav_id = $selectedListValue AND tech_id = $tid";
                        $res = mysqli_query($conn, $chkQ);
                        if (empty(mysqli_fetch_assoc($res))) {
                            $insQ = "INSERT INTO favorites_item (fav_id, tech_id, created_by) 
                                 VALUES ($selectedListValue, $tid, $userid);";
                            mysqli_query($conn, $insQ);

                            $sqlList = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) 
                                    VALUES (NOW(), $selectedListValue, $userid, $userid)";
                            $result = mysqli_query($conn, $sqlList);

                            $jfu_idRecall = "SELECT MAX(jfu_id) AS max_jfu_id 
                                         FROM join_favorites_user 
                                         WHERE adder_id = $userid";
                            $jfuresult = mysqli_query($conn, $jfu_idRecall);
                            if ($jfuresult) {
                                $row = mysqli_fetch_assoc($jfuresult);
                                $jfuid = $row['max_jfu_id'];
                            }
                            $jfuid = intval($jfuid);
                            $adderSQL = "INSERT INTO j_join_favorites_user_adder (jfu_id, adder_id) 
                                     VALUES ($jfuid, $userid)";
                            mysqli_query($conn, $adderSQL);

                            // Set the refresh flag
                            $refreshPage = true;
                        }
                    }
                }
            } else {
                echo '<p>No Techstacks available.</p>';
            }
            break;

        case 'Products':
            if (!empty($productID)) {
                generateDropdown($productID, $productName, 'Product', 'productDropdown', $selectedDropdownValue);

                // Execute additional code when the Submit button is clicked
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['productDropdown'])) {
                    $techIds = [$_POST['productDropdown']];
                    foreach ($techIds as $tid) {
                        $chkQ = "SELECT * FROM favorites_item 
                             WHERE fav_id = $selectedListValue AND product_id = $tid";
                        $res = mysqli_query($conn, $chkQ);
                        if (empty(mysqli_fetch_assoc($res))) {
                            $insQ = "INSERT INTO favorites_item (fav_id, product_id, created_by) 
                                 VALUES ($selectedListValue, $tid, $userid);";
                            mysqli_query($conn, $insQ);

                            $sqlList = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) 
                                    VALUES (NOW(), $selectedListValue, $userid, $userid)";
                            $result = mysqli_query($conn, $sqlList);

                            $jfu_idRecall = "SELECT MAX(jfu_id) AS max_jfu_id 
                                         FROM join_favorites_user 
                                         WHERE adder_id = $userid";
                            $jfuresult = mysqli_query($conn, $jfu_idRecall);
                            if ($jfuresult) {
                                $row = mysqli_fetch_assoc($jfuresult);
                                $jfuid = $row['max_jfu_id'];
                            }
                            $jfuid = intval($jfuid);
                            $adderSQL = "INSERT INTO j_join_favorites_user_adder (jfu_id, adder_id) 
                                     VALUES ($jfuid, $userid)";
                            mysqli_query($conn, $adderSQL);

                            // Set the refresh flag
                            $refreshPage = true;
                        }
                    }
                }
            } else {
                echo '<p>No Products available.</p>';
            }
            break;

        case 'Vendors':
            if (!empty($vendorID)) {
                generateDropdown($vendorID, $vendorName, 'Vendor', 'vendorDropdown', $selectedDropdownValue);

                // Execute additional code when the Submit button is clicked
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['vendorDropdown'])) {
                    $vendIds = [$_POST['vendorDropdown']];
                    foreach ($vendIds as $vid) {
                        $chkQ = "SELECT * FROM favorites_item 
                             WHERE fav_id = $selectedListValue AND vendor_id = $vid";
                        $res = mysqli_query($conn, $chkQ);
                        if (empty(mysqli_fetch_assoc($res))) {
                            $insQ = "INSERT INTO favorites_item (fav_id, vendor_id, created_by) 
                                 VALUES ($selectedListValue, $vid, $userid);";
                            mysqli_query($conn, $insQ);

                            $sqlList = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) 
                                    VALUES (NOW(), $selectedListValue, $userid, $userid)";
                            $result = mysqli_query($conn, $sqlList);

                            $jfu_idRecall = "SELECT MAX(jfu_id) AS max_jfu_id 
                                         FROM join_favorites_user 
                                         WHERE adder_id = $userid";
                            $jfuresult = mysqli_query($conn, $jfu_idRecall);
                            if ($jfuresult) {
                                $row = mysqli_fetch_assoc($jfuresult);
                                $jfuid = $row['max_jfu_id'];
                            }
                            $jfuid = intval($jfuid);
                            $adderSQL = "INSERT INTO j_join_favorites_user_adder (jfu_id, adder_id) 
                                     VALUES ($jfuid, $userid)";
                            mysqli_query($conn, $adderSQL);

                            // Set the refresh flag
                            $refreshPage = true;
                        }
                    }
                }
            } else {
                echo '<p>No Vendors available.</p>';
            }
            break;

        default:
            echo '<p>Please select a valid dropdown type.</p>';
            break;
    }

    // If the refresh flag is set, output a JavaScript snippet to refresh the page
    if ($refreshPage) {
        echo '<script type="text/javascript">window.location.reload();</script>';
    }
}

echo '<br><br>';




////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////