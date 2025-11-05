<?php
ob_start();
include_once 'includes/header.inc.php';
$searchOutput = ob_get_contents();
ob_end_clean();
include_once 'includes/searchresults.inc.php';
include_once 'includes/favorites.inc.php';

if (!empty($indexArray)) {
    $indexArray = array_map(function ($value) use ($conn) {
        return mysqli_real_escape_string($conn, $value);
    }, $indexArray);

    $indexString = implode(",", $indexArray);
    $query = "SELECT * FROM use_case WHERE uc_id IN (" . $indexString . ")";
    $result = mysqli_query($conn, $query);

    $ucIDValue = [];
    $ucNameValue = [];
    $ucDescriptionValue = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ucIDValue[] = $row['uc_id'];
            $ucNameValue[] = $row['use_case_name'];
            $ucDescriptionValue[] = $row['use_case_description'];
        }
    } else {
        $error = "Error executing query: " . mysqli_error($conn);
    }
} else {
    $noResults = true;
}

$favIDFetch = 0;

$sql = "SELECT grp_id FROM join_user_grp WHERE user_id = " . $userid . ";";
$result = mysqli_query($conn, $sql);
$grpID = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $grpID = intval($row['grp_id']);
    }
}

if (isset($_POST['submit1'])) {
    $dup = 0;
    $inputName = isset($_POST['inputName']) ? trim($_POST['inputName']) : '';
    if (!empty($inputName)) {
        $sql = "SELECT * FROM favorites WHERE fav_name = '" . $inputName . "' AND owner_id = " . $_SESSION['id'];
        $res = mysqli_query($conn, $sql);
        if ($res->num_rows > 0) {
            $listError = "Error: List name already exists";
            $dup = 1;
        }
        if (!$dup) {
            $sql = "INSERT INTO favorites (created_at, fav_name, created_by, created_id, owner_id, active)
                        VALUES ('$dateTime', '$inputName', '$username', $userid, $userid, 1)";
            if (mysqli_query($conn, $sql) === TRUE) {
                $successMessage = "Record <strong>" . $inputName . "</strong> added successfully";
                $favIDFetch = "SELECT MAX(fav_id) AS max_fav_id FROM favorites WHERE created_id = $userid";
                $result = mysqli_query($conn, $favIDFetch);
                if (!empty($result)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $favID = intval($row['max_fav_id']);
                    }
                }
                $sqlGrp = "INSERT INTO join_favorites_grp (favorite_id, grp_id) VALUES ($favID, $grpID)";
                mysqli_query($conn, $sqlGrp);

                $query = "SELECT use_case_unique_id FROM `use_case_log` 
                              WHERE submitted_by = $userid 
                              AND query_no = (SELECT MAX(query_no) FROM `use_case_log` WHERE submitted_by = $userid)";
                $result = mysqli_query($conn, $query);

                if (!empty($result)) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $reQuery[] = $row['use_case_unique_id'];
                    }
                }

                $indexString = implode(',', $reQuery);
                $query = "SELECT * FROM use_case WHERE uc_id IN (" . $indexString . ")";
                $result = mysqli_query($conn, $query);

                $ucIDValue = [];
                $ucNameValue = [];
                $ucDescriptionValue = [];

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $ucIDValue[] = $row['uc_id'];
                        $ucNameValue[] = $row['use_case_name'];
                        $ucDescriptionValue[] = $row['use_case_description'];
                    }
                }
                //header("Location: " . $_SERVER['PHP_SELF'] . '#');
                //exit();
            } else {
                $sqlError = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    } else {
        $emptyTextboxError = "Textbox cannot be empty.";
    }
}
$sql = "SELECT * FROM favorite_table_type";
$result10 = mysqli_query($conn, $sql);
if ($result10) {
    $listTypeID = array();
    $listTypeName = array();
    while ($row = mysqli_fetch_assoc($result10)) {
        $listTypeID[] = $row['table_type_id'];
        $listTypeName[] = $row['table_type'];
    }
}

$usecaseFavoritesList = usecaseArrayDropdown1($result9, $resultCheck9);

