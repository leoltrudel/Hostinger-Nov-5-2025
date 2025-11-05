<!DOCTYPE html>
<html>

<head>
    <title>Inbox and Outbox Data</title>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body class="flex-body">
    <?php
    include_once 'includes/header.inc.php';
    ?>

    <section class="center-form">
        <form class="notifications-container" action="includes/login.inc.php" method="post">
            <h2>Login</h2>
            <input type="text" name="useruid" placeholder="Username/Email..."><br>
            <input type="password" name="pwd" placeholder="Password..."><br>
            <button type="submit" name="submit">Login</button>
            <br><br>
            <?php
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "emptyinput") {
                    echo "<p>Fill in all fields!</p>";
                } else if ($_GET["error"] == "wronglogin") {
                    echo "<p>Incorrect login information!</p>";
                }
            }
            ?>
        </form>
    </section>
</body>

</html>