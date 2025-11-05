<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    include_once '../query_tests/includes/header.inc.php';
    include_once '../query_tests/includes/favorites.inc.php';
    ?>
    <?php
    echo '<form action="#" method="post">';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['addUserToList'])) {
            if (!empty($_POST['userFavoritesListUC']) && !empty($_POST['selectList'])) {
                // Process each selected user and list
                foreach ($_POST['userFavoritesListUC'] as $selectedUserID) {
                    // ...existing code...
                    foreach ($_POST['selectList'] as $selectedFavID) {
                        // Safe: only insert join_favorites_user if it does not already exist
                        $checkSql = "SELECT jfu_id FROM join_favorites_user WHERE fav_id = ? AND user_id = ? AND adder_id <=> ?";
                        if ($stmtCheck = $conn->prepare($checkSql)) {
                            $stmtCheck->bind_param("iii", $selectedFavID, $selectedUserID, $userid);
                            $stmtCheck->execute();
                            $stmtCheck->store_result();
                            if ($stmtCheck->num_rows === 0) {
                                // insert new join row
                                $insertSql = "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) VALUES (NOW(), ?, ?, ?)";
                                if ($stmtIns = $conn->prepare($insertSql)) {
                                    $stmtIns->bind_param("iii", $selectedFavID, $selectedUserID, $userid);
                                    $stmtIns->execute();
                                    $jfu_id = $conn->insert_id;
                                    $stmtIns->close();
                                }
                            } else {
                                // already exists — get existing jfu_id
                                $stmtCheck->bind_result($jfu_id);
                                $stmtCheck->fetch();
                            }
                            $stmtCheck->close();
                        } else {
                            // fallback minimal check: try to get any existing id or insert
                            $res = mysqli_query($conn, "SELECT jfu_id FROM join_favorites_user WHERE fav_id = $selectedFavID AND user_id = $selectedUserID AND adder_id <=> $userid LIMIT 1");
                            if ($res && $r = mysqli_fetch_assoc($res)) {
                                $jfu_id = $r['jfu_id'];
                            } else {
                                mysqli_query($conn, "INSERT INTO join_favorites_user (created_at, fav_id, user_id, adder_id) VALUES (NOW(), $selectedFavID, $selectedUserID, $userid)");
                                $jfu_id = $conn->insert_id;
                            }
                        }

                        // Ensure link in j_join_favorites_user_adder (avoid dupes)
                        if (!empty($jfu_id)) {
                            $sqlLink = "INSERT IGNORE INTO j_join_favorites_user_adder (jfu_id, adder_id) VALUES (?, ?)";
                            if ($stmtLink = $conn->prepare($sqlLink)) {
                                $stmtLink->bind_param("ii", $jfu_id, $userid);
                                $stmtLink->execute();
                                $stmtLink->close();
                            }

                            // Insert inbox message (one per share action)
                            $inboxSql = "INSERT INTO inbox (created_at, sender_id, fav_list_id, subject_contents, message_contents, inbox_owner_id, retained, unread) VALUES (NOW(), ?, ?, NULL, NULL, ?, 1, 1)";
                            if ($stmtInbox = $conn->prepare($inboxSql)) {
                                $stmtInbox->bind_param("iii", $userid, $selectedFavID, $selectedUserID);
                                $stmtInbox->execute();
                                $stmtInbox->close();
                            }
                        }
                    }
                    // ...existing code...
                }
            }
            //header("Location: " . $_SERVER['PHP_SELF'] . '#');
            //exit();
        }
        if (isset($_POST['removeUserFromList'])) {
            if (!empty($_POST['userFavoritesListUC']) && !empty($_POST['selectList'])) {
                // Process each selected user and list
                foreach ($_POST['userFavoritesListUC'] as $selectedUserID) {
                    foreach ($_POST['selectList'] as $selectedFavID) {
                        $sql8 = "SELECT jfu_id FROM join_favorites_user WHERE fav_id = $selectedFavID AND user_id = $selectedUserID AND adder_id = $userid";
                        $result = mysqli_query($conn, $sql8);
                        while ($row = mysqli_fetch_assoc($result)) {
                            $jfu_id = $row['jfu_id'];
                            $sql9 = "DELETE FROM j_join_favorites_user_adder WHERE jfu_id = '$jfu_id' AND adder_id = $userid";
                            mysqli_query($conn, $sql9);
                        }
                        $sql7 = "DELETE FROM join_favorites_user WHERE fav_id = $selectedFavID AND user_id = $selectedUserID AND adder_id = $userid";
                        mysqli_query($conn, $sql7);
                    }
                }
            }
            //header("Location: " . $_SERVER['PHP_SELF'] . '#');
            //exit();
        }
    }


    // Query objective
    // Display lists that the user has access to
    // This will require access to the grp or favorites table...figure that out next
    // Two places to look for this info:
    // The visual display of the database
    // The favorites.php file (and related files) to see how those queries were structured
    // favorites_item --> user --> join_user_grp
    // OR
    // join_use_case_user --> user --> join_user_group WHERE grp_id = (group ID(s) associated with user's session ID)
    // OR
    // join_favorites_user --> user --> favorites_item --> use_case
    // This is done
    // Now all you need to do is add in a WHERE statement that selects for the userid in 
    // COMPLETE

    // NEXT STEPS
    // DONE Insert 'Created By' for each list 
    // DONE Insert 'Created On' for each list 
    // Insert 'Shared By' for each list
    // Consider creating a 'Shared On' entry
    // DONE Create 'Add / Remove User' module (visible only to list Admin)
    // DONE Add 'Existing Users' into the tables
    // Create Use Case Counter for each Use Case
    // Make Techstack, Product, and Vendor Plural for each table where there is more than one
    // DONE Make tables even
    // DONE Make background color even
    // DONE Make table backgrounds a different color from the overall background
    // DONE Make List Headers Bigger
    //

    // Existing Users
    $sql = "SELECT join_favorites_user.user_id userID,
join_favorites_user.fav_id listIDUC,
favorites_item.tech_id techID,
favorites.fav_name favName
FROM favorites_item
INNER JOIN favorites
ON favorites_item.fav_id = favorites.fav_id
INNER JOIN join_favorites_user
ON favorites.fav_id = join_favorites_user.fav_id
WHERE join_favorites_user.user_id 
IN (" . $userid . ")
AND favorites_item.uc_id IS NOT NULL
ORDER BY favName";

    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    //var_dump($resultCheck); THIS IS CORRECT

    if ($resultCheck > 0) {
        $listIDUC = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listIDUC[] = $row['listIDUC'] . '<br>';
        }
        $listIDUC = array_unique($listIDUC);
        $listIDUC = array_values($listIDUC);
    }
    //print_r($listIDUC); // THIS IS CORRECT
    //echo '<br>';

    //var_dump($listIDUC);
    //echo $userid;
    if (!empty($listIDUC)) {
        $listIDUCCleaned = array_map(function ($value) {
            return str_replace('<br>', '', $value);
        }, $listIDUC);

        $listIDUCString = implode(',', $listIDUCCleaned);

        $sql = "SELECT favorites.fav_id,
    GROUP_CONCAT(DISTINCT CONCAT(join_favorites_user.user_id, '<br>')
    ORDER BY join_favorites_user.user_id SEPARATOR '') AS existingUsersUC
    FROM favorites_item
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN join_favorites_user
    ON favorites_item.fav_id = join_favorites_user.fav_id
    WHERE favorites.fav_id IN ($listIDUCString)
    GROUP BY favorites.fav_id";

        $result = mysqli_query($conn, $sql);
        $resultCheck10 = mysqli_num_rows($result);
        $techIDsByFavID = array();
        $existingUsersUC = [];

        if ($resultCheck10 > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $existingUsersUC[$row['fav_id']] = $row['existingUsersUC'];
                $techIDsByFavID[] = $row['fav_id'];
            }
        }

        $sql = 'SELECT user.id, user.username FROM user';
        $result4 = mysqli_query($conn, $sql);
        $resultCheck4 = mysqli_num_rows($result4);

        if ($resultCheck4 > 0) {
            while ($row = mysqli_fetch_assoc($result4)) {
                $userID[] = $row['id'];
                $username1[] = $row['username'];
            }
        }

        $existingUsersWithUsernamesUC = [];
        foreach ($existingUsersUC as $favID => $users) {
            $userIds = explode('<br>', $users);
            $usernames = [];
            foreach ($userIds as $userId) {
                $index = array_search($userId, $userID);
                if ($index !== false) {
                    $usernames[$username1[$index]] = $userId;
                }
            }
            $existingUsersWithUsernamesUC[$favID] = $usernames;
        }

        foreach ($existingUsersWithUsernamesUC as $favID => $usernames) {
            if (isset($finalNamesArray[$favID])) {
                $existingUsersString = implode(', ', array_keys($usernames));
                $finalNamesArray[$favID] .= ', ' . $existingUsersString;
            } else {
                $finalNamesArray[$favID] = implode(', ', array_keys($usernames));
            }
        }
    }
    /*echo '<br>';
print_r($finalNamesArray); */

    $sqlMasterQuery = "SELECT use_case.uc_id ucid, use_case_name, techstack_name.tech_id techid, techstack_name, verified, product.prod_id, product_name, com_id, com_name, favorites.fav_id favid, fav_name, created_id, favorites.created_at createdat, favorites.active, favorites.owner_id
