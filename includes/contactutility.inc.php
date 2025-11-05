<?php
include_once 'session.inc.php';

echo '<form action="#" method="post">';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_action'])) {
    $techstackID = $_POST['techstack_id'];
    $usecaseID = $_POST['usecase_id'];
    $favID = $_POST['fav_id'];

    // Fetching the use case name (Prepared Statement)
    $stmt = $conn->prepare("SELECT use_case_name FROM use_case WHERE uc_id = ?");
    $stmt->bind_param("i", $usecaseID); // Binding the integer parameter usecaseID
    $stmt->execute();
    $result = $stmt->get_result();
    $useCaseName = '';
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $useCaseName = $row['use_case_name'];
    }
    $stmt->close();

    // Fetching the techstack name (Prepared Statement)
    $stmt = $conn->prepare("SELECT techstack_name FROM techstack_name WHERE tech_id = ?");
    $stmt->bind_param("i", $techstackID); // Binding the integer parameter techstackID
    $stmt->execute();
    $result = $stmt->get_result();
    $techstackName = '';
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $techstackName = $row['techstack_name'];
    }
    $stmt->close();

    // Fetching the verifiers (Prepared Statement)
    $stmt = $conn->prepare("SELECT user_id FROM dummy_data WHERE tech_id = ?");
    $stmt->bind_param("i", $techstackID); // Binding the integer parameter techstackID
    $stmt->execute();
    $result = $stmt->get_result();
    $verifiers = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $verifiers[] = $row['user_id'];
        }
    }
    $stmt->close();

    // Preparing the subject and message with hyperlinks
    $techstackLink = '<a href="techstack.php?techstack=' . urlencode($techstackID) . '">' . htmlspecialchars($techstackName) . '</a>';
    $usecaseLink = '<a href="usecase.php?id=' . urlencode($usecaseID) . '">' . htmlspecialchars($useCaseName) . '</a>';

    $subject = "Request to connect about the following Techstack: <strong><i>" . $techstackLink . "</i></strong>.";
    $message = "Hello! I would like to chat with you about Techstack: <strong><i>" . $techstackLink . "</i></strong>, as applied to Use Case: <strong><i>" . $usecaseLink . "</i></strong>.";
    $subject = mysqli_real_escape_string($conn, $subject);
    $message = mysqli_real_escape_string($conn, $message);

    // Inserting the message into the inbox for each verifier
    foreach ($verifiers as $verifier) {
        $sql = "INSERT INTO inbox (created_at, sender_id, fav_list_id, subject_contents, message_contents, inbox_owner_id, retained, unread, hidden_value, tech_id, techstack, usecase_id, usecase) 
            VALUES (NOW(), $userid, NULL, '$subject', '$message', $verifier, 1, 1, 1, '$techstackID', '$techstackName', '$usecaseID', '$useCaseName')";
        mysqli_query($conn, $sql);
        echo "Message Sent! The recipient is reviewing your message.";
    }

    // Redirect after form submission
    header('Location: ../favorites.php');
    exit;
}

// Add the hidden form fields to pass the techstack information
echo '<input type="hidden" name="techstack_id" value="' . htmlspecialchars($techstackID ?? '') . '">';
echo '<input type="hidden" name="techstack_name" value="' . htmlspecialchars($techstackName ?? '') . '">';
echo '<input type="hidden" name="usecase_id" value="' . htmlspecialchars($usecaseID ?? '') . '">';
echo '<input type="hidden" name="fav_id" value="' . htmlspecialchars($favID ?? '') . '">';
echo '</form>';