if (isset($_POST['submitQuery'])) {
    $ucQueryNumber = "SELECT MAX(query_no) as `maxqueryno`, use_case_log_id FROM `use_case_log` WHERE submitted_by = $userid";
    $ucQueryNumberResult = mysqli_query($conn, $ucQueryNumber);
    $ucQueryNumberArray = mysqli_fetch_array($ucQueryNumberResult);
    $ucQueryNumberIncrement = $ucQueryNumberArray['maxqueryno'];
    $ucQueryNumberIncrement = intval($ucQueryNumberIncrement);
    $ucQueryNumberIncrement++;
    foreach ($indexArray as $index) {
        $submitToDatabase = "INSERT INTO use_case_log (query_no, created_at, use_case_unique_id, submitted_by) VALUES ('$ucQueryNumberIncrement', '$dateTime', '$index', '$userid')";
        mysqli_query($conn, $submitToDatabase);
    }
} else {
    $sql = "SELECT use_case_unique_id
        FROM `use_case_log`
        WHERE submitted_by = $userid
        AND query_no = (SELECT MAX(query_no) 
        FROM `use_case_log` 
        WHERE submitted_by = $userid);";
    $result = mysqli_query($conn, $sql);

    if (!empty($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reQuery[] = $row['use_case_unique_id'];
        }
    }

    $indexString = implode(',', $reQuery);

    $query = "SELECT * FROM use_case WHERE uc_id IN (" . $indexString . ")";
    $result = mysqli_query($conn, $query);

    $ucIDValue = [];
    $ucNameValue = [];
    $ucDescriptionValue = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ucIDValue[] = $row['uc_id'];
            $ucNameValue[] = $row['use_case_name'];
            $ucDescriptionValue[] = $row['use_case_description'];
        }
    }
}

if (!empty($reQuery)) {
    $indexArrayReQuery[] = $reQuery;
    $reQuery = array_unique($reQuery);
}
if (isset($_POST['submitQuery'])) {
    $numRows = count($indexArray);
} else {
    $numRows = count($reQuery);
}

if (isset($_POST['usecaseFavoritesList'])) {
    $fav_list_id1 = isset($_POST['usecaseFavoritesList']) ? $_POST['usecaseFavoritesList'] : '';
}
if (isset($_POST['addToFavorites'])) {
    if (isset($_POST['ucid'])) {
        foreach ($_POST['ucid'] as $selectedUserID2) {
            $selectedFavID2 = $_POST['usecaseFavoritesList'];
            $selectedFavID2 = intval($selectedFavID2);
            //echo $selectedUserID2.'<br>';
            //echo $selectedFavID1; // this is fav_id
            $sql17 = "INSERT INTO favorites_item (fav_id, uc_id, created_at, created_by) VALUES ($selectedFavID2, $selectedUserID2, '$dateTime', $userid)";
            mysqli_query($conn, $sql17);
            $sql18 = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) VALUES (NOW(), $selectedFavID2, $userid, $userid)";
            mysqli_query($conn, $sql18);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    echo $searchOutput;
    ?>
    <div class="parent-class">
        <div class="left-side" style="background-color: white;">
            <br><br>
            <form class="shaded-box" method="post" action="#">
                <input type="text" name="inputName" id="inputId" placeholder="Create New List..."><br />
                <button type="submit" name="submit1" onclick="handleFormSubmit(event)">Create</button>
            </form>
        </div>

        <?php if (isset($listError)) { ?>
            <p><?php echo $listError; ?></p>
        <?php } ?>

        <?php if (isset($successMessage)) { ?>
            <p><?php echo $successMessage; ?></p>
        <?php } ?>

        <?php if (isset($emptyTextboxError)) { ?>
            <p><?php echo $emptyTextboxError; ?></p>
        <?php } ?>

        <?php if (isset($sqlError)) { ?>
            <p><?php echo $sqlError; ?></p>
        <?php } ?>

        <?php if (isset($noResults)) { ?>
            <strong>No results found.</strong>
        <?php } ?>

        <?php if (isset($error)) { ?>
            <p><?php echo $error; ?></p>
        <?php } ?>

        <?php if (!empty($ucIDValue)) { ?>
            <div class="middle-side"><br>
                <p><strong>Use Case Search Results</strong></p>
                <form name="action" method="POST" id="mainForm">
                    <div class="table-default" style="text-align: left;">
                        <table class="data-table" border="1" style="text-align: left;">
                            <tr>
                                <th>Use Case Name</th>
                                <th>Description</th>
                                <th>
                                    <!--<label for="usecaseFavoritesList"></label><br>-->
                                    <select name="usecaseFavoritesList" id="usecaseFavoritesList">
                                        <?php foreach ($usecaseFavoritesList as $favorite) { ?>
                                            <option value="<?php echo $favorite[1]; ?>"><?php echo $favorite[0]; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div>
                                        <button type="submit" name="addToFavorites" value="addToFavorites">Add To Favorites</button>
                                    </div>
                                </th>
                            </tr>
                            <?php for ($i = 0; $i < count($ucIDValue); $i++) { ?>
                                <tr>
                                    <td><a href="/utilityproject/query_tests/usecase.php?id=<?php echo $ucIDValue[$i]; ?>"><?php echo $ucNameValue[$i]; ?></a></td>
                                    <td><?php echo $ucDescriptionValue[$i]; ?></td>
                                    <td><input type="checkbox" name="ucid[]" value="<?php echo $ucIDValue[$i]; ?>"></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </form>
            </div>
        <?php } ?>
    </div>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputName = isset($_POST['inputName']) ? trim($_POST['inputName']) : '';

    if (!empty($inputName)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?submitted=1");
        exit();
    }
}