FROM join_favorites_user
INNER JOIN favorites
ON join_favorites_user.fav_id = favorites.fav_id
INNER JOIN favorites_item
ON favorites.fav_id = favorites_item.fav_id
INNER JOIN use_case
ON favorites_item.uc_id = use_case.uc_id
INNER JOIN join_use_case_techstack
ON use_case.uc_id = join_use_case_techstack.use_case_id
INNER JOIN techstack_name
ON join_use_case_techstack.techstack_id = techstack_name.tech_id
INNER JOIN techstack_summary
ON techstack_name.tech_id = techstack_summary.tech_id
INNER JOIN product
ON techstack_summary.prod_id = product.prod_id
INNER JOIN join_product_company
ON product.prod_id = join_product_company.product_id
INNER JOIN company
ON join_product_company.company_id = company.com_id
WHERE join_favorites_user.user_id = $userid
AND favorites_item.uc_id IS NOT NULL";
    $sqlMasterQuery = mysqli_query($conn, $sqlMasterQuery);

    $nestedArray = [];
    $uniqueCheck = [];
    $usecaseName = [];
    $favID = [];
    $favName = [];
    $createdIDs = [];
    $createdAts = [];

    // CREATE AN IF STATEMENT THAT TESTS IF THE USER IS SHARED TO THE LIST
    // Where does this come from?

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendToArchive'])) {
        $favIDToArchive = intval($_POST['sendToArchive']);
        $updateQuery = "UPDATE favorites SET active = 0 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToArchive);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error archiving list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reinstate'])) {
        $favIDToReinstate = intval($_POST['reinstate']);
        $updateQuery = "UPDATE favorites SET active = 1 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToReinstate);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error reinstating list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    if (!empty($sqlMasterQuery)) {
        while ($row = mysqli_fetch_assoc($sqlMasterQuery)) {
            $usecaseID = $row['ucid'];
            $usecaseName[$usecaseID] = $row['use_case_name'];
            $techstackID = $row['techid'];
            $techstackName[$techstackID] = $row['techstack_name'];
            $verified[$techstackID] = $row['verified'];
            $productID = $row['prod_id'];
            $productName[$productID] = $row['product_name'];
            $vendorID = $row['com_id'];
            $vendorName[$vendorID] = $row['com_name'];
            $favID = $row['favid'];
            $favName[$favID] = $row['fav_name'];
            $createdID = $row['created_id'];
            $createdIDs[$favID][] = $createdID;
            $createdAt = $row['createdat'];
            $createdAts[$favID][$createdID] = $createdAt;
            $activeList[$favID] = $row['active'];
            $ownerID[$favID] = $row['owner_id'];

            $uniqueKey = "$favID-$usecaseID-$techstackID-$productID-$vendorID";
            if (!isset($uniqueCheck[$uniqueKey])) {
                $uniqueCheck[$uniqueKey] = true;
                if (!isset($nestedArray[$favID])) {
                    $nestedArray[$favID] = [];
                }
                if (!isset($nestedArray[$favID][$usecaseID])) {
                    $nestedArray[$favID][$usecaseID] = [];
                }
                if (!isset($nestedArray[$favID][$usecaseID][$techstackID])) {
                    $nestedArray[$favID][$usecaseID][$techstackID] = [];
                }
                if (!isset($nestedArray[$favID][$usecaseID][$techstackID][$productID])) {
                    $nestedArray[$favID][$usecaseID][$techstackID][$productID] = [];
                }
                $nestedArray[$favID][$usecaseID][$techstackID][$productID][] = $vendorID;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Assuming 'submit_feedback' carries the favID when submitting, and 'clear_feedback' also carries the favID when clearing.
        $favID = isset($_POST['submit_feedback']) ? mysqli_real_escape_string($conn, $_POST['submit_feedback']) : (isset($_POST['clear_feedback']) ? mysqli_real_escape_string($conn, $_POST['clear_feedback']) : '');

        if (isset($_POST['clear_feedback'])) {
            // Clear feedback
            $sql = "DELETE FROM user_selections WHERE fav_id = '$favID' AND user_id = '$userid'";
            if (mysqli_query($conn, $sql)) {
                //echo "Feedback cleared successfully for favID: $favID";
            } else {
                echo "Error clearing feedback: " . mysqli_error($conn);
            }
        }

        if (isset($_POST['submit_feedback'])) {
            // Submit feedback
            $usecases = isset($_POST['save-usecases']) ? $_POST['save-usecases'] : [];
            $techstacks = isset($_POST['save-techstacks']) ? $_POST['save-techstacks'] : [];
            $products = isset($_POST['save-products']) ? $_POST['save-products'] : [];
            $vendors = isset($_POST['save-vendors']) ? $_POST['save-vendors'] : [];

            foreach ($usecases as $usecaseID) {
                $usecaseID = mysqli_real_escape_string($conn, $usecaseID);
                $sql = "INSERT INTO user_selections (created_at, fav_id, item_id, item_type, user_id) VALUES (NOW(), '$favID', '$usecaseID', 1, '$userid')";
                if (!mysqli_query($conn, $sql)) {
                    echo "Error: " . mysqli_error($conn);
                }
            }

            foreach ($techstacks as $techstackID) {
                $techstackID = mysqli_real_escape_string($conn, $techstackID);
                $sql = "INSERT INTO user_selections (created_at, fav_id, item_id, item_type, user_id) VALUES (NOW(), '$favID', '$techstackID', 2, '$userid')";
                if (!mysqli_query($conn, $sql)) {
                    echo "Error: " . mysqli_error($conn);
                }
            }

            foreach ($products as $productID) {
                $productID = mysqli_real_escape_string($conn, $productID);
                $sql = "INSERT INTO user_selections (created_at, fav_id, item_id, item_type, user_id) VALUES (NOW(), '$favID', '$productID', 3, '$userid')";
                if (!mysqli_query($conn, $sql)) {
                    echo "Error: " . mysqli_error($conn);
                }
            }

            foreach ($vendors as $vendorID) {
                $vendorID = mysqli_real_escape_string($conn, $vendorID);
                $sql = "INSERT INTO user_selections (created_at, fav_id, item_id, item_type, user_id) VALUES (NOW(), '$favID', '$vendorID', 4, '$userid')";
                if (!mysqli_query($conn, $sql)) {
                    echo "Error: " . mysqli_error($conn);
                }
            }

            //echo "Feedback submitted successfully for favID: $favID";
        }
    }


    //print_r($productName);
    // Wrap the entire output in a container div to facilitate side-by-side layout
    echo '<div class="parent-class">'; // 1 parent-class - all objects are within this class.
    echo '<div>'; // 2 START - REACTIVATE THIS AT THE END!!!
    echo '<div>'; // 3
    // Main content block container
    echo '<div class="middle-side">'; // 4 all middle objects contained here
    echo '<div class="expand-bar-1">'; // 5
    echo '<div class="rotating-triangle-1" id="triangle-usecase" style="width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 10px solid black; margin-right: 10px; cursor: pointer;">'; // 6
    echo '</div>'; //  5 close rotating-triangle-1
    echo '<strong>Use Case Lists</strong>';
    echo '</div>'; // 4 close expanding-bar-1

    echo '<div class="usecase-content" id="usecase-content" style="display: none;">'; // 5 div with use case...if this is deleted, the expanding bar stops working and a lot of the lists disappear
    /*echo '<br>'; //??//
print_r($nestedArray); */
    foreach ($nestedArray as $favID => $usecases) {
        echo '<div class="usecase-item" data-active="' . $activeList[$favID] . '" data-owner-id="' . $ownerID[$favID] . '">';
        echo '<form method="post" action="#">';
        echo '<div class="middle-side-main-body">'; // 6 blue box
        echo '<div class="middle-side-inset">'; // 7 grey box behind list details and control buttons

        echo '<form method="post" action="#">';
        echo '<table style="width: 100%;">';
        echo '<tr>';
        echo '<td style="text-align: left; padding-right: 10px;">';
        echo '<div style="display: flex; align-items: center;">'; // 8 useless flex THIS SEEMS TO BE DELETABLE!!!
        echo '<h4 style="margin: 0;">' . $favName[$favID];
        // Display [Archived] if the activeList value is 0
        if (isset($activeList[$favID]) && $activeList[$favID] == 0) {
            echo ' <span style="color: red;">[Archived]</span>';
        }
        echo '</h4>';
        // Display the archive or reinstate button based on the activeList value and ownerID
        if (isset($ownerID[$favID]) && $ownerID[$favID] == $userid) {
            if (isset($activeList[$favID])) {
                if ($activeList[$favID] == 1) {
                    echo '<button type="submit" name="sendToArchive" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Send to Archive</button>';
                } elseif ($activeList[$favID] == 0) {
                    echo '<button type="submit" name="reinstate" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Reinstate List</button>';
                }
            }
        }
        echo '</div>'; // 7 end useless flex
        echo '</td>';

        if (isset($activeList[$favID]) && $activeList[$favID] == 1) {
            echo '<td style="text-align: right;">';
            echo '<button type="submit" name="submit_feedback" value="' . htmlspecialchars($favID) . '">Submit Feedback</button>';
            echo '<br>';
            echo '<button type="submit" name="clear_feedback" value="' . htmlspecialchars($favID) . '">Clear Feedback</button>';
            echo '</td>';
        }

        echo '</tr>';
        echo '</table>';
        echo '</form>';

        if (isset($createdIDs[$favID])) {
            $uniqueCreatedIDs = array_unique($createdIDs[$favID]);
            foreach ($uniqueCreatedIDs as $createdID) {
                $sql = "SELECT username FROM user WHERE id = $createdID";
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    // Handle form submission
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
                        // Check if the form is submitted and matches the current favID
                        if (isset($_POST['favID']) && $_POST['favID'] == $favID) {
                            $favInstructions = mysqli_real_escape_string($conn, $_POST['favInstructions']);
                            $favIDToUpdate = mysqli_real_escape_string($conn, $_POST['favID']); // Get the favID from the form

                            // Update the specific row with the given favID
                            $sqlUpdate = "UPDATE favorites SET fav_instructions = '$favInstructions' WHERE owner_id = $userid AND fav_id = $favIDToUpdate";
                            if ($conn->query($sqlUpdate) === FALSE) {
                                echo "Error updating record: " . $conn->error;
                            }
                        }
                    }

                    // Fetch the fav_instructions for the given createdID and favID after potential update
                    $instructionsQuery = "SELECT fav_instructions FROM favorites WHERE created_id = $createdID AND fav_id = $favID";
                    $instructionsResult = mysqli_query($conn, $instructionsQuery);
                    $favInstructionsText = '';
                    if ($instructionsResult && mysqli_num_rows($instructionsResult) > 0) {
                        $row = mysqli_fetch_assoc($instructionsResult);
                        // Check if the fav_instructions value is null or empty
                        if ($row['fav_instructions'] === null || $row['fav_instructions'] === '') {
                            $favInstructionsText = '<i>No instructions provided.</i>';
                        } else {
                            $favInstructionsText = htmlspecialchars($row['fav_instructions']);
                        }
                    } else {
                        $favInstructionsText = '<i>No instructions found.</i>';
                    }

                    // Display the fetched instructions
                    echo '<div style="margin-bottom: 10px;"><strong>Instructions: </strong>' . $favInstructionsText . '</div>'; // Display fetched instructions

                    // Display the form with the placeholder
                    if ($userid == $createdID) {
                        echo '<form method="POST" action="">'; // Action is set to empty string to submit to the same page.
                        echo '<input type="hidden" name="favID" value="' . htmlspecialchars($favID) . '">'; // Hidden input for favID

                        // Textbox for updating instructions with only the placeholder
                        echo '<input type="text" name="favInstructions" id="favInstructions" placeholder="Create / update team instructions..." style="width: 350px;">';
                        echo '<button type="submit" name="update">Update</button>';
                        echo '</form>';
                    }

                    // Display user creation info
                    while ($userRow = mysqli_fetch_assoc($result)) {
                        echo '<div><i>Created By:</i> ' . htmlspecialchars($userRow['username']);
                        if (isset($createdAts[$favID][$createdID])) {
                            $formattedDate = date('F j, Y', strtotime($createdAts[$favID][$createdID]));
                            echo ' <i>On:</i> ' . htmlspecialchars($formattedDate) . '</div>';

                            // Add another div for $finalNamesArray
                            if (isset($finalNamesArray[$favID])) {
                                echo '<div><i>Shared With:</i> ' . htmlspecialchars($finalNamesArray[$favID]) . '</div>';
                            }
                        } else {
                            echo '</div>';
                        }
                    }
                }
            }
        }

        echo '</div>'; // 6 End of grey background div

        //echo '<br>';

        echo '<table>'; //
        echo '<tr>';
        echo '<td id="container1_' . htmlspecialchars($favID) . '" style="vertical-align: top;">';

        foreach ($usecases as $usecaseID => $techstacks) {
            // Add data-usecase-id to the use case container
            //??//
            echo '<div class="table-default" data-usecase-id="' . $usecaseID . '">';  // 7
            echo '<input type="checkbox" name="save-usecases[]" value="' . $usecaseID . '"><a href="usecase.php?id=' . $usecaseID . '">' . $usecaseName[$usecaseID] . '</a>';
            echo '<br>';
            echo '<table border="1" class="data-table1">';
            echo '<tr>';
            echo '<th class="uc-col-techstack">Techstack</th>';
            echo '<th class="uc-col-verified">Status</th>';
            echo '<th class="uc-col-product-heading">Product</th>';
            echo '<th class="uc-col-vendor-heading">Vendor</th>';
            echo '</tr>';
            foreach ($techstacks as $techstackID => $products) {
                echo '<tr>';
                // Add data-techstack-id to the techstack cell
                echo '<td class="col-techstack" data-techstack-id="' . $techstackID . '" rowspan="' . count($products) . '"><input type="checkbox" name="save-techstacks[]" value="' . $techstackID . '"><a href="techstack.php?techstack=' . $techstackID . '">' . $techstackName[$techstackID] . '</a></td>';
                echo '<td class="col-verified" rowspan="' . count($products) . '">';

                if ($verified[$techstackID] == 1) {
                    //echo "<strong>Verified</strong>";
                    echo '<form action="../query_tests/includes/contactutility.inc.php" method="post">';
                    echo '<input type="hidden" name="techstack_id" value="' . $techstackID . '">';
                    echo '<input type="hidden" name="techstack_name" value="' . htmlspecialchars($techstackName[$techstackID]) . '">';
                    echo '<input type="hidden" name="usecase_id" value="' . $usecaseID . '">';
                    echo '<input type="hidden" name="usecase_name" value="' . htmlspecialchars($usecaseName[$usecaseID]) . '">';
                    echo '<input type="hidden" name="fav_id" value="' . $favID . '">';
                    echo '<button type="submit" name="contact_action"><strong>Verified</strong><br>More Info</button>';
                    echo '</form>';
                } else {
                    echo "Unverified";
                }

                echo '</td>';

                foreach ($products as $productID => $vendors) {
                    echo '<td class="col-product" data-product-id="' . $productID . '" data-product-techstack-id="' . $techstackID . '"><input type="checkbox" name="save-products[]" value="' . $productID . '"><a href="product.php?product=' . $productID . '">' . $productName[$productID] . '</a></td>';
                    foreach ($vendors as $vendorID) {
                        echo '<td class="col-vendor" data-vendor-id="' . $vendorID . '"><input type="checkbox" name="save-vendors[]" value="' . $vendorID . '"><a href="entity.php?entity=' . $vendorID . '">' . $vendorName[$vendorID] . '</a></td>';
                        echo '</tr>';
                    }
                }
            }
            echo '</table>';
            //echo '<br>';
            echo '</div>'; // 6 End of white background div for $usecaseName[$usecaseID]
        }

        echo '</td>';
        echo '<td style="max-width: 20px;" id="container1_' . htmlspecialchars($favID) . '" >';
        //echo '<div style="width: 20px;"></div>';
        echo '</td>';

        /////////////////////    /////////////////////    /////////////////////    /////////////////////
        /////////////////////    /////////////////////

        echo '<td id="container_' . htmlspecialchars($favID) . '">';

        $sql = "SELECT fav_id, item_id, item_type, user_id FROM user_selections WHERE user_id IN $userGrpString";
        $result = mysqli_query($conn, $sql);

        $favIDVote = [];
        $itemIDVote = [];
        $itemTypeVote = [];
        $userIDVote = [];

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $favIDVote[] = $row['fav_id'];
                $itemIDVote[] = $row['item_id'];
                $itemTypeVote[] = $row['item_type'];
                $userIDVote[] = $row['user_id'];
            }
        }

        echo '<div class="middle-side-right-table table-default">';
        echo '<table border="1" class="data-table">';
        echo '<tr><th>Item Name</th><th>Total Votes</th>';

        if (isset($finalNamesArray[$favID])) {
            $usernames = explode(", ", $finalNamesArray[$favID]);
            foreach ($usernames as $username) {
                $userID = isset($existingUsersWithUsernamesUC[$favID][$username]) ? $existingUsersWithUsernamesUC[$favID][$username] : '';
                echo '<th>' . htmlspecialchars($username) . ($userID ? '<!-- (' . $userID . ')-->' : '') . '</th>';
            }
        }
        echo '</tr>';

        // Use Cases row
        $colspan = isset($finalNamesArray[$favID]) ? count(explode(", ", $finalNamesArray[$favID])) + 2 : 2;
        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Use Cases</td></tr>';

        $displayedUsecases = []; // Initialize an empty array to track displayed usecases

        foreach ($usecases as $usecaseID => $techstacks) {
            if (!isset($displayedUsecases[$usecaseID])) {
                echo '<tr><td><a href="usecase.php?id=' . $usecaseID . '">' . $usecaseName[$usecaseID] . '</a></td>';
                echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

                foreach ($usernames as $username) {
                    $userID = isset($existingUsersWithUsernamesUC[$favID][$username]) ? $existingUsersWithUsernamesUC[$favID][$username] : '';
                    $voteFound = false;
                    for ($i = 0; $i < count($favIDVote); $i++) {
                        if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $usecaseID && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 1) {
                            echo '<td class="vote">1</td>';
                            $voteFound = true;
                            break;
                        }
                    }
                    if (!$voteFound) {
                        echo '<td class="vote">0</td>';
                    }
                }
                echo '</tr>';
                $displayedUsecases[$usecaseID] = true;
            }
        }

        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Techstacks</td></tr>';
        $displayedTechstacks = []; // Initialize an empty array to track displayed techstacks

        foreach ($usecases as $usecaseID => $techstacks) {
            foreach ($techstacks as $techstackID => $products) {
                if (!isset($displayedTechstacks[$techstackID])) {
                    echo '<tr><td><a href="techstack.php?techstack=' . $techstackID . '">' . $techstackName[$techstackID] . '</a></td>';
                    echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

                    foreach ($usernames as $username) {
                        $userID = isset($existingUsersWithUsernamesUC[$favID][$username]) ? $existingUsersWithUsernamesUC[$favID][$username] : '';
                        $voteFound = false;
                        for ($i = 0; $i < count($favIDVote); $i++) {
                            if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $techstackID && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 2) {
                                echo '<td class="vote">1</td>';
                                $voteFound = true;
                                break;
                            }
                        }
                        if (!$voteFound) {
                            echo '<td class="vote">0</td>';
                        }
                    }
                    echo '</tr>';
                    $displayedTechstacks[$techstackID] = true;
                }
            }
        }

        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Products</td></tr>';
        $displayedProducts = []; // Initialize an empty array to track displayed products

        foreach ($usecases as $usecaseID => $techstacks) {
            foreach ($techstacks as $techstackID => $products) {
                foreach ($products as $productID => $vendors) {
                    if (!isset($displayedProducts[$productID])) {
                        echo '<tr><td><a href="product.php?product=' . $productID . '">' . $productName[$productID] . '</a></td>';
                        echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

                        foreach ($usernames as $username) {
                            $userID = isset($existingUsersWithUsernamesUC[$favID][$username]) ? $existingUsersWithUsernamesUC[$favID][$username] : '';
                            $voteFound = false;
                            for ($i = 0; $i < count($favIDVote); $i++) {
                                if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $productID && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 3) {
                                    echo '<td class="vote">1</td>';
                                    $voteFound = true;
                                    break;
                                }
                            }
                            if (!$voteFound) {
                                echo '<td class="vote">0</td>';
                            }
                        }
                        echo '</tr>';
                        $displayedProducts[$productID] = true;
                    }
                }
            }
        }

        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Vendors</td></tr>';
        $displayedVendors = []; // Initialize an empty array to track displayed vendors

        foreach ($usecases as $usecaseID => $techstacks) {
            foreach ($techstacks as $techstackID => $products) {
                foreach ($products as $productID => $vendors) {
                    foreach ($vendors as $vendorID) {
                        if (!isset($displayedVendors[$vendorID])) {
                            echo '<tr><td><a href="entity.php?entity=' . $vendorID . '">' . $vendorName[$vendorID] . '</a></td>';
                            echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

                            foreach ($usernames as $username) {
                                $userID = isset($existingUsersWithUsernamesUC[$favID][$username]) ? $existingUsersWithUsernamesUC[$favID][$username] : '';
                                $voteFound = false;
                                for ($i = 0; $i < count($favIDVote); $i++) {
                                    if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $vendorID && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 4) {
                                        echo '<td class="vote">1</td>';
                                        $voteFound = true;
                                        break;
                                    }
                                }
                                if (!$voteFound) {
                                    echo '<td class="vote">0</td>';
                                }
                            }
                            echo '</tr>';
                            $displayedVendors[$vendorID] = true;
                        }
                    }
                }
            }
        }

        echo '</table>';
        echo '</div>';

        echo '</td>';
        echo '</tr>';
        echo '</table>';


        echo '</div>'; // 5 End of light blue background div
        echo '</form>';
        echo '</div>';
    }
    echo '</div>'; // 4
    //include_once 'test2.php';
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Initialize arrays to hold various data
    $favNames = [];
    $techstackByFav = [];
    $verifiedByFav = [];
    $productsByTechID = [];
    $componentsByTechID = [];
    $methodsByTechID = [];
    $proName = [];
    $comName = [];
    $insName = [];
    $techstackName = [];
    $verifiedStatusMap = [];

    // Fetch product names
    $sql = "SELECT prod_id, product_name FROM product";
    $productResult = mysqli_query($conn, $sql);
    if ($productResult) {
        while ($row = mysqli_fetch_assoc($productResult)) {
            $proName[$row['prod_id']] = $row['product_name'];
        }
    }

    // Fetch component names
    $sql = "SELECT tech_component_id, component_name FROM techstack_component";
    $componentResult = mysqli_query($conn, $sql);
    if ($componentResult) {
        while ($row = mysqli_fetch_assoc($componentResult)) {
            $comName[$row['tech_component_id']] = $row['component_name'];
        }
    }

    // Fetch instantiation names
    $sql = "SELECT inst_id, instantiation_name FROM instantiation";
    $instResult = mysqli_query($conn, $sql);
    if ($instResult) {
        while ($row = mysqli_fetch_assoc($instResult)) {
            $insName[$row['inst_id']] = $row['instantiation_name'];
        }
    }
    // Existing Users
    $sql = "SELECT join_favorites_user.user_id userID,
