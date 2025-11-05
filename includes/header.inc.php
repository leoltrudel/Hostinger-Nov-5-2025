<?php
require 'session.inc.php';

// Fetch the logo image
$sql = "SELECT misc_asset FROM misc_image_assets WHERE unique_id = 1";
$result = mysqli_query($conn, $sql);
if (!empty($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $logo = $row['misc_asset'];
    }
}

// Fetch the name asset (assuming it's another image)
$sql = "SELECT misc_asset FROM misc_image_assets WHERE unique_id = 2";
$result = mysqli_query($conn, $sql);
if (!empty($result)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row['misc_asset'];
    }
}

$logoAsset = '';
$nameAsset = '';

$logoRef = !empty($_SESSION["id"]) ? 'index.php' : 'signup.php';

// Encode and display the logo
if (!empty($logo)) {
    $encodedLogo = base64_encode($logo);
    $logoAsset = '<a href="' . $logoRef . '"><img src="data:image/png;base64,' . $encodedLogo . '"></a>';
} else {
    $logoAsset = '<i>(No logo available).</i>';
}

// Encode and display the name asset
if (!empty($name)) {
    $encodedName = base64_encode($name);
    $nameAsset = '<a href="' . $logoRef . '"><img class="logo-name" src="data:image/png;base64,' . $encodedName . '"></a>'; // Correctly wrapped in <a>
} else {
    $nameAsset = '<i>(No name image available).</i>';
}
?>

<div style="background-color: white;">
    <nav>
        <!-- Logo on the left, aligned with navigation -->
        <div class="logo">
            <?php echo $logoAsset; ?> <!-- Logo asset -->
            <?php echo $nameAsset; ?> <!-- Name asset -->
        </div>

        <!-- Navigation links on the right of the logo -->
        <div class="header-links">
            <?php if (isset($_SESSION["useruid"])) { ?>
                <a href="index.php">Home / Search</a>
                <a href="profile.php?id=<?php echo $userid; ?>">My Profile</a>
                <a href="favorites.php">Favorites</a>
                <a href="notifications.php">Notifications</a>
                <?php include_once 'browse.inc.php'; ?> <!-- Assuming this outputs valid HTML -->
                <a href="submitinfo.php">Submit Info</a>
                <a href="includes/logout.inc.php">Log Out</a>
            <?php } else { ?>
                <a href="signup.php">Signup</a>
                <a href="login.php">Login</a>
            <?php } ?>

        </div>
    </nav>
</div>