<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    include_once 'includes/header.inc.php';
    ?>
    <div class="parent-class">
        <div class="middle-side">
            <div class="middle-side-main-body">
                <div>
                    <?php include_once 'includes/ferctables.inc.php'; ?>
                </div>
            </div>
        </div>
        <div class="left-side">
            <div class="left-inset-panel"><br>
                <?php
                if (isset($_SESSION["useruid"])) {
                    echo "<p>Hello there, " . $_SESSION["username"] . "!</p>";
                } else {
                    header("location: /query_tests/signup.php");
                    exit();
                }
                echo '<br>';
                include_once 'search.php';
                ?>
            </div>
        </div>
    </div>
</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Select the parent and the two divs to be swapped
        const parent = document.querySelector('.parent-class');
        const leftSide = document.querySelector('.left-side');
        const middleSide = document.querySelector('.middle-side');

        // Use flex order to switch their positions
        leftSide.style.order = "1";
        middleSide.style.order = "2";

        // Make sure parent has display: flex;
        parent.style.display = "flex";
    });
</script>