join_favorites_user.fav_id listIDTech,
favorites_item.tech_id techID,
favorites.fav_name favName
FROM favorites_item
INNER JOIN favorites
ON favorites_item.fav_id = favorites.fav_id
INNER JOIN join_favorites_user
ON favorites.fav_id = join_favorites_user.fav_id
WHERE join_favorites_user.user_id 
IN (" . $userid . ")
AND favorites_item.tech_id IS NOT NULL
ORDER BY favName";

    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    //var_dump($resultCheck); THIS IS CORRECT

    if ($resultCheck > 0) {
        $listIDTech = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listIDTech[] = $row['listIDTech'] . '<br>';
        }
        $listIDTech = array_unique($listIDTech);
        $listIDTech = array_values($listIDTech);
    }
    //print_r($listIDTech); // THIS IS CORRECT
    echo '<br>';

    //var_dump($listIDTech);
    //echo $userid;
    if (!empty($listIDTech)) {
        $listIDTechCleaned = array_map(function ($value) {
            return str_replace('<br>', '', $value);
        }, $listIDTech);

        $listIDTechString = implode(',', $listIDTechCleaned);

        $sql = "SELECT favorites.fav_id,
    GROUP_CONCAT(DISTINCT CONCAT(join_favorites_user.user_id, '<br>')
    ORDER BY join_favorites_user.user_id SEPARATOR '') AS existingUsersTech
    FROM favorites_item
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN join_favorites_user
    ON favorites_item.fav_id = join_favorites_user.fav_id
    WHERE favorites.fav_id IN ($listIDTechString)
    GROUP BY favorites.fav_id";

        $result = mysqli_query($conn, $sql);
        $resultCheck10 = mysqli_num_rows($result);
        $techIDsByFavID = array();
        $existingUsersTech = [];

        if ($resultCheck10 > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $existingUsersTech[$row['fav_id']] = $row['existingUsersTech'];
                $techIDsByFavID[] = $row['fav_id'];
            }
        }

        $sql = 'SELECT user.id, user.username FROM user';
        $result4 = mysqli_query($conn, $sql);
        $resultCheck4 = mysqli_num_rows($result4);
        $userID = [];
        if ($resultCheck4 > 0) {
            while ($row = mysqli_fetch_assoc($result4)) {
                $userID[] = $row['id'];
                $username1[] = $row['username'];
            }
        }

        $existingUsersWithUsernamesTech = [];
        foreach ($existingUsersTech as $favID => $users) {
            $userIds = explode('<br>', $users);
            $usernames = [];
            foreach ($userIds as $userId) {
                $index = array_search($userId, $userID);
                if ($index !== false) {
                    $usernames[$username1[$index]] = $userId;
                }
            }
            $existingUsersWithUsernamesTech[$favID] = $usernames;
        }

        foreach ($existingUsersWithUsernamesTech as $favID => $usernames) {
            if (isset($finalNamesArrayTech[$favID])) {
                $existingUsersString = implode(', ', array_keys($usernames));
                $finalNamesArrayTech[$favID] .= ', ' . $existingUsersString;
            } else {
                $finalNamesArrayTech[$favID] = implode(', ', array_keys($usernames));
            }
        }
    }


    // Fetch the main data for tech stacks
    $sqlTechstackQuery = "SELECT favorites.fav_id, favorites.fav_name, techstack_summary.tech_id AS techid, 
                         techstack_summary.prod_id AS prodid, method_id, comp_id, techstack_name, verified, created_id, favorites.created_at createdat, favorites.active, favorites.owner_id
                      FROM join_favorites_user
                      INNER JOIN favorites ON join_favorites_user.fav_id = favorites.fav_id
                      INNER JOIN favorites_item ON favorites.fav_id = favorites_item.fav_id
                      INNER JOIN techstack_name ON favorites_item.tech_id = techstack_name.tech_id
                      INNER JOIN techstack_summary ON techstack_name.tech_id = techstack_summary.tech_id
                      INNER JOIN product ON techstack_summary.prod_id = product.prod_id
                      WHERE join_favorites_user.user_id = $userid
                      AND favorites_item.tech_id IS NOT NULL
                      ORDER BY favorites.fav_name, verified, techstack_name, product_name";

    $result = mysqli_query($conn, $sqlTechstackQuery);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendToArchive'])) {
        $favIDToArchive = intval($_POST['sendToArchive']);
        $updateQuery = "UPDATE favorites SET active = 0 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToArchive);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error archiving list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reinstate'])) {
        $favIDToReinstate = intval($_POST['reinstate']);
        $updateQuery = "UPDATE favorites SET active = 1 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToReinstate);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error reinstating list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $favID = $row['fav_id'];
            $techstackID = $row['techid'];
            $prodID = $row['prodid'];
            $instID1 = $row['method_id'];
            $compID = $row['comp_id'];
            $createdID = $row['created_id'];
            $createdIDs[$favID][] = $createdID;
            $createdAt = $row['createdat'];
            $createdAts[$favID][$createdID] = $createdAt;

            $favNames[$favID] = $row['fav_name'];
            $techstackByFav[$favID][] = $row['techstack_name'];
            $techstackName[$techstackID] = $row['techstack_name'];
            $verifiedByFav[$techstackID] = $row['verified'];

            $productsByTechID[$techstackID][] = $prodID;
            $componentsByTechID[$techstackID][] = $compID;
            $methodsByTechID[$techstackID][] = $instID1;
            $activeList[$favID] = $row['active'];
            $ownerID[$favID] = $row['owner_id'];
        }
    }

    // Fetch verified statuses
    $sql = "SELECT verified_id, v_status FROM verified";
    $verifiedResult = mysqli_query($conn, $sql);
    if ($verifiedResult) {
        while ($row = mysqli_fetch_assoc($verifiedResult)) {
            $verifiedStatusMap[$row['verified_id']] = $row['v_status'];
        }
    }

    // Replace verified values with their corresponding statuses
    $verifiedWithStatus = [];
    foreach ($verifiedByFav as $techstackID => $verifiedID) {
        $verifiedWithStatus[$techstackID] = $verifiedStatusMap[$verifiedID] ?? "Unknown";
    }

    echo '<div class="expand-bar-1">'; // 5 
    echo '<div id="triangle-techstack" style="width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 10px solid black; margin-right: 10px; cursor: pointer;">'; // 6 
    echo '</div>'; // 5
    echo '<strong>Techstack Lists</strong>';
    echo '</div>'; // 4

    echo '<div class="techstack-content" id="techstack-content" style="display: none;">'; // 5 

    foreach ($favNames as $favID => $favName) {
        echo '<div class="usecase-item" data-active="' . $activeList[$favID] . '" data-owner-id="' . $ownerID[$favID] . '">';
        echo '<form method="post" action="#">';
        echo '<div class="middle-side-main-body">'; // 6 
        echo '<div class="middle-side-inset">'; // 7

        echo '<form method="post" action="#">'; // specify the action script
        echo '<table style="width: 100%;">';
        echo '<tr>';
        echo '<td style="text-align: left; padding-right: 10px;">';
        echo '<div style="display: flex; align-items: center;">'; // 8 Added flex container THIS SEEMS LIKE IT'S DELETABLE!!!
        echo "<h4 style='margin: 0;'>$favName"; // This is echoing out the list name. 
        if (isset($activeList[$favID]) && $activeList[$favID] == 0) {
            echo ' <span style="color: red;">[Archived]</span>';
        }
        echo '</h4>';
        // Display the archive or reinstate button based on the activeList value and ownerID
        if (isset($ownerID[$favID]) && $ownerID[$favID] == $userid) {
            if (isset($activeList[$favID])) {
                if ($activeList[$favID] == 1) {
                    echo '<button type="submit" name="sendToArchive" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Send to Archive</button>';
                } elseif ($activeList[$favID] == 0) {
                    echo '<button type="submit" name="reinstate" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Reinstate List</button>';
                }
            }
        }
        echo '</div>'; // 7
        echo '</td>';

        if (isset($activeList[$favID]) && $activeList[$favID] == 1) {
            echo '<td style="text-align: right;">';
            echo '<button type="submit" name="submit_feedback" value="' . htmlspecialchars($favID) . '" style="margin-left: auto;">Submit Feedback</button>';  // Use type="submit"
            echo '<br>';
            echo '<button type="submit" name="clear_feedback" value="' . htmlspecialchars($favID) . '" style="margin-left: auto;">Clear Feedback</button>';  // Use type="submit"
            echo '</td>';
        }

        echo '</tr>';
        echo '</table>';
        echo '</form>';

        if (isset($createdIDs[$favID])) {
            $uniqueCreatedIDs = array_unique($createdIDs[$favID]);
            foreach ($uniqueCreatedIDs as $createdID) {
                $sql = "SELECT username FROM user WHERE id = $createdID";
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    // Handle form submission
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
                        // Check if the form is submitted and matches the current favID
                        if (isset($_POST['favID']) && $_POST['favID'] == $favID) {
                            $favInstructions = mysqli_real_escape_string($conn, $_POST['favInstructions']);
                            $favIDToUpdate = mysqli_real_escape_string($conn, $_POST['favID']); // Get the favID from the form

                            // Update the specific row with the given favID
                            $sqlUpdate = "UPDATE favorites SET fav_instructions = '$favInstructions' WHERE owner_id = $userid AND fav_id = $favIDToUpdate";
                            if ($conn->query($sqlUpdate) === FALSE) {
                                echo "Error updating record: " . $conn->error;
                            }
                        }
                    }

                    // Fetch the fav_instructions for the given createdID and favID after potential update
                    $instructionsQuery = "SELECT fav_instructions FROM favorites WHERE created_id = $createdID AND fav_id = $favID";
                    $instructionsResult = mysqli_query($conn, $instructionsQuery);
                    $favInstructionsText = '';
                    if ($instructionsResult && mysqli_num_rows($instructionsResult) > 0) {
                        $row = mysqli_fetch_assoc($instructionsResult);
                        // Check if the fav_instructions value is null or empty
                        if ($row['fav_instructions'] === null || $row['fav_instructions'] === '') {
                            $favInstructionsText = '<i>No instructions provided.</i>';
                        } else {
                            $favInstructionsText = htmlspecialchars($row['fav_instructions']);
                        }
                    } else {
                        $favInstructionsText = '<i>No instructions found.</i>';
                    }

                    // Display the fetched instructions
                    echo '<div style="margin-bottom: 10px;"><strong>Instructions: </strong>' . $favInstructionsText . '</div>'; // Display fetched instructions

                    // Display the form with the placeholder
                    if ($userid == $createdID) {
                        echo '<form method="POST" action="">'; // Action is set to empty string to submit to the same page.
                        echo '<input type="hidden" name="favID" value="' . htmlspecialchars($favID) . '">'; // Hidden input for favID

                        // Textbox for updating instructions with only the placeholder
                        echo '<input type="text" name="favInstructions" id="favInstructions" placeholder="Create / update team instructions..." style="width: 350px;">';
                        echo '<button type="submit" name="update">Update</button>';
                        echo '</form>';
                    }

                    // Display user creation info
                    while ($userRow = mysqli_fetch_assoc($result)) {
                        echo '<div><i>Created By:</i> ' . htmlspecialchars($userRow['username']);
                        if (isset($createdAts[$favID][$createdID])) {
                            $formattedDate = date('F j, Y', strtotime($createdAts[$favID][$createdID]));
                            echo ' <i>On:</i> ' . htmlspecialchars($formattedDate) . '</div>';

                            // Add another div for $finalNamesArrayTech
                            if (isset($finalNamesArrayTech[$favID])) {
                                echo '<div><i>Shared With:</i> ' . htmlspecialchars($finalNamesArrayTech[$favID]) . '</div>';
                            } else {
                                echo '<div><i>Shared With:</i> No associated users</div>';
                            }
                        } else {
                            echo '</div>';
                        }
                    }
                }
            }
        }


        echo '</div>'; // 6

        // Initialize the array to track displayed product names
        $displayedProductNames = [];

        echo '<table>'; //
        echo '<tr>';
        echo '<td id="container1_' . htmlspecialchars($favID) . '" style="vertical-align: top;">';
        echo '<div class="table-default">';
        //??//
        echo '<table class="data-table1" border="1">
        <tr>
            <th class="tech-col-techstack">Techstack</th>
            <th class="tech-col-verified">Status</th>
            <th class="tech-col-product">Product</th>
            <th class="tech-col-component">Component</th>
            <th class="tech-col-method">Method</th>
        </tr>';


        $uniqueTechstackNames = [];
        if (isset($techstackByFav[$favID])) {
            foreach ($techstackByFav[$favID] as $techName) {
                if (!in_array($techName, $uniqueTechstackNames)) {
                    $uniqueTechstackNames[] = $techName;
                    $techstackID = array_search($techName, $techstackName);
                    $verifiedStatus = $verifiedWithStatus[$techstackID] ?? "Unknown";

                    $productNames = array_map(function ($id) use ($proName) {
                        return $proName[$id] ?? "Unknown Product";
                    }, $productsByTechID[$techstackID] ?? []);

                    $componentNames = array_map(function ($id) use ($comName) {
                        return $comName[$id] ?? "Unknown Component";
                    }, $componentsByTechID[$techstackID] ?? []);

                    $methodNames = array_map(function ($id) use ($insName) {
                        return $insName[$id] ?? "N/A";
                    }, $methodsByTechID[$techstackID] ?? []);

                    $maxRows = max(count($productNames), count($componentNames), count($methodNames));

                    // This is the loop that creates rows for each techstack and its products/components/methods.
                    // THIS IS PROBABLY WHERE THE FAULTY LOGIC IS FOR THE DUPLICATE PRODUCTS /!/!/!
                    // ...existing code...
                    // Build deduped render rows for this techstack (keep IDs, render names)
                    $seen = [];
                    $renderRows = [];

                    $prodArr = $productsByTechID[$techstackID] ?? [];
                    $compArr = $componentsByTechID[$techstackID] ?? [];
                    $methArr = $methodsByTechID[$techstackID] ?? [];

                    $maxRows = max(count($prodArr), count($compArr), count($methArr));

                    for ($i = 0; $i < $maxRows; $i++) {
                        $productID = $prodArr[$i] ?? '';
                        // product display name was built earlier as $productNames
                        $productName = $productNames[$i] ?? ($proName[$productID] ?? '');

                        // component and method IDs (original arrays hold IDs)
                        $componentID = $compArr[$i] ?? '';
                        $methodID = $methArr[$i] ?? '';

                        // component/method display names: prefer mapped arrays created earlier, fall back to name lookups
                        $componentName = $componentNames[$i] ?? ($comName[$componentID] ?? '');
                        $methodName = $methodNames[$i] ?? ($insName[$methodID] ?? '');

                        // Skip fully empty rows
                        if ($productID === '' && $componentID === '' && $methodID === '' && $productName === '' && $componentName === '' && $methodName === '') {
                            continue;
                        }

                        // Use a composite key of IDs to dedupe exact duplicate rows.
                        $key = $productID . '|' . $componentID . '|' . $methodID;
                        if (isset($seen[$key])) {
                            continue;
                        }
                        $seen[$key] = true;

                        $renderRows[] = [
                            'productID' => $productID,
                            'productName' => $productName,
                            'componentID' => $componentID,
                            'componentName' => $componentName,
                            'methodID' => $methodID,
                            'methodName' => $methodName
                        ];
                    }

                    // Output rows, printing the techstack / verified cells only on the first row
                    $first = true;
                    $rowCount = count($renderRows) ?: 1; // ensure rowspan at least 1
                    foreach ($renderRows as $r) {
                        $productID = $r['productID'];
                        $productName = $r['productName'];
                        $component = $r['componentName'];
                        $method = $r['methodName'];

                        if ($first) {
                            echo "<tr>
                                <td class='col-techstack' techstack-data-techstack-id='$techstackID' rowspan='$rowCount'>
                                    <input type='checkbox' name='save-techstacks[]' value='$techstackID'>
                                    <a href='/techstack.php?techstack=$techstackID'>" . htmlspecialchars($techName) . "</a>
                                </td>";
                            if ($verifiedStatus == 'Verified') {
                                echo "<td rowspan='$rowCount'>";
                                echo '<form action="../query_tests/includes/contactutility.inc.php" method="post">';
                                echo '<input type="hidden" name="techstack_id" value="' . intval($techstackID) . '">';
                                echo '<input type="hidden" name="techstack_name" value="' . htmlspecialchars($techstackName[$techstackID] ?? '') . '">';
                                echo '<input type="hidden" name="usecase_id" value="' . intval($usecaseID) . '">';
                                echo '<input type="hidden" name="usecase_name" value="' . htmlspecialchars($usecaseName[$usecaseID] ?? '') . '">';
                                echo '<input type="hidden" name="fav_id" value="' . intval($favID) . '">';
                                echo '<button type="submit" name="contact_action"><strong>Verified</strong><br>More Info</button>';
                                echo '</form>';
                                echo "</td>";
                            } else {
                                echo "<td rowspan='$rowCount'>" . htmlspecialchars($verifiedStatus) . "</td>";
                            }

                            echo "<td class='techstack-col-product' techstack-data-product-id='" . htmlspecialchars($productID) . "' techstack-data-product-techstack-id='" . htmlspecialchars($techstackID) . "'>
                                    <input type='checkbox' name='save-products[]' value='" . htmlspecialchars($productID) . "'>
                                    <a href='/product.php?product=" . urlencode($productID) . "'>" . htmlspecialchars($productName) . "</a>
                                </td>
                                <td>" . htmlspecialchars($component) . "</td>
                                <td>" . htmlspecialchars($method) . "</td>
                            </tr>";
                            $first = false;
                        } else {
                            echo "<tr>
                                <td class='techstack-col-product' techstack-data-product-id='" . htmlspecialchars($productID) . "' techstack-data-product-techstack-id='" . htmlspecialchars($techstackID) . "'>
                                    <input type='checkbox' name='save-products[]' value='" . htmlspecialchars($productID) . "'>
                                    <a href='/product.php?product=" . urlencode($productID) . "'>" . htmlspecialchars($productName) . "</a>
                                </td>
                                <td>" . htmlspecialchars($component) . "</td>
                                <td>" . htmlspecialchars($method) . "</td>
                            </tr>";
                        }
                    }
                    // ...existing code...
                }
            }
        }
        echo '</table>';
        //echo '<br>';
        echo '</div>';
        echo '</td>';
        echo '<td style="max-width: 20px;" id="container1_' . htmlspecialchars($favID) . '" >';
        //echo '<div style="width: 20px;"></div>';
        echo '</td>';

        echo '<td id="container_' . htmlspecialchars($favID) . '">';
        echo '<div class="middle-side-right-table table-default">';
        echo '<table border="1" class="data-table">';
        echo '<tr><th>Item Name</th><th>Total Votes</th>';

        if (isset($finalNamesArrayTech[$favID])) {
            $usernames = explode(", ", $finalNamesArrayTech[$favID]);
            foreach ($usernames as $username) {
                $userID = isset($existingUsersWithUsernamesTech[$favID][$username]) ? $existingUsersWithUsernamesTech[$favID][$username] : '';
                echo '<th>' . htmlspecialchars($username) . ($userID ? '<!-- (' . $userID . ')-->' : '') . '</th>';
            }
        }
        echo '</tr>';

        // Techstacks row
        $colspan = isset($finalNamesArrayTech[$favID]) ? count(explode(", ", $finalNamesArrayTech[$favID])) + 2 : 2;
        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Techstacks</td></tr>';

        $displayedTechstacks = []; // Initialize an empty array to track displayed techstacks

        foreach ($uniqueTechstackNames as $techName) {
            $techstackID = array_search($techName, $techstackName);
            if (!isset($displayedTechstacks[$techstackID])) {
                $techstackURL = '/techstack.php?techstack=' . urlencode($techstackID);
                echo "<tr><td><a href='" . htmlspecialchars($techstackURL) . "'>" . htmlspecialchars($techName) . '</a></td>';
                echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

                foreach ($usernames as $username) {
                    $userID = isset($existingUsersWithUsernamesTech[$favID][$username]) ? $existingUsersWithUsernamesTech[$favID][$username] : '';
                    $voteFound = false;
                    for ($i = 0; $i < count($favIDVote); $i++) {
                        if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $techstackID && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 2) {
                            echo '<td class="vote">1</td>';
                            $voteFound = true;
                            break;
                        }
                    }
                    if (!$voteFound) {
                        echo '<td class="vote">0</td>';
                    }
                }
                echo '</tr>';
                $displayedTechstacks[$techstackID] = true; // Mark this techstackID as displayed
            }
        }

        // Additional row for Products
        $colspan = isset($finalNamesArrayTech[$favID]) ? count(explode(", ", $finalNamesArrayTech[$favID])) + 2 : 2;
        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Products</td></tr>';

        $displayedProductNames = []; // Initialize an empty array to track displayed products

        foreach ($uniqueTechstackNames as $techName) {
            $techstackID = array_search($techName, $techstackName);
            $productIDs = $productsByTechID[$techstackID] ?? [];
            $productNames = array_map(function ($id) use ($proName) {
                return $proName[$id] ?? "Unknown Product";
            }, $productIDs);

            foreach ($productNames as $index => $productName) {
                $productID = $productIDs[$index];  // Get the corresponding product ID
                if (!in_array($productName, $displayedProductNames)) {
                    echo '<tr><td><a href="/product.php?product=' . urlencode($productID) . '">' . htmlspecialchars($productName) . '</a></td>';
                    echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

                    foreach ($usernames as $username) {
                        $userID = isset($existingUsersWithUsernamesTech[$favID][$username]) ? $existingUsersWithUsernamesTech[$favID][$username] : '';
                        $voteFound = false;

                        for ($i = 0; $i < count($favIDVote); $i++) {
                            if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $productID && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 3) {
                                echo '<td class="vote">1</td>';
                                $voteFound = true;
                                break;
                            }
                        }
                        if (!$voteFound) {
                            echo '<td class="vote">0</td>';
                        }
                    }
                    echo '</tr>';
                    $displayedProductNames[] = $productName;  // Mark this product as displayed
                }
            }
        }

        echo '</table>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        echo '</div>';  // 5 Closing the outer div for each favorite
        echo '</form>';
        echo '</div>';
    }
    echo '</div>'; // 4

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Existing Users
    $sql = "SELECT join_favorites_user.user_id userID,
