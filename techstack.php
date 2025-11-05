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

    if (isset($_GET['techstack'])) {
        $techstackID = $_GET['techstack'];
    } else {
        echo "No techstack ID provided.";
        die();
    }

    $sql = "SELECT * FROM techstack_name WHERE tech_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $techstackID);
    $stmt->execute();
    $stmt->bind_result($techstackID, $createdAt, $techName, $verified);
    $stmt->fetch();
    $stmt->close();
    ?>
    <br>
    <div class=notifications-container>
        <?php
        echo '<strong>Techstack Name</strong><br>';
        echo $techName . '<br>';

        $conn->close();
        ?>
    </div>
</body>

</html>