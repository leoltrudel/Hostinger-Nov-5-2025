<?php
//require 'session.inc.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title></title>
</head>

<body>
    <nav>
        <div class="wrapper">
            <a>
                <?php
                if (isset($_SESSION["useruid"])) {
                    echo "<a href='index.php'>Home</a> | ";
                    echo "<a href='profile.php'>My Profile</a> | ";
                    echo "<a href='search.php'>Search</a> | ";
                    echo "<a href='favorites.php'>Favorites</a> | ";
                    echo "<a href='inbox.php'>Inbox</a> | ";
                    echo "<a href='submitinfo.php'>Submit Info</a> | ";
                    echo "<a href='includes/logout.inc.php'>Log Out</a>";
                } else {
                    echo "<a href='signup.php'>Signup</a> | ";
                    echo "<a href='login.php'>Login</a>";
                }
                ?>
            </a>
        </div>
    </nav>
</body>

</html>