join_favorites_user.fav_id listIDProduct,
favorites_item.tech_id techID,
favorites.fav_name favName
FROM favorites_item
INNER JOIN favorites
ON favorites_item.fav_id = favorites.fav_id
INNER JOIN join_favorites_user
ON favorites.fav_id = join_favorites_user.fav_id
WHERE join_favorites_user.user_id 
IN (" . $userid . ")
AND favorites_item.product_id IS NOT NULL
ORDER BY favName";

    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    //var_dump($resultCheck); THIS IS CORRECT

    if ($resultCheck > 0) {
        $listIDProduct = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listIDProduct[] = $row['listIDProduct'] . '<br>';
        }
        $listIDProduct = array_unique($listIDProduct);
        $listIDProduct = array_values($listIDProduct);
    }
    //print_r($listIDProduct); // THIS IS CORRECT
    echo '<br>';

    //var_dump($listIDProduct);
    //echo $userid;
    if (!empty($listIDProduct)) {
        $listIDProductCleaned = array_map(function ($value) {
            return str_replace('<br>', '', $value);
        }, $listIDProduct);

        $listIDProductString = implode(',', $listIDProductCleaned);

        $sql = "SELECT favorites.fav_id,
    GROUP_CONCAT(DISTINCT CONCAT(join_favorites_user.user_id, '<br>')
    ORDER BY join_favorites_user.user_id SEPARATOR '') AS existingUsersProduct
    FROM favorites_item
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN join_favorites_user
    ON favorites_item.fav_id = join_favorites_user.fav_id
    WHERE favorites.fav_id IN ($listIDProductString)
    GROUP BY favorites.fav_id";

        $result = mysqli_query($conn, $sql);
        $resultCheck10 = mysqli_num_rows($result);
        $techIDsByFavID = array();
        $existingUsersProduct = [];

        if ($resultCheck10 > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $existingUsersProduct[$row['fav_id']] = $row['existingUsersProduct'];
                $techIDsByFavID[] = $row['fav_id'];
            }
        }

        $sql = 'SELECT user.id, user.username FROM user';
        $result4 = mysqli_query($conn, $sql);
        $resultCheck4 = mysqli_num_rows($result4);
        $userID = [];
        $username1 = [];
        if ($resultCheck4 > 0) {
            while ($row = mysqli_fetch_assoc($result4)) {
                $userID[] = $row['id'];
                $username1[] = $row['username'];
            }
        }

        $existingUsersWithUsernamesProduct = [];
        foreach ($existingUsersProduct as $favID => $users) {
            $userIds = explode('<br>', $users);
            $usernames = [];
            foreach ($userIds as $userId) {
                $index = array_search($userId, $userID);
                if ($index !== false) {
                    $usernames[$username1[$index]] = $userId;
                }
            }
            $existingUsersWithUsernamesProduct[$favID] = $usernames;
        }

        foreach ($existingUsersWithUsernamesProduct as $favID => $usernames) {
            if (isset($finalNamesArrayProduct[$favID])) {
                $existingUsersString = implode(', ', array_keys($usernames));
                $finalNamesArrayProduct[$favID] .= ', ' . $existingUsersString;
            } else {
                $finalNamesArrayProduct[$favID] = implode(', ', array_keys($usernames));
            }
        }
    }


    $sqlProductQuery = "SELECT DISTINCT favorites.fav_id favid, favorites.fav_name favname, product.prod_id prodid, product_name, product_description, com_id, com_name, created_id, favorites.created_at createdat, favorites.active, favorites.owner_id
                    FROM join_favorites_user
                    INNER JOIN favorites ON join_favorites_user.fav_id = favorites.fav_id
                    INNER JOIN favorites_item ON favorites.fav_id = favorites_item.fav_id
                    INNER JOIN product ON favorites_item.product_id = product.prod_id
                    INNER JOIN join_product_company ON product.prod_id = join_product_company.product_id
                    INNER JOIN company ON join_product_company.company_id = company.com_id
                    WHERE join_favorites_user.user_id = $userid
                    AND favorites_item.product_id IS NOT NULL
                    ORDER BY favorites.fav_name, product_name";

    $productQResult = mysqli_query($conn, $sqlProductQuery);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendToArchive'])) {
        $favIDToArchive = intval($_POST['sendToArchive']);
        $updateQuery = "UPDATE favorites SET active = 0 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToArchive);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error archiving list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reinstate'])) {
        $favIDToReinstate = intval($_POST['reinstate']);
        $updateQuery = "UPDATE favorites SET active = 1 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToReinstate);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error reinstating list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    $dataByFavID = [];
    $createdIDs = [];
    $createdAts = [];
    $favNames = [];
    $productsByFav = [];
    $productName = [];
    $productDescriptions = [];
    $vendorsByProdID = [];
    $vendorNames = [];
    $activeList = [];
    $ownerID = [];

    if ($productQResult) {
        while ($row = mysqli_fetch_assoc($productQResult)) {
            $favID = $row['favid'];
            $prodID = $row['prodid'];
            $compID = $row['com_id'];
            $createdID = $row['created_id'];
            $createdAt = $row['createdat'];

            $createdIDs[$favID][] = $createdID;
            $createdAts[$favID][$createdID] = $createdAt;

            $favNames[$favID] = $row['favname'];
            $productsByFav[$favID][] = $row['product_name'];
            $productName[$prodID] = $row['product_name'];
            $productDescriptions[$prodID] = $row['product_description'];
            $vendorsByProdID[$prodID][] = $compID;
            $vendorNames[$compID] = $row['com_name'];
            $activeList[$favID] = $row['active'];
            $ownerID[$favID] = $row['owner_id'];

            $dataByFavID[$favID][] = [
                'prodid' => $prodID,
                'product_name' => $row['product_name'],
                'product_description' => $row['product_description'],
                'com_id' => $compID,
                'com_name' => $row['com_name'],
                'createdid' => $createdID,
                'createdat' => $createdAt,
                'favname' => $row['favname']
            ];
        }
    }

    echo '<div class="expand-bar-1">'; // 5 
    echo '<div id="triangle-product" style="width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 10px solid black; margin-right: 10px; cursor: pointer;">'; // 6
    echo '</div>'; // 5 
    echo '<strong>Product Lists</strong>';
    echo '</div>'; // 4

    echo '<div id="product-content" style="display: none;">'; // 5

    foreach ($dataByFavID as $favID => $productData) {
        echo '<div class="usecase-item" data-active="' . $activeList[$favID] . '" data-owner-id="' . $ownerID[$favID] . '">';
        echo '<form method="post" action="#">';
        echo '<div class="middle-side-main-body">'; // 6
        echo '<div class="middle-side-inset">'; // 7
        echo '<table style="width: 100%;">';
        echo '<tr>';
        echo '<td style="text-align: left; padding-right: 10px;">';
        echo '<div style="display: flex; align-items: center;">'; // 8 Added flex container THIS SEEMS DELETABLE!!!
        echo "<h4 style='margin: 0;'>{$productData[0]['favname']}";
        if (isset($activeList[$favID]) && $activeList[$favID] == 0) {
            echo ' <span style="color: red;">[Archived]</span>';
        }
        echo '</h4>';
        // Display the archive or reinstate button based on the activeList value and ownerID
        if (isset($ownerID[$favID]) && $ownerID[$favID] == $userid) {
            if (isset($activeList[$favID])) {
                if ($activeList[$favID] == 1) {
                    echo '<button type="submit" name="sendToArchive" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Send to Archive</button>';
                } elseif ($activeList[$favID] == 0) {
                    echo '<button type="submit" name="reinstate" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Reinstate List</button>';
                }
            }
        }
        echo '</div>'; // 7
        echo '</td>';

        if (isset($activeList[$favID]) && $activeList[$favID] == 1) {
            echo '<td style="text-align: right;">';
            echo '<button type="submit" name="submit_feedback" value="' . htmlspecialchars($favID) . '" style="margin-left: auto;">Submit Feedback</button>';  // Set this button to submit the form
            echo '<br>';
            echo '<button type="submit" name="clear_feedback" value="' . htmlspecialchars($favID) . '" style="margin-left: auto;">Clear Feedback</button>';  // Change from type="reset" to "submit" and ensure it's part of the form submission
            echo '</td>';
        }

        echo '</tr>';
        echo '</table>';
        echo '</form>';

        if (isset($createdIDs[$favID])) {
            $uniqueCreatedIDs = array_unique($createdIDs[$favID]);
            foreach ($uniqueCreatedIDs as $createdID) {
                $sql = "SELECT username FROM user WHERE id = " . $createdID;
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    // Initialize the instructions text
                    $favInstructionsText = '';

                    // Handle form submission
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
                        if (isset($_POST['favID']) && $_POST['favID'] == $favID) {
                            $favInstructions = mysqli_real_escape_string($conn, $_POST['favInstructions']);
                            $favIDToUpdate = mysqli_real_escape_string($conn, $_POST['favID']);
                            $sqlUpdate = "UPDATE favorites SET fav_instructions = '$favInstructions' WHERE owner_id = $userid AND fav_id = $favIDToUpdate";
                            if ($conn->query($sqlUpdate) === FALSE) {
                                echo "Error updating record: " . $conn->error;
                            }
                        }
                    }

                    // Fetch the fav_instructions for the given createdID and favID after potential update
                    $instructionsQuery = "SELECT fav_instructions FROM favorites WHERE created_id = $createdID AND fav_id = $favID";
                    $instructionsResult = mysqli_query($conn, $instructionsQuery);
                    if ($instructionsResult && mysqli_num_rows($instructionsResult) > 0) {
                        $row = mysqli_fetch_assoc($instructionsResult);
                        $favInstructionsText = $row['fav_instructions'] === null || $row['fav_instructions'] === ''
                            ? '<i>No instructions provided.</i>'
                            : htmlspecialchars($row['fav_instructions']);
                    } else {
                        $favInstructionsText = '<i>No instructions found.</i>';
                    }

                    echo '<div style="margin-bottom: 10px;"><strong>Instructions: </strong>' . $favInstructionsText . '</div>'; // Display fetched instructions

                    if ($userid == $createdID) {
                        echo '<form method="POST" action="">'; // Action is set to empty string to submit to the same page.
                        echo '<input type="hidden" name="favID" value="' . htmlspecialchars($favID) . '">'; // Hidden input for favID

                        // Textbox for updating instructions with only the placeholder
                        echo '<input type="text" name="favInstructions" id="favInstructions" placeholder="Create / update team instructions..." style="width: 350px;">';
                        echo '<button type="submit" name="update">Update</button>';
                        echo '</form>';
                    }

                    while ($userRow = mysqli_fetch_assoc($result)) {
                        echo '<div><i>Created By:</i> ' . htmlspecialchars($userRow['username']);
                        if (isset($createdAts[$favID][$createdID])) {
                            $formattedDate = date('F j, Y', strtotime($createdAts[$favID][$createdID]));
                            echo ' <i>On:</i> ' . htmlspecialchars($formattedDate) . '</div>'; // Display the creation date

                            if (isset($finalNamesArrayProduct[$favID])) {
                                echo '<div><i>Shared With:</i> ' . htmlspecialchars($finalNamesArrayProduct[$favID]) . '</div>'; // Display shared information
                            } else {
                                echo '<div>No entry in finalNamesArrayProduct for favID: ' . htmlspecialchars($favID) . '</div>'; // Handle missing entries in finalNamesArrayProduct
                            }
                        } else {
                            echo '</div>'; // Close div if no creation date is found
                        }
                    }
                }
            }
        }

        echo '</div>'; // 6

        echo '<table>'; //
        echo '<tr>';
        echo '<td id="container1_' . htmlspecialchars($favID) . '" style="vertical-align: top;">';
        echo '<div class="table-default">';
        echo '<table border="1" class="data-table1">
            <tr>
                <th class="prod-col-techstack">Product</th>
                <th class="prod-col-desc">Description</th>
                <th class="prod-col-vendor">Vendor</th>
            </tr>';

        $uniqueProductNames = []; // To track unique product names
        $uniqueVendorNames = []; // To track unique vendor names

        foreach ($productData as $product) {
            $productID = $product['prodid'];
            $productName = $product['product_name'];
            $productDescription = $product['product_description'];
            $vendorID = $product['com_id'];
            $vendorName = $product['com_name'];

            echo "<tr>
            <td class='col-product' data-product-id='$productID'><input type='checkbox' name='save-products[]' value='$productID'><a href='/product.php?product=$productID'>$productName</a></td>
            <td>$productDescription</td>
            <td class='col-vendor' data-vendor-id='$vendorID'><input type='checkbox' name='save-vendors[]' value='$vendorID'><a href='/entity.php?entity=$vendorID'>$vendorName</a></td>
        </tr>";

            // Collect unique product names
            if (!in_array($productName, array_column($uniqueProductNames, 'name'))) {
                $uniqueProductNames[] = ['id' => $productID, 'name' => $productName];
            }

            // Collect unique vendor names
            if (!in_array($vendorName, array_column($uniqueVendorNames, 'name'))) {
                $uniqueVendorNames[] = ['id' => $vendorID, 'name' => $vendorName];
            }
        }

        echo "</table>";
        //echo '<br>';
        echo '</div>';
        echo '</td>';
        echo '<td style="max-width: 20px;" id="container1_' . htmlspecialchars($favID) . '" >';
        //echo '<div style="width: 20px;"></div>';
        echo '</td>';
        // New table to the right of each existing $favID //pp
        echo '<td id="container_' . htmlspecialchars($favID) . '">';
        echo '<div class="middle-side-right-table table-default">';
        echo '<table border="1" class="data-table">';
        echo '<tr><th>Item Name</th><th>Total Votes</th>';

        // print_r($finalNamesArrayProduct);

        if (isset($finalNamesArrayProduct[$favID])) {
            $usernames = explode(", ", $finalNamesArrayProduct[$favID]);
            foreach ($usernames as $username) {
                $userID = isset($existingUsersWithUsernamesProduct[$favID][$username]) ? $existingUsersWithUsernamesProduct[$favID][$username] : '';
                echo '<th>' . htmlspecialchars($username) . ($userID ? '<!-- (' . $userID . ')-->' : '') . '</th>';
            }
        }
        echo '</tr>';

        // Products row
        $colspan = isset($finalNamesArrayProduct[$favID]) ? count(explode(", ", $finalNamesArrayProduct[$favID])) + 2 : 2;
        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Products</td></tr>';

        foreach ($uniqueProductNames as $product) {
            echo '<tr>';
            echo '<td><a href="/product.php?product=' . urlencode($product['id']) . '">' . htmlspecialchars($product['name']) . '</a></td>';
            echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

            if (isset($finalNamesArrayProduct[$favID])) {
                $usernames = explode(", ", $finalNamesArrayProduct[$favID]);
                foreach ($usernames as $username) {
                    $userID = isset($existingUsersWithUsernamesProduct[$favID][$username]) ? $existingUsersWithUsernamesProduct[$favID][$username] : '';
                    $voteFound = false;

                    for ($i = 0; $i < count($favIDVote); $i++) {
                        if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $product['id'] && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 3) {
                            echo '<td class="vote">1</td>';
                            $voteFound = true;
                            break;
                        }
                    }
                    if (!$voteFound) {
                        echo '<td class="vote">0</td>';
                    }
                }
            }
            echo '</tr>';
        }

        // Vendors row
        $colspan = isset($finalNamesArrayProduct[$favID]) ? count(explode(", ", $finalNamesArrayProduct[$favID])) + 2 : 2;
        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Vendors</td></tr>';

        foreach ($uniqueVendorNames as $vendor) {
            echo '<tr>';
            echo '<td><a href="/entity.php?entity=' . urlencode($vendor['id']) . '">' . htmlspecialchars($vendor['name']) . '</a></td>';
            echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

            if (isset($finalNamesArrayProduct[$favID])) {
                $usernames = explode(", ", $finalNamesArrayProduct[$favID]);
                foreach ($usernames as $username) {
                    $userID = isset($existingUsersWithUsernamesProduct[$favID][$username]) ? $existingUsersWithUsernamesProduct[$favID][$username] : '';
                    $voteFound = false;

                    for ($i = 0; $i < count($favIDVote); $i++) {
                        if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $vendor['id'] && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 4) {
                            echo '<td class="vote">1</td>';
                            $voteFound = true;
                            break;
                        }
                    }
                    if (!$voteFound) {
                        echo '<td class="vote">0</td>';
                    }
                }
            }
            echo '</tr>';
        }

        echo '</table>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        echo '</div>'; // 5 Closing the outer div here
        echo '</div>';
    }
    echo '</div>'; // 4

    //var_dump($productData);

    //???////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Existing Users
    $sql = "SELECT join_favorites_user.user_id userID,
