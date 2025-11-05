<?php
//delete this includes_once 'dbh.inc.php' when done testing  
include_once 'dbh.inc.php';

ob_start();
$favIDFetch = 0;

$stmt = $conn->prepare("SELECT grp_id FROM join_user_grp WHERE user_id = ?");
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

$grpID = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grpID[] = $row['grp_id'];
    }
}
$stmt->close();

if (isset($_POST['submit1'])) {
    // Before we insert, verify this name is not in use
    $dup = 0;
    $inputName = isset($_POST['inputName']) ? trim($_POST['inputName']) : '';

    if (!empty($inputName)) {
        // Prepared statement to check for duplicate list name
        $stmt = $conn->prepare("SELECT * FROM favorites WHERE fav_name = ? AND owner_id = ?");
        $stmt->bind_param("si", $inputName, $_SESSION['id']);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $listExists = "Error: List name already exists";
            $dup = 1;
        }
        $stmt->close();

        // If no duplicates, insert the new favorite
        if (!$dup) {
            // Prepared statement for insert
            $stmt = $conn->prepare("INSERT INTO favorites (created_at, fav_name, created_by, created_id, owner_id, active) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
            $active = 1;
            $stmt->bind_param("sssiii", $dateTime, $inputName, $username, $userid, $userid, $active);
            if ($stmt->execute() === TRUE) {
                // Get the max favorite ID for the newly inserted favorite
                $stmt = $conn->prepare("SELECT MAX(fav_id) AS max_fav_id FROM favorites WHERE created_id = ?");
                $stmt->bind_param("i", $userid);
                $stmt->execute();
                $result = $stmt->get_result();
                // Further processing can be done with $result if needed
            } else {
                $listError = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $textboxFull = "Textbox cannot be empty.";
    }
}


$newListName = 'Create New List...';

// Capture the output of the form

echo '<form method="post" action="#">';
echo '<input type="text" name="inputName" id="inputId" placeholder="' . htmlspecialchars($newListName) . '"><br/>';
$stmt = $conn->prepare("SELECT * FROM favorite_table_type");
$stmt->execute();
$result10 = $stmt->get_result();

if ($result10) {
    $listTypeID = array();
    $listTypeName = array();
    while ($row = $result10->fetch_assoc()) {
        $listTypeID[] = $row['table_type_id'];
        $listTypeName[] = $row['table_type'];
    }
}

$stmt->close();

echo '<button type="submit" name="submit1">Create</button>';
if (!empty($textboxFull)) {
    echo '<br>';
    echo '<div style="text-align: left;">' . $textboxFull . '</div>';
}
if (!empty($listExists)) {
    echo '<br>';
    echo '<div style="text-align: left;">' . $listExists . '</div>';
}
if (!empty($listError)) {
    echo '<br>';
    echo '<div style="text-align: left;">' . $listError . '</div>';
}
echo '</form>';
$formOutput = ob_get_clean();
