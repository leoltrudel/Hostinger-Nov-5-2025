<!DOCTYPE html>
<html>

<head>

    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    //include_once 'includes/dbh.inc.php';
    include_once 'includes/header.inc.php';
    include_once 'includes/searchresults.inc.php';
    //include_once 'includes/favorites.inc.php';

    //include_once 'includes/usecase.profile.inc.php';
    //include_once 'includes/usecase.techstack_table.inc.php';
    /*
if(isset($_GET['entity'])) {
    $vendID = $_GET['entity'];
    echo $vendID;
} else {
    echo "no such value";
}
*/
    if (isset($_POST['techstackFavoritesList'])) {
        $fav_list_id = isset($_POST['techstackFavoritesList']) ? $_POST['techstackFavoritesList'] : '';
    }

    //$specialTechstackArray = [];

    //$specialTechstackArray = implode(",", $specialTechstackArray);

    //echo is_array($_POST['techid']);
    //var_dump($_POST['techstackFavoritesList']);
    //echo $_POST['techstackFavoritesList'] != 'techstackFavoritesList';
    if (isset($_POST['techstackFavoritesList'])) //&& $_POST['techstackFavoritesList'] == $techstackFavoritesList) 
    {
        // var_dump($_POST);

        if (isset($_POST['techid']) and is_array($_POST['techid'])) {
            foreach ($_POST['techid'] as $tid) {
                $chkQ = "select * FROM favorites_item 
             where fav_id = $fav_list_id and 
                 tech_id = $tid";
                $res = mysqli_query($conn, $chkQ);
                if (empty(mysqli_fetch_assoc($res))) {
                    //$res->num_rows != 0) {  // otherwise, skip it, already exists
                    //    $tableType = 2;
                    $insQ = "INSERT INTO favorites_item (fav_id, tech_id, created_by) 
                VALUES (" . $fav_list_id . "," . $tid . "," . $_SESSION['id'] . ");";
                    //echo $insQ.'<br>'; 
                    mysqli_query($conn, $insQ);
                    $sqlList = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) VALUES (NOW(), $fav_list_id, $userid, $userid)";
                    $result = mysqli_query($conn, $sqlList);
                    $jfu_idRecall = "SELECT MAX(jfu_id) AS max_jfu_id FROM join_favorites_user WHERE adder_id = $userid";
                    $jfuresult = mysqli_query($conn, $jfu_idRecall);
                    if ($jfuresult) {
                        $row = mysqli_fetch_assoc($jfuresult);
                        $jfuid = $row['max_jfu_id'];
                    }
                    $jfuid = intval($jfuid);
                    $adderSQL = "INSERT INTO j_join_favorites_user_adder (jfu_id, adder_id) VALUES ($jfuid, $userid)";
                    mysqli_query($conn, $adderSQL);
                }
            }
        } else {
            echo "Nothing selected to add to favorite list.";
        }
    }

    if (isset($_POST['vendorFavoritesList'])) {
        $fav_list_id_vend = isset($_POST['vendorFavoritesList']) ? $_POST['vendorFavoritesList'] : '';
    }

    if (isset($_POST['vendorFavoritesList'])) //&& $_POST['techstackFavoritesList'] == $techstackFavoritesList) 
    {
        // var_dump($_POST);

        if (isset($_POST['vendid']) and is_array($_POST['vendid'])) {
            foreach ($_POST['vendid'] as $vid) {
                $chkQ = "select * FROM favorites_item 
             where fav_id = $fav_list_id_vend and 
                 vendor_id = $vid";
                $res = mysqli_query($conn, $chkQ);
                if (empty(mysqli_fetch_assoc($res))) {
                    //if ($res->num_rows != 0) {  // otherwise, skip it, already exists
                    //    $tableType = 2;
                    $insQ = "INSERT INTO favorites_item (fav_id, vendor_id, created_by) 
                VALUES (" . $fav_list_id_vend . "," . $vid . "," . $_SESSION['id'] . ");";
                    //echo $insQ.'<br>'; 
                    mysqli_query($conn, $insQ);
                    $sqlList = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) VALUES (NOW(), $fav_list_id_vend, $userid, $userid)";
                    $result = mysqli_query($conn, $sqlList);
                    $jfu_idRecall = "SELECT MAX(jfu_id) AS max_jfu_id FROM join_favorites_user WHERE adder_id = $userid";
                    $jfuresult = mysqli_query($conn, $jfu_idRecall);
                    if ($jfuresult) {
                        $row = mysqli_fetch_assoc($jfuresult);
                        $jfuid = $row['max_jfu_id'];
                    }
                    $jfuid = intval($jfuid);
                    $adderSQL = "INSERT INTO j_join_favorites_user_adder (jfu_id, adder_id) VALUES ($jfuid, $userid)";
                    mysqli_query($conn, $adderSQL);
                }
            }
        } else {
            echo "Nothing selected to add to favorite list.";
        }
    }

    if (isset($_POST['productFavoritesList'])) {
        $fav_list_id_prod = isset($_POST['productFavoritesList']) ? $_POST['productFavoritesList'] : '';
    }

    if (isset($_POST['productFavoritesList'])) //&& $_POST['techstackFavoritesList'] == $techstackFavoritesList) 
    {
        // var_dump($_POST);

        if (isset($_POST['prodid']) and is_array($_POST['prodid'])) {
            foreach ($_POST['prodid'] as $proid) {
                $chkQ = "select * FROM favorites_item 
             where fav_id = $fav_list_id_prod and 
                 product_id = $proid";
                $res = mysqli_query($conn, $chkQ);
                if (empty(mysqli_fetch_assoc($res))) {
                    //if ($res->num_rows != 0) {  // otherwise, skip it, already exists
                    //    $tableType = 2;
                    $insQ = "INSERT INTO favorites_item (fav_id, product_id, created_by) 
                VALUES (" . $fav_list_id_prod . "," . $proid . "," . $_SESSION['id'] . ");";
                    //echo $insQ.'<br>'; 
                    mysqli_query($conn, $insQ);
                    $sqlList = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) VALUES (NOW(), $fav_list_id_prod, $userid, $userid)";
                    $result = mysqli_query($conn, $sqlList);
                    $jfu_idRecall = "SELECT MAX(jfu_id) AS max_jfu_id FROM join_favorites_user WHERE adder_id = $userid";
                    $jfuresult = mysqli_query($conn, $jfu_idRecall);
                    if ($jfuresult) {
                        $row = mysqli_fetch_assoc($jfuresult);
                        $jfuid = $row['max_jfu_id'];
                    }
                    $jfuid = intval($jfuid);
                    $adderSQL = "INSERT INTO j_join_favorites_user_adder (jfu_id, adder_id) VALUES ($jfuid, $userid)";
                    mysqli_query($conn, $adderSQL);
                }
            }
        } else {
            echo "Nothing selected to add to favorite list.";
        }
    }

    //} 

    //Don't send whitespace to browser, and have opening html tage abbut the closing php tag. 

    if ($idValue != 0) {
        require 'includes/usecase.profile.inc.php';
    } //else {
    //echo "figure this out later";
    //}
    ?>

</body>

</html>