join_favorites_user.fav_id listIDVendor,
favorites_item.tech_id techID,
favorites.fav_name favName
FROM favorites_item
INNER JOIN favorites
ON favorites_item.fav_id = favorites.fav_id
INNER JOIN join_favorites_user
ON favorites.fav_id = join_favorites_user.fav_id
WHERE join_favorites_user.user_id 
IN (" . $userid . ")
AND favorites_item.vendor_id IS NOT NULL
ORDER BY favName";

    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    //var_dump($resultCheck); THIS IS CORRECT

    if ($resultCheck > 0) {
        $listIDVendor = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $listIDVendor[] = $row['listIDVendor'] . '<br>';
        }
        $listIDVendor = array_unique($listIDVendor);
        $listIDVendor = array_values($listIDVendor);
    }
    //print_r($listIDVendor); // THIS IS CORRECT
    echo '<br>';

    //var_dump($listIDVendor);
    //echo $userid;
    if (!empty($listIDVendor)) {
        $listIDVendorCleaned = array_map(function ($value) {
            return str_replace('<br>', '', $value);
        }, $listIDVendor);

        $listIDVendorString = implode(',', $listIDVendorCleaned);

        $sql = "SELECT favorites.fav_id,
    GROUP_CONCAT(DISTINCT CONCAT(join_favorites_user.user_id, '<br>')
    ORDER BY join_favorites_user.user_id SEPARATOR '') AS existingUsersVendor
    FROM favorites_item
    INNER JOIN favorites
    ON favorites_item.fav_id = favorites.fav_id
    INNER JOIN join_favorites_user
    ON favorites_item.fav_id = join_favorites_user.fav_id
    WHERE favorites.fav_id IN ($listIDVendorString)
    GROUP BY favorites.fav_id";

        $result = mysqli_query($conn, $sql);
        $resultCheck10 = mysqli_num_rows($result);
        $techIDsByFavID = array();
        $existingUsersVendor = [];

        if ($resultCheck10 > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $existingUsersVendor[$row['fav_id']] = $row['existingUsersVendor'];
                $techIDsByFavID[] = $row['fav_id'];
            }
        }

        $sql = 'SELECT user.id, user.username FROM user';
        $result4 = mysqli_query($conn, $sql);
        $resultCheck4 = mysqli_num_rows($result4);
        $userID = [];
        $username1 = [];
        if ($resultCheck4 > 0) {
            while ($row = mysqli_fetch_assoc($result4)) {
                $userID[] = $row['id'];
                $username1[] = $row['username'];
            }
        }

        $existingUsersWithUsernamesVendor = [];
        foreach ($existingUsersVendor as $favID => $users) {
            $userIds = explode('<br>', $users);
            $usernames = [];
            foreach ($userIds as $userId) {
                $index = array_search($userId, $userID);
                if ($index !== false) {
                    $usernames[$username1[$index]] = $userId;
                }
            }
            $existingUsersWithUsernamesVendor[$favID] = $usernames;
        }

        foreach ($existingUsersWithUsernamesVendor as $favID => $usernames) {
            if (isset($finalNamesArrayVendor[$favID])) {
                $existingUsersString = implode(', ', array_keys($usernames));
                $finalNamesArrayVendor[$favID] .= ', ' . $existingUsersString;
            } else {
                $finalNamesArrayVendor[$favID] = implode(', ', array_keys($usernames));
            }
        }
    }

    $sqlVendorQuery = "SELECT DISTINCT favorites.fav_id favid, favorites.fav_name favname, company.com_id, company.com_name, company.com_description, company.com_email, company.com_phone, company.com_website, created_id, favorites.created_at createdat, product.prod_id, favorites.active, favorites.owner_id
                   FROM join_favorites_user
                   INNER JOIN favorites ON join_favorites_user.fav_id = favorites.fav_id
                   INNER JOIN favorites_item ON favorites.fav_id = favorites_item.fav_id
                   INNER JOIN company ON favorites_item.vendor_id = company.com_id
                   INNER JOIN join_product_company ON company.com_id = join_product_company.company_id
                   INNER JOIN product ON join_product_company.product_id = product.prod_id
                   WHERE join_favorites_user.user_id = $userid
                   AND favorites_item.vendor_id IS NOT NULL
                   ORDER BY favorites.fav_name, company.com_name";

    $vendorQResult = mysqli_query($conn, $sqlVendorQuery);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sendToArchive'])) {
        $favIDToArchive = intval($_POST['sendToArchive']);
        $updateQuery = "UPDATE favorites SET active = 0 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToArchive);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error archiving list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reinstate'])) {
        $favIDToReinstate = intval($_POST['reinstate']);
        $updateQuery = "UPDATE favorites SET active = 1 WHERE fav_id = ?";
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param("i", $favIDToReinstate);
            if ($stmt->execute()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Error reinstating list: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    $dataByFavID = [];
    $createdIDs = [];
    $createdAts = [];
    $favNames = [];
    $vendorsByFav = [];
    $vendorNames = [];
    $vendorProduct = [];
    $activeList = [];
    $ownerID = [];

    if ($vendorQResult) {
        while ($row = mysqli_fetch_assoc($vendorQResult)) {
            $favID = $row['favid'];
            $compID = $row['com_id'];
            $createdID = $row['created_id'];
            $createdAt = $row['createdat'];
            $vendorProd = $row['prod_id'];

            $createdIDs[$favID][] = $createdID;
            $createdAts[$favID][$createdID] = $createdAt;

            $favNames[$favID] = $row['favname'];
            $vendorsByFav[$favID][] = $row['com_name'];
            $vendorNames[$compID] = $row['com_name'];
            $vendorProduct[$vendorProd] = $row['prod_id'];
            $activeList[$favID] = $row['active'];
            $ownerID[$favID] = $row['owner_id'];

            // Build $vendorData array with key to prevent duplicates
            $dataByFavID[$favID][$compID] = [
                'favname' => $row['favname'],
                'com_id' => $compID,
                'com_name' => $row['com_name'],
                'com_description' => $row['com_description'],
                'com_email' => $row['com_email'],
                'com_phone' => $row['com_phone'],
                'com_website' => $row['com_website'],
                'createdid' => $createdID,
                'createdat' => $createdAt
            ];
        }
    }

    // Now $dataByFavID is ready for output with unique $vendorData entries

    echo '<div class="expand-bar-1">'; // 5
    echo '<div id="triangle-vendor" style="width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 10px solid black; margin-right: 10px; cursor: pointer;">'; // 6
    echo '</div>'; // 5
    echo '<strong>Vendor Lists</strong>';
    echo '</div>'; // 4

    echo '<div id="vendor-content" style="display: none;">'; // 5

    foreach ($dataByFavID as $favID => $vendorData) {
        echo '<div class="usecase-item" data-active="' . $activeList[$favID] . '" data-owner-id="' . $ownerID[$favID] . '">';
        echo '<form method="post" action="#">';
        echo '<div class="middle-side-main-body">'; // 6
        echo '<div class="middle-side-inset">'; // 7
        echo '<table style="width: 100%;">';
        echo '<tr>';
        echo '<td style="text-align: left; padding-right: 10px;">';
        echo '<div style="display: flex; align-items: center;">'; // 8 Added flex container
        echo "<h4 style='margin: 0;'>{$vendorData[array_key_first($vendorData)]['favname']}";
        if (isset($activeList[$favID]) && $activeList[$favID] == 0) {
            echo ' <span style="color: red;">[Archived]</span>';
        }
        echo '</h4>';
        // Display the archive or reinstate button based on the activeList value and ownerID
        if (isset($ownerID[$favID]) && $ownerID[$favID] == $userid) {
            if (isset($activeList[$favID])) {
                if ($activeList[$favID] == 1) {
                    echo '<button type="submit" name="sendToArchive" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Send to Archive</button>';
                } elseif ($activeList[$favID] == 0) {
                    echo '<button type="submit" name="reinstate" value="' . htmlspecialchars($favID) . '" style="margin-left: 10px;">Reinstate List</button>';
                }
            }
        }
        echo '</div>'; // 7
        echo '</td>';

        if (isset($activeList[$favID]) && $activeList[$favID] == 1) {
            echo '<td style="text-align: right;">';
            echo '<button type="submit" name="submit_feedback" value="' . htmlspecialchars($favID) . '" style="margin-left: auto;">Submit Feedback</button>'; // Set this button to submit the form
            echo '<br>';
            echo '<button type="submit" name="clear_feedback" value="' . htmlspecialchars($favID) . '" style="margin-left: auto;">Clear Feedback</button>'; // Change from type="reset" to "submit" and ensure it's part of the form submission
            echo '</td>';
        }

        echo '</tr>';
        echo '</table>';
        echo '</form>';

        if (isset($createdIDs[$favID])) {
            $uniqueCreatedIDs = array_unique($createdIDs[$favID]);
            foreach ($uniqueCreatedIDs as $createdID) {
                $sql = "SELECT username FROM user WHERE id = " . $createdID;
                $result = mysqli_query($conn, $sql);
                if ($result && mysqli_num_rows($result) > 0) {
                    // Initialize the instructions text
                    $favInstructionsText = '';

                    // Handle form submission
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
                        if (isset($_POST['favID']) && $_POST['favID'] == $favID) {
                            $favInstructions = mysqli_real_escape_string($conn, $_POST['favInstructions']);
                            $favIDToUpdate = mysqli_real_escape_string($conn, $_POST['favID']);
                            $sqlUpdate = "UPDATE favorites SET fav_instructions = '$favInstructions' WHERE owner_id = $userid AND fav_id = $favIDToUpdate";
                            if ($conn->query($sqlUpdate) === FALSE) {
                                echo "Error updating record: " . $conn->error;
                            }
                        }
                    }

                    // Fetch the fav_instructions for the given createdID and favID after potential update
                    $instructionsQuery = "SELECT fav_instructions FROM favorites WHERE created_id = $createdID AND fav_id = $favID";
                    $instructionsResult = mysqli_query($conn, $instructionsQuery);
                    if ($instructionsResult && mysqli_num_rows($instructionsResult) > 0) {
                        $row = mysqli_fetch_assoc($instructionsResult);
                        $favInstructionsText = $row['fav_instructions'] === null || $row['fav_instructions'] === ''
                            ? '<i>No instructions provided.</i>'
                            : htmlspecialchars($row['fav_instructions']);
                    } else {
                        $favInstructionsText = '<i>No instructions found.</i>';
                    }

                    echo '<div style="margin-bottom: 10px;"><strong>Instructions: </strong>' . $favInstructionsText . '</div>'; // Display fetched instructions

                    if ($userid == $createdID) {
                        echo '<form method="POST" action="">'; // Action is set to empty string to submit to the same page.
                        echo '<input type="hidden" name="favID" value="' . htmlspecialchars($favID) . '">'; // Hidden input for favID

                        // Textbox for updating instructions with only the placeholder
                        echo '<input type="text" name="favInstructions" id="favInstructions" placeholder="Create / update team instructions..." style="width: 350px;">';
                        echo '<button type="submit" name="update">Update</button>';
                        echo '</form>';
                    }

                    while ($userRow = mysqli_fetch_assoc($result)) {
                        echo '<div><i>Created By:</i> ' . htmlspecialchars($userRow['username']);
                        if (isset($createdAts[$favID][$createdID])) {
                            $formattedDate = date('F j, Y', strtotime($createdAts[$favID][$createdID]));
                            echo ' <i>On:</i> ' . htmlspecialchars($formattedDate) . '</div>'; // Display the creation date

                            if (isset($finalNamesArrayVendor[$favID])) {
                                echo '<div><i>Shared With:</i> ' . htmlspecialchars($finalNamesArrayVendor[$favID]) . '</div>'; // Display shared information
                            } else {
                                echo '<div>No entry in finalNamesArrayVendor for favID: ' . htmlspecialchars($favID) . '</div>'; // Handle missing entries in finalNamesArrayVendor
                            }
                        } else {
                            echo '</div>'; // Close div if no creation date is found
                        }
                    }
                }
            }
        }

        echo '</div>'; // 6

        echo '<table>';
        echo '<tr>';
        echo '<td id="container1_' . htmlspecialchars($favID) . '" style="vertical-align: top;">';
        echo '<div class="table-default">';

        echo "<table border='1' class='data-table1'>
            <tr>
                <th class='vend-col-vendor'>Vendor Name</th>
                <th class='vend-col-desc'>Description</th>
                <th class='vend-col-email'>Email</th>
                <th class='vend-col-phone'>Phone</th>
                <th class='vend-col-website'>Website</th>
            </tr>";

        foreach ($vendorData as $vendor) {  // Use $vendorData here, with unique vendors
            $vendorID = $vendor['com_id'];
            $vendorName = $vendor['com_name'];
            $vendorDescription = $vendor['com_description'];
            $vendorEmail = $vendor['com_email'];
            $vendorPhone = $vendor['com_phone'];
            $vendorWebsite = $vendor['com_website'];

            echo "<tr>
            <td class='test-vendor-list-class' data-test-vendor-list-id='$vendorID'><input type='checkbox' name='save-vendors[]' value='$vendorID'><a href='../query_tests/entity.php?entity=$vendorID'>$vendorName</a></td>
            <td>$vendorDescription</td>
            <td><a href='mailto:$vendorEmail'>$vendorEmail</a></td>
            <td>$vendorPhone</td>
            <td><a href='$vendorWebsite' target='_blank'>$vendorWebsite</a></td>
        </tr>";
        }

        echo "</table>";
        //echo '<br>';
        echo '</div>';
        echo '</td>';
        echo '<td style="max-width: 20px;" id="container1_' . htmlspecialchars($favID) . '" >';
        //echo '<div style="width: 20px;"></div>';
        echo '</td>';
        echo '<td style="vertical-align: top;" id="container_' . htmlspecialchars($favID) . '">'; // New table to the right of each existing $favID
        echo '<div class="middle-side-right-table table-default">';
        echo '<table border="1" class="data-table">';
        echo '<tr><th>Item Name</th><th>Total Votes</th>';

        if (isset($finalNamesArrayVendor[$favID])) {
            $usernames = explode(", ", $finalNamesArrayVendor[$favID]);
            foreach ($usernames as $username) {
                $userID = isset($existingUsersWithUsernamesVendor[$favID][$username]) ? $existingUsersWithUsernamesVendor[$favID][$username] : '';
                echo '<th>' . htmlspecialchars($username) . ($userID ? '<!-- (' . $userID . ')-->' : '') . '</th>';
            }
        }
        echo '</tr>';

        // Vendors row
        $colspan = isset($finalNamesArrayVendor[$favID]) ? count(explode(", ", $finalNamesArrayVendor[$favID])) + 2 : 2;
        echo '<tr class="data_table_subheader"><td colspan="' . $colspan . '">Vendors</td></tr>';

        foreach ($vendorData as $vendor) {  // Use $vendorData here
            echo '<tr>';
            echo '<td><a href="/entity.php?entity=' . urlencode($vendor['com_id']) . '">' . htmlspecialchars($vendor['com_name']) . '</a></td>';
            echo '<td class="total-votes">0</td>'; // Placeholder for Total Votes

            if (isset($finalNamesArrayVendor[$favID])) {
                $usernames = explode(", ", $finalNamesArrayVendor[$favID]);
                foreach ($usernames as $username) {
                    $userID = isset($existingUsersWithUsernamesVendor[$favID][$username]) ? $existingUsersWithUsernamesVendor[$favID][$username] : '';
                    $voteFound = false;

                    for ($i = 0; $i < count($favIDVote); $i++) {
                        if ($favIDVote[$i] == $favID && $itemIDVote[$i] == $vendor['com_id'] && $userIDVote[$i] == $userID && $itemTypeVote[$i] == 4) {
                            echo '<td class="vote">1</td>';
                            $voteFound = true;
                            break;
                        }
                    }
                    if (!$voteFound) {
                        echo '<td class="vote">0</td>';
                    }
                }
            }
            echo '</tr>';
        }

        echo '</table>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        echo "</div>"; // 5 Closing the outer div here
        echo '</div>';
    }
    echo '</div>'; // 4
    //var_dump($vendorData);

    //var_dump($vendorData);

    //echo '<div style="width: 0px; position: absolute; right: 0%; top: 53%; transform: translateY(-45%); z-index: 10;">';
    //echo '<div id="triangle"></div>';
    //echo '</div>';



    /*echo '<div style="display: flex; justify-content: space-between;">'; // Parent container
// Your other content here
echo '<div>';
echo '<div class="right-side; id=right-side; style=width: 25px; height: 100px; background-color: lightgrey; display: flex; align-items: center; padding-left: 10px;">';
echo '<div id="triangle style=width: 0; height: 0; border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 10px solid black; margin-right: 10px; cursor: pointer;"></div>';
echo '</div>';
echo '</div>';
echo '</div>';
*/
    // Additional content block, adjacent to the right of the original block
    echo '<div class="left-side">'; // 2
    echo '<div class="left-inset-panel">'; // 3
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
    echo '<h4>Filter Options</h4>';

    ?>
    <br>
    <div id="group1">
        <button id="all_lists">All</button>
        <button id="my_lists">My Lists</button>
        <button id="shared_lists">Shared With Me</button>
    </div>
    <br>
    <div id="group2">
        <button id="archive_active">All</button>
        <button id="active_only">Active</button>
        <button id="archive_only">Archived</button>
    </div>
    <br>
    <div id="group3">
        <button id="both_lists">All</button>
        <button id="core_only">Core</button>
        <button id="results_only">Results</button>
    </div>


    <?php
    echo '</div>'; // 2
    echo '<br>';
    echo '<div class="left-inset-panel">'; // 3
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
    echo '<h4>Create New List</h4>';
    if (isset($_POST['submit'])) {
        // before we insert, verify this name is not in use
        $dup = 0;
        $inputName = isset($_POST['inputName']) ? trim($_POST['inputName']) : '';
        if (!empty($inputName)) {
            $sql = "select * 
                    from favorites
                    where fav_name = '" . $inputName . "'
                        and owner_id = " . $_SESSION['id'];
            $res = mysqli_query($conn, $sql);
            if ($res->num_rows > 0) {
                echo "Error: List name already exists";
                $dup = 1;
            }
            if (!$dup) {
                $sql = "INSERT INTO favorites (created_at, fav_name, created_by, created_id, owner_id, active)
                VALUES ('$dateTime', '$inputName', '$username', $userid, $userid, 1)";
                if (mysqli_query($conn, $sql) === TRUE) {
                    echo "Record <strong>" . $inputName . "</strong> added successfully";
                    $favIDFetch = "SELECT MAX(fav_id) AS max_fav_id FROM favorites WHERE created_id = $userid";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
        } else {
            echo "Textbox cannot be empty.";
        }
    } //echo '<br>';

    $newListName = 'New List Name...';
    ?>
    <form method="post" action="">
        <input type="text" name="inputName" id="inputId" style="width: 200px;" placeholder="<?php echo htmlspecialchars($newListName) ?>"><br />
        <?php
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
        echo '<br>';

        ?>
        <button type="submit" name="submit" id="addList">Create</button>
    </form>

    <?php
    echo '</div>'; // 2
    echo '<br>';
    echo '<div class="left-inset-panel">'; // 3
    echo '<h4>Populate List</h4>';

    echo '<label for="textInput"></label>';
    //echo '<input type="text" name="textInput" id="textInput" style="width: 200px;" placeholder="Search Items to Add to List..."></input><br><br>';
    include_once '../query_tests/includes/populatelist.inc.php';

    //echo '<br><br>';
    //echo '<button type="submit">Search</button>';
    echo '<button type="submit">Update</button>';
    echo '<button type="button" onclick="clearForm()">New Search</button>';
    //echo '</form>';
    echo '<script type="text/javascript">
    function clearForm() {
        // Reset the form
        document.querySelector("form").reset();

        // Remove the query string from the URL
        if (window.location.href.includes("#")) {
            // Replace the URL without the hash
            window.history.replaceState(null, null, window.location.pathname);

            // Refresh the page after clearing the hash
            window.location.reload();
        } else {
            // If no hash, just refresh the page
            window.location.reload();
        }
    }
</script>';

    //print_r($_POST);
    echo '</form>';

    echo '</div>'; // 2
    echo '<br>';
    echo '<div class="left-inset-panel">'; // 3
    echo '<form method="post" action="">';  // Add form with POST method
    echo '<tr><th>';
    echo '<h4>Share / Unshare List</h4>';
    echo '<label for="userFavoritesListUC">Select User</label><br>';
    echo '<select name="userFavoritesListUC[]" id="userFavoritesListUC">';
    $userFavoritesListUC = userArrayDropdown($result6, $resultCheck6);

    if (!empty($userFavoritesListUC)) {
        foreach ($userFavoritesListUC as $favorite) {
            echo "<option value='{$favorite[0]}'>{$favorite[1]}</option>";
        }
    }
    echo '</select><br><br>';

    echo '<label for="selectList">Select List</label><br>';
    echo '<select name="selectList[]" id="selectList" style="width: 250px;">';
    $sqlValidate109 = "SELECT DISTINCT fav_name, favorites_item.fav_id favid
FROM favorites_item
INNER JOIN favorites
ON favorites_item.fav_id = favorites.fav_id
WHERE favorites.owner_id = $userid
ORDER BY fav_name";
    $resultSQLValidate = mysqli_query($conn, $sqlValidate109);
    $resultCheckSQLValidate = mysqli_num_rows($resultSQLValidate);

    function validateArrayDropdown($resultSQLValidate, $resultCheckSQLValidate)
    {
        $validateList = []; // Initialize the array
        if ($resultCheckSQLValidate > 0) {
            while ($row = mysqli_fetch_assoc($resultSQLValidate)) {
                $validateList[] = [$row['favid'], $row['fav_name']];
            }
            return $validateList;
        } else {
            return [];
        }
    }
    $selectList = coworkerArrayDropdown($resultSQLCoworkers, $resultCheckSQLCoworkers);

    if (!empty($selectList)) {
        foreach ($selectList as $list) {
            echo "<option value='{$list[0]}'>{$list[1]}</option>";
        }
    }
    echo '</select>';

    echo '<br>';
    echo '<div>'; // 4
    echo '<br>';
    echo '<button type="submit" name="addUserToList" value="addUserToList">Add</button>';  // Remove array notation
    echo '<button type="submit" name="removeUserFromList" value="removeUserFromList">Remove</button>';
    echo '</div>'; // 3
    echo '</th></tr>';
    echo '</form>';  // Close form
    echo '</div>'; // 2




    echo '<br>';
    echo '<div class="left-inset-panel">'; // 3
    echo '<h4>Validate Lists</h4>'; /*
echo '<label for="validateList">Select List</label><br>';
echo '<select name="validateList[]" id="validateList">';
$validateList = validateArrayDropdown($resultSQLValidate, $resultCheckSQLValidate);


if (!empty($validateList)) {
    foreach ($validateList as $list) {
        echo "<option value='{$list[0]}'>{$list[1]}</option>";
    }
}
echo '</select><br><br>'; 
*/
    echo '<form method="post" action="#">';
    include_once 'includes/productsearch.inc.php';
    $formSubmitted = isset($_POST['submitValidation']);
    ?>
    <script>
        var indexArray = <?php echo json_encode($indexArray); ?>;

        var uniqueVendors = <?php echo json_encode($vendorIndexArray); ?>;
        var uniqueVendors = Object.values(uniqueVendors).map(number => number);
        console.log(indexArray);
        console.log(uniqueVendors);
        var formSubmitted = <?php echo $formSubmitted ? 'true' : 'false'; ?>;
    </script>
    <?php

    // WHAT DO I WANT THIS QUERY TO DO?
    // 1. Take each key element from the list (unique id for use cases, techstacks, products, or vendors, depending on the type of list it is) and put it in an array
    // 2. Eliminate any duplicates from the array
    // 3. Run the search query and return the corresponding elements relative to those on the list (if it's a techstack list, return unique IDs for techstacks, for example)
    // 4. Compare the two arrays, and create a new array that contains only the values that match in each of the orginal two arrays
    // 5. If an element in a new array matches an element in the list that was just queries, make that element bold
    // 6. If an element in the list is not matched by an element in the new array, create a strike-through

    // Design Feature: Two buttons in each techstack cell: Upvote or Clear. The final column in the table will list all the upvotes, which will refresh in real time. 
    //if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitValidation'])) {
    if (!empty($indexArray)) {

        include_once '../query_tests/includes/search.inc.php';
    ?>
        <script>
            function toggleOptions(id) {
                var options = document.getElementById(id);
                if (options.style.display === "none") {
                    options.style.display = "block";
                } else {
                    options.style.display = "none";
                }
            }
        </script>
        <?php
        echo '<br>';
        echo '<button type="submit" name="submitValidation" value="1">Submit Query</button>';
        echo '<button type="button" onclick="clearHashAndReload();" name="clearValidation">Clear All</button><br>';


        $implodedIndexArray = implode(',', $indexArray);

        $sqlValidateProd = "SELECT DISTINCT product.prod_id prodid
        FROM use_case
        INNER JOIN join_use_case_techstack
        ON use_case.uc_id = join_use_case_techstack.use_case_id
        INNER JOIN techstack_name
        ON join_use_case_techstack.techstack_id = techstack_name.tech_id
        INNER JOIN techstack_summary
        ON techstack_name.tech_id = techstack_summary.tech_id
        INNER JOIN product
        ON techstack_summary.prod_id = product.prod_id
        WHERE use_case.uc_id IN ($implodedIndexArray)
        ORDER BY product.prod_id";

        $resultSQLValidateProd = mysqli_query($conn, $sqlValidateProd);
        if (!empty($resultSQLValidateProd)) {
            while ($row = mysqli_fetch_assoc($resultSQLValidateProd)) {
                $prodArray[] = $row['prodid'];
            }
        }
    } else {
        include_once '../query_tests/includes/search.inc.php';
        ?>
        <script>
            function toggleOptions(id) {
                var options = document.getElementById(id);
                if (options.style.display === "none") {
                    options.style.display = "block";
                } else {
                    options.style.display = "none";
                }
            }
        </script>
    <?php
        echo '<br>';
        echo '<button type="submit" name="submitValidation" value="1">Submit Query</button>';
        echo '<button type="button" onclick="clearHashAndReload();" name="clearValidation">Clear All</button><br>';
    }

    //print_r($prodArray);
    ?>
    <br>


    </form>
</body>

</html>


<?php
echo '</div>'; // 2

echo '</div>'; // 1 
echo '</div>'; // 0
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const columns = ["techstack", "verified", "product", "vendor"];
        const maxWidths = {};

        columns.forEach(column => {
            maxWidths[column] = 0;
            document.querySelectorAll(".col-" + column).forEach(cell => {
                const width = cell.offsetWidth;
                if (width > maxWidths[column]) {
                    maxWidths[column] = width;
                }
            });
        });

        columns.forEach(column => {
            document.querySelectorAll(".col-" + column).forEach(cell => {
                cell.style.width = maxWidths[column] + "px";
            });
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const parent = document.querySelector(".parent-class");
        const leftSideDiv = document.querySelector(".left-side");
        const middleSideDiv = document.querySelector(".middle-side");

        const rightSideDiv = document.createElement("div");
        rightSideDiv.className = "right-side";

        parent.innerHTML = "";
        parent.appendChild(leftSideDiv);
        parent.appendChild(middleSideDiv);
        parent.appendChild(rightSideDiv);

        function adjustHeight() {
            const middleSideHeight = middleSideDiv.offsetHeight;
            rightSideDiv.style.height = middleSideHeight + "px";
        }

        window.addEventListener("resize", adjustHeight);
        adjustHeight();
    });
</script>


<script>
    // Function to save the scroll position
    function saveScrollPosition() {
        localStorage.setItem("scrollPosition", window.scrollY);
    }

    // Function to restore the scroll position
    function restoreScrollPosition() {
        var scrollPosition = localStorage.getItem("scrollPosition");
        if (scrollPosition !== null) {
            window.scrollTo(0, parseInt(scrollPosition));
        }
    }

    // Save scroll position before the page is unloaded
    window.addEventListener("beforeunload", saveScrollPosition);

    // Restore scroll position when the page loads
    window.addEventListener("load", restoreScrollPosition);

    // Function to toggle content and save state to localStorage
    function toggleContent(id) {
        var triangle = document.getElementById("triangle-" + id);
        var content = document.getElementById(id + "-content");

        triangle.classList.toggle("rotate");
        if (content.style.display === "none") {
            content.style.display = "block";
            localStorage.setItem(id + "-content", "block");
        } else {
            content.style.display = "none";
            localStorage.setItem(id + "-content", "none");
        }
    }

    // Add event listeners for each triangle
    document.getElementById("triangle-usecase").addEventListener("click", function() {
        toggleContent("usecase");
    });

    document.getElementById("triangle-techstack").addEventListener("click", function() {
        toggleContent("techstack");
    });

    document.getElementById("triangle-product").addEventListener("click", function() {
        toggleContent("product");
    });

    document.getElementById("triangle-vendor").addEventListener("click", function() {
        toggleContent("vendor");
    });

    // Restore the state of the content sections on page load
    window.addEventListener("load", function() {
        var contentIds = ["usecase", "techstack", "product", "vendor"];

        contentIds.forEach(function(id) {
            var content = document.getElementById(id + "-content");
            var triangle = document.getElementById("triangle-" + id);
            var displayState = localStorage.getItem(id + "-content");

            if (displayState === "block") {
                content.style.display = "block";
                triangle.classList.add("rotate");
            } else {
                content.style.display = "none";
                triangle.classList.remove("rotate");
            }
        });
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (formSubmitted) {
            // Process Product cells and apply corresponding Vendor and Techstack styles
            var productCells = document.querySelectorAll(".col-product");
            productCells.forEach(function(productCell) {
                var productId = productCell.getAttribute("data-product-id");
                var techStackCell = productCell.closest('tr').querySelector('[data-techstack-id]');
                var vendors = productCell.closest('tr').querySelectorAll('.col-vendor');
                //console.log(indexArray)
                if (indexArray.includes(productId)) {
                    productCell.classList.add("font-bold");
                    // Apply the same class to corresponding vendor cells
                    vendors.forEach(function(vendorCell) {
                        vendorCell.classList.add("font-bold");
                    });
                } else {
                    productCell.classList.add("font-not-bold");
                    // Apply the same class to corresponding vendor cells
                    vendors.forEach(function(vendorCell) {
                        vendorCell.classList.add("font-not-bold");
                    });

                    // Apply "font-not-bold" to the techstack cell if any product is "font-not-bold"
                    if (techStackCell) {
                        techStackCell.classList.add("font-not-bold");
                    }
                }
            });

            // Handle Techstack cells
            var techstackCells = document.querySelectorAll("[data-techstack-id]");
            techstackCells.forEach(function(techstackCell) {
                var techstackId = techstackCell.getAttribute("data-techstack-id");
                var associatedProducts = document.querySelectorAll("[data-product-id]");
                var allProductsBold = true;
                associatedProducts.forEach(function(productCell) {
                    if (productCell.closest('tr').querySelector('[data-product-techstack-id="' + techstackId + '"]')) {
                        //console.log(productCell.classList);
                        if (productCell.classList.contains("font-not-bold")) {
                            allProductsBold = false;
                        }
                    }
                });
                if (!allProductsBold) {
                    techstackCell.classList.add("font-not-bold");
                } else {
                    techstackCell.classList.add("font-bold");
                }
            });
            /*
             var techstackCells = document.querySelectorAll("[data-techstack-id]");

                techstackCells.forEach(function(techstackCell) {
                    var techstackId = techstackCell.getAttribute("data-techstack-id");
                    var associatedProducts = document.querySelectorAll('[data-product-techstack-id="' + techstackId + '"][data-product-id]');

                    var allProductsBold = true;
                    associatedProducts.forEach(function(productCell) {
                        if (productCell.classList.contains("font-not-bold")) {
                            allProductsBold = false;
                        }
                    });

                    if (!allProductsBold) {
                        techstackCell.classList.add("font-not-bold");
                    } else {
                        techstackCell.classList.add("font-bold");
                    }
                }); */
            // Handle Usecase cells
            var usecaseCells = document.querySelectorAll("[data-usecase-id]");
            usecaseCells.forEach(function(usecaseCell) {
                var usecaseId = usecaseCell.getAttribute("data-usecase-id");
                var associatedTechStacks = document.querySelectorAll('[data-techstack-id]');

                var allTechStacksNotBold = true;
                var anyTechStackBold = false;

                associatedTechStacks.forEach(function(techstackCell) {
                    if (techstackCell.closest('[data-usecase-id="' + usecaseId + '"]')) {
                        if (!techstackCell.classList.contains("font-not-bold")) {
                            allTechStacksNotBold = false;
                        }
                        if (techstackCell.classList.contains("font-bold")) {
                            anyTechStackBold = true;
                        }
                    }
                });

                if (allTechStacksNotBold) {
                    usecaseCell.querySelector('a').classList.add("font-not-bold");
                }

                // Apply "font-bold" to usecase if any techstack is "font-bold"
                if (anyTechStackBold) {
                    usecaseCell.querySelector('a').classList.add("font-bold");
                }
            });

            var productCells = document.querySelectorAll(".techstack-col-product");
            productCells.forEach(function(productCell) {
                var productId = productCell.getAttribute("techstack-data-product-id");
                var techStackCell = productCell.closest('tr').querySelector('[techstack-data-techstack-id]');

                //console.log(indexArray)
                if (indexArray.includes(productId)) {
                    productCell.classList.add("font-bold");

                } else {
                    productCell.classList.add("font-not-bold");


                    // Apply "font-not-bold" to the techstack cell if any product is "font-not-bold"
                    if (techStackCell) {
                        techStackCell.classList.add("font-not-bold");
                    }
                }
            });

            var techstackCells = document.querySelectorAll("[techstack-data-techstack-id]");
            techstackCells.forEach(function(techstackCell) {
                var techstackId = techstackCell.getAttribute("techstack-data-techstack-id");
                var associatedProducts = document.querySelectorAll("[techstack-data-product-id]");
                var allProductsBold = true;
                associatedProducts.forEach(function(productCell) {
                    if (productCell.closest('tr').querySelector('[techstack-data-product-techstack-id="' + techstackId + '"]')) {
                        //console.log(productCell.classList);
                        if (productCell.classList.contains("font-not-bold")) {
                            allProductsBold = false;
                        }
                    }
                });
                if (!allProductsBold) {
                    techstackCell.classList.add("font-not-bold");
                } else {
                    techstackCell.classList.add("font-bold");
                }
            });
            // New code for vendor list
            var testVendorCells = document.querySelectorAll(".test-vendor-list-class");
            testVendorCells.forEach(function(testVendorCell) {
                var vendorId = testVendorCell.getAttribute("data-test-vendor-list-id");
                //console.log(uniqueVendors);
                if (uniqueVendors.includes(vendorId)) {
                    testVendorCell.classList.add("font-bold");

                } else {
                    testVendorCell.classList.add("font-not-bold");

                }
            });
        }
    });
    //console.log(uniqueVendors)
</script>


<script>
    function clearHashAndReload() {
        // Check if the URL contains a hash
        if (window.location.hash) {
            // Remove the hash part from the URL
            window.location.hash = ''; // This clears the hash directly
        }
        // Reload the page
        window.location.href = window.location.href.split('#')[0]; // Reload without the hash
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rightSideDiv = document.querySelector('.middle-side');

        function setDivHeight() {
            // Set the height to 100px less than the viewport height
            rightSideDiv.style.height = `${window.innerHeight - 100}px`;
        }

        // Set the initial height
        setDivHeight();

        // Adjust height on window resize
        window.addEventListener('resize', setDivHeight);

        // Example: Adding new content dynamically
        function addContent() {
            const newContent = document.createElement('p');
            rightSideDiv.appendChild(newContent);
        }

        // Call this function to add new content (example usage)
        addContent();
    });
</script>

<!-- Right rotating triangle -->
<script>
    // Function to save the current state to localStorage
    /* function saveState(containersVisible) {
         localStorage.setItem("containersVisible", containersVisible ? "true" : "false");
     }

     // Function to load the state from localStorage
     function loadState() {
         const containersVisible = localStorage.getItem("containersVisible") === "true";
         return containersVisible;
     }

     document.addEventListener("DOMContentLoaded", function() {
         const containersVisible = loadState();

         // Get the core_only and both_lists button elements
         const coreOnlyButton = document.getElementById("core_only");
         const bothListsButton = document.getElementById("both_lists");

         // Select all containers with IDs starting with "container_"
         const containers = document.querySelectorAll('td[id^="container_"]');

         // Set the initial visibility of containers based on the saved state
         containers.forEach(container => {
             container.style.display = containersVisible ? "table-cell" : "none";
         });

         // Add event listener to the core_only button for activating the script
         coreOnlyButton.addEventListener("click", function() {
             // Make all containers visible
             containers.forEach(container => {
                 container.style.display = "none";
             });

             // Save the new state to localStorage
             saveState(false);
         });

         // Add event listener to the both_lists button for deactivating the script
         bothListsButton.addEventListener("click", function() {
             // Make all containers visible
             containers.forEach(container => {
                 container.style.display = "table-cell";
             });

             // Save the new state to localStorage
             saveState(true);
         });
     }); */
</script>


<!-- Archive Button Darkening -->

<script>
    // Function to activate the selected button and deactivate others in a group
    function activateButton(groupSelector, buttonId) {
        // Remove active class from all buttons in the specified group
        document.querySelectorAll(groupSelector + ' button').forEach(function(btn) {
            btn.classList.remove('active');
        });

        // Add active class to the selected button
        document.getElementById(buttonId).classList.add('active');
    }

    // Group 1 (All, My Lists, Shared With Me)
    document.getElementById('all_lists').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeListButton', 'all_lists');
        activateButton('#group1', 'all_lists');
    });

    document.getElementById('my_lists').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeListButton', 'my_lists');
        activateButton('#group1', 'my_lists');
    });

    document.getElementById('shared_lists').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeListButton', 'shared_lists');
        activateButton('#group1', 'shared_lists');
    });

    // Group 2 (All, Active, Archived)
    document.getElementById('archive_active').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeArchiveButton', 'archive_active');
        activateButton('#group2', 'archive_active');
    });

    document.getElementById('active_only').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeArchiveButton', 'active_only');
        activateButton('#group2', 'active_only');
    });

    document.getElementById('archive_only').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeArchiveButton', 'archive_only');
        activateButton('#group2', 'archive_only');
    });

    // Group 3 (All, Core, Results)
    document.getElementById('both_lists').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeCoreButton', 'both_lists');
        activateButton('#group3', 'both_lists');
    });

    document.getElementById('core_only').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeCoreButton', 'core_only');
        activateButton('#group3', 'core_only');
    });

    document.getElementById('results_only').addEventListener('click', function(event) {
        event.preventDefault();
        localStorage.setItem('activeCoreButton', 'results_only');
        activateButton('#group3', 'results_only');
    });

    // On page load, apply the active state to the last selected button in each group
    document.addEventListener('DOMContentLoaded', function() {
        const activeListButton = localStorage.getItem('activeListButton') || 'all_lists'; // group1
        const activeArchiveButton = localStorage.getItem('activeArchiveButton') || 'archive_active'; // group2
        const activeCoreButton = localStorage.getItem('activeCoreButton') || 'both_lists'; // group3
        //console.log(activeArchiveButton)
        //applyFilters()

        activateButton('#group1', activeListButton);
        activateButton('#group2', activeArchiveButton);
        activateButton('#group3', activeCoreButton);
    });
