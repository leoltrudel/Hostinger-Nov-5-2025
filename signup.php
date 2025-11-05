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
        <form class="notifications-container" action="includes/signup.inc.php" method="post">
            <h2>Signup</h2>
            <input type="text" name="name" placeholder="Full name..."><br>
            <input type="text" name="email" placeholder="Email..."><br>
            <input type="text" name="useruid" placeholder="Username..."><br>
            <input type="password" name="pwd" placeholder="Password..."><br>
            <input type="password" name="pwdrepeat" placeholder="Repeat password..."><br>
            <button type="submit" name="submit">Sign up</button>
            <br><br>
            <?php
            if (isset($_GET["error"])) {
                if ($_GET["error"] == "emptyinput") {
                    echo "<p>Fill in all fields!</p>";
                } else if ($_GET["error"] == "invaliduid") {
                    echo "<p>Please choose a username containing only letters and numbers!</p>";
                } else if ($_GET["error"] == "invalidemail") {
                    echo "<p>Please enter a valid email address!</p>";
                } else if ($_GET["error"] == "passwordmismatch") {
                    echo "<p>Please make sure your passwords match!</p>";
                } else if ($_GET["error"] == "stmtfailed") {
                    echo "<p>Something went wrong...try again</p>";
                } else if ($_GET["error"] == "usernametaken") {
                    echo "<p>This username is already taken.</p>";
                } else if ($_GET["error"] == "none") {
                    echo "<p>You have signed up!</p>";
                }
            }
            ?>
        </form>
    </section>
</body>

</html>