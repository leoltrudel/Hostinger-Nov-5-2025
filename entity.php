<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    require_once 'includes/dbh.inc.php';
    require_once 'includes/header.inc.php';
    include_once 'includes/session.inc.php';

    if (!isset($_SESSION["id"])) {
        echo 'Please login to access this page.';
        die();
    }

    if (isset($_GET['entity'])) {
        $entityID = $_GET['entity'];
    } else {
        echo "No value found.";
        die();
    }

    $sql = "SELECT * FROM company WHERE com_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $entityID);
    $stmt->execute();
    $stmt->bind_result($entityID, $createdAt, $entityName, $entityEmail, $entityPhone, $entityDesc, $entityWebsite, $logo);
    $stmt->fetch();
    $stmt->close();
    ?>
    <br>
    <div class="notifications-container">
        <?php


        echo '<strong>Company Name</strong><br>';
        echo $entityName . '<br><br>';

        echo '<strong>Company Description</strong><br>';
        if (!$entityDesc) {
            echo '<i>(No description provided).</i><br><br>';
        } else {
            echo $entityDesc . '<br><br>';
        }

        echo '<strong>Email</strong><br>';
        if (!$entityEmail) {
            echo '<i>(No email provided).</i><br><br>';
        } else {
            echo $entityEmail . '<br><br>';
        }

        echo '<strong>Phone Number</strong><br>';
        if (!$entityPhone) {
            echo '<i>(No phone number provided).</i><br><br>';
        } else {
            echo $entityPhone . '<br><br>';
        }

        echo '<strong>Website</strong><br>';
        if (!$entityWebsite) {
            echo '<i>(No website provided).</i><br><br>';
        } else {
            echo '<a href="' . $entityWebsite . '" target="_blank">' . $entityWebsite . '</a><br><br>';
        }

        $conn->close();
        ?>
    </div>

    <body>

</html>