</script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Define buttons for the first group (My Lists, Shared Lists, All)
        const myListsButton = document.getElementById("my_lists");
        const sharedListsButton = document.getElementById("shared_lists");
        const allListsButton = document.getElementById("all_lists");

        // Define buttons for the second group (Active, Archived, All)
        const activeOnlyButton = document.getElementById("active_only");
        const archiveOnlyButton = document.getElementById("archive_only");
        const archiveActiveButton = document.getElementById("archive_active");
        applyFilters()

        // Store the currently active buttons in local storage
        function storeActiveButtons(listButtonId, archiveButtonId) {
            if (listButtonId) {
                localStorage.setItem("activeListButton", listButtonId);
            }
            if (archiveButtonId) {
                localStorage.setItem("activeArchiveButton", archiveButtonId);
            }
        }

        // Retrieve the active buttons from local storage
        function getActiveListButton() {
            return localStorage.getItem("activeListButton") || "all_lists"; // Default to 'all_lists'
        }

        function getActiveArchiveButton() {
            return localStorage.getItem("activeArchiveButton") || "archive_active"; // Default to 'archive_active'
        }

        // Function to apply the combined filters from both button groups
        function applyFilters() {
            //console.log('applyFilters')
            const activeListButton = getActiveListButton();
            const activeArchiveButton = getActiveArchiveButton();
            const items = document.querySelectorAll('.usecase-item');
            //console.log(activeArchiveButton)

            items.forEach(function(item) {
                const ownerId = item.getAttribute("data-owner-id");
                const isActive = item.getAttribute('data-active') == "1";
                const isArchived = item.getAttribute('data-active') == "0";

                let showItem = true;

                // Apply list filter
                if (activeListButton === "my_lists") {
                    if (ownerId != "<?php echo $userid; ?>") showItem = false;
                } else if (activeListButton === "shared_lists") {
                    if (ownerId == "<?php echo $userid; ?>") showItem = false;
                }

                // Apply archive filter
                if (activeArchiveButton === "active_only") {
                    if (!isActive) showItem = false;
                } else if (activeArchiveButton === "archive_only") {
                    if (!isArchived) showItem = false;
                }

                item.style.display = showItem ? "block" : "none";
            });
        }

        // Event listeners for the first group
        myListsButton.addEventListener("click", function(event) {
            event.preventDefault();
            storeActiveButtons("my_lists", null); // Save active button states
            activateListButton("my_lists"); // Activate the button
            applyFilters(); // Apply filters with new selection
        });

        sharedListsButton.addEventListener("click", function(event) {
            event.preventDefault();
            storeActiveButtons("shared_lists", null);
            activateListButton("shared_lists");
            applyFilters();
        });

        allListsButton.addEventListener("click", function(event) {
            event.preventDefault();
            storeActiveButtons("all_lists", null);
            activateListButton("all_lists");
            applyFilters();
        });

        // Event listeners for the second group
        activeOnlyButton.addEventListener('click', function(event) {
            event.preventDefault();
            storeActiveButtons(null, 'active_only');
            activateArchiveButton('active_only');
            applyFilters();
        });

        archiveOnlyButton.addEventListener('click', function(event) {
            event.preventDefault();
            storeActiveButtons(null, 'archive_only');
            activateArchiveButton('archive_only');
            applyFilters();
        });

        archiveActiveButton.addEventListener('click', function(event) {
            event.preventDefault();
            storeActiveButtons(null, 'archive_active');
            activateArchiveButton('archive_active');
            applyFilters();
        });

        // Functions to activate the correct buttons
        function activateListButton(buttonId) {
            // Remove active class only from the list buttons
            myListsButton.classList.remove("active");
            sharedListsButton.classList.remove("active");
            allListsButton.classList.remove("active");
            // Add active class to the selected list button
            document.getElementById(buttonId).classList.add("active");

            // Ensure the current archive button remains active
            const currentArchiveButton = getActiveArchiveButton();
            document.getElementById(currentArchiveButton).classList.add("active");
        }

        function activateArchiveButton(buttonId) {
            // Remove active class only from the archive buttons
            activeOnlyButton.classList.remove("active");
            archiveOnlyButton.classList.remove("active");
            archiveActiveButton.classList.remove("active");
            // Add active class to the selected archive button
            document.getElementById(buttonId).classList.add("active");

            // Ensure the current list button remains active
            const currentListButton = getActiveListButton();
            document.getElementById(currentListButton).classList.add("active");
        }

        // On page load, apply the active state to the last selected buttons and filters
        document.addEventListener('DOMContentLoaded', function() {
            const activeListButton = getActiveListButton();
            const activeArchiveButton = getActiveArchiveButton();
            activateListButton(activeListButton);
            activateArchiveButton(activeArchiveButton);
            applyFilters();
        });
    });
