<?php
include_once 'dbh.inc.php';
include_once 'session.inc.php';
echo '<form method="POST">';
echo '<input type="text" name="favInstructions" id="favInstructions" placeholder="Enter Team Instructions..." style="width: 350px;">';
echo '<button type="submit" name="update">Update</button>';
echo '</form>';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $favInstructions = mysqli_real_escape_string($conn, $_POST['favInstructions']);

    $fav_id = 280;

    $sql = "UPDATE favorites SET fav_instructions = '$favInstructions' WHERE owner_id = $userid AND fav_id = $fav_id";
    if ($conn->query($sql) === TRUE) {
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