</script>

<!-- Bottom row of buttons -->

<script>
    // Function to save the current state to localStorage
    function saveState(containersVisible, containers1Visible) {
        localStorage.setItem("containersVisible", containersVisible ? "true" : "false");
        localStorage.setItem("containers1Visible", containers1Visible ? "true" : "false");
    }

    // Function to load the state from localStorage
    function loadState() {
        const containersVisible = localStorage.getItem("containersVisible") === "true";
        const containers1Visible = localStorage.getItem("containers1Visible") === "true";
        return {
            containersVisible,
            containers1Visible
        };
    }

    document.addEventListener("DOMContentLoaded", function() {
        const {
            containersVisible,
            containers1Visible
        } = loadState();

        // Get the core_only, results_only, and both_lists button elements
        const coreOnlyButton = document.getElementById("core_only");
        const resultsOnlyButton = document.getElementById("results_only");
        const bothListsButton = document.getElementById("both_lists");

        // Select all containers with IDs starting with "container_" and "container1_"
        const containers = document.querySelectorAll('td[id^="container_"]');
        const containers1 = document.querySelectorAll('td[id^="container1_"]');

        // Set the initial visibility of containers based on the saved state
        containers.forEach(container => {
            container.style.display = containersVisible ? "table-cell" : "none";
        });

        containers1.forEach(container1 => {
            container1.style.display = containers1Visible ? "table-cell" : "none";
            if (containers1Visible && !containersVisible) {
                container1.classList.add('show-table');
            }
        });

        // Add event listener to the core_only button for activating the script
        coreOnlyButton.addEventListener("click", function() {
            // Hide all containers with "container_"
            containers.forEach(container => {
                container.style.display = "none";
            });

            // Show all containers with "container1_"
            containers1.forEach(container1 => {
                container1.style.display = "table-cell";
                container1.classList.add('show-table');
            });

            // Save the new state to localStorage
            saveState(false, true);
        });

        // Add event listener to the results_only button for hiding 'container1_' containers
        resultsOnlyButton.addEventListener("click", function() {
            // Hide all containers with "container1_"
            containers1.forEach(container1 => {
                container1.style.display = "none";
                container1.classList.remove('show-table');
            });

            // Show all containers with "container_"
            containers.forEach(container => {
                container.style.display = "table-cell";
            });

            // Save the new state to localStorage
            saveState(true, false);
        });

        // Add event listener to the both_lists button for deactivating the script
        bothListsButton.addEventListener("click", function() {
            // Show all containers with both "container_" and "container1_"
            containers.forEach(container => {
                container.style.display = "table-cell";
            });

            containers1.forEach(container1 => {
                container1.style.display = "table-cell";
                container1.classList.remove('show-table');
            });

            // Save the new state to localStorage
            saveState(true, true);
        });
    });
</script>

<!-- Hold scroll position upon refresh -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const middleSideDiv = document.querySelector('.middle-side');

        // Restore scroll position when the page loads
        function restoreScrollPosition() {
            const scrollTop = localStorage.getItem('middleSideScrollTop');
            const scrollLeft = localStorage.getItem('middleSideScrollLeft');

            if (scrollTop !== null) {
                middleSideDiv.scrollTop = parseInt(scrollTop, 10);
            }
            if (scrollLeft !== null) {
                middleSideDiv.scrollLeft = parseInt(scrollLeft, 10);
            }
        }

        // Save scroll position when the page is about to be unloaded
        function saveScrollPosition() {
            localStorage.setItem('middleSideScrollTop', middleSideDiv.scrollTop);
            localStorage.setItem('middleSideScrollLeft', middleSideDiv.scrollLeft);
        }

        // Attach event listeners
        window.addEventListener('beforeunload', saveScrollPosition);
        window.addEventListener('load', restoreScrollPosition);
        restoreScrollPosition(); // Ensure it runs after the page is loaded
    });
</script>

<!-- Sum Total Votes in Vote Tables -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.data-table tr');

        rows.forEach(function(row) {
            const voteCells = row.querySelectorAll('.vote');
            const totalCell = row.querySelector('.total-votes');

            let totalVotes = 0;

            voteCells.forEach(function(cell) {
                totalVotes += parseInt(cell.textContent, 10);
            });

            if (totalCell) {
                totalCell.textContent = totalVotes;
            }
        });
    });
</script>

<!-- Replaces "0" with "-" and "1" with checkmarks in vote tables -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.data-table tr');

        rows.forEach(function(row) {
            const voteCells = row.querySelectorAll('.vote');
            const totalCell = row.querySelector('.total-votes');

            let totalVotes = 0;

            voteCells.forEach(function(cell) {
                const voteValue = parseInt(cell.textContent, 10);
                if (voteValue === 1) {
                    cell.textContent = '✓'; // Replace "1" with a checkmark
                } else if (voteValue === 0) {
                    cell.textContent = '-'; // Replace "0" with "-"
                }
                totalVotes += voteValue;
            });

            if (totalCell) {
                if (totalVotes === 0) {
                    totalCell.textContent = '-'; // Replace "0" in the Total Votes column with "-"
                } else {
                    totalCell.textContent = totalVotes;
                }

                // Apply the same styling as the "Total Votes" header
                totalCell.style.fontWeight = 'bold'; // Example: Use the same font weight
                totalCell.style.fontSize = 'inherit'; // Ensure it uses the same size as the header
                totalCell.style.fontFamily = 'inherit'; // Use the same font family
                totalCell.style.color = 'inherit'; // Use the same text color
                totalCell.style.padding = 'inherit'; // Apply the same padding
                totalCell.style.textAlign = 'center'; // Center align like the header
            }
        });
    });
</script>

<!-- Centers vote results in tables -->
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<?php
//include_once '../query_tests/includes/footer.inc.php';
?>
</body>

</html>
<?php
