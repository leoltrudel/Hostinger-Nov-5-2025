<?php
require_once 'includes/config_session.inc.php';
require_once 'includes/signup_view.inc.php';
require_once 'includes/login_view.inc.php';

<?php
include_once 'includes/dbh.inc.php';
include_once 'includes/header.inc.php';

//echo '<form action="submitted.php" method="post" enctype="multipart/form-data">';
echo '<form method="post" action="#" enctype="multipart/form-data">'; // Form added for submission
echo '<p><b>What Assets Does the Described Solution Serve?</b></p><br>';

$sql = "SELECT DISTINCT asset_category.ac_id, asset_category_category, asset_type.at_id, asset_type, acom_id, component_name
FROM asset_category
INNER JOIN asset_type
ON asset_category.ac_id = asset_type.ac_id
INNER JOIN asset_component
ON asset_type.at_id = asset_component.asset_type_id
INNER JOIN join_use_case_asset_component
ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
INNER JOIN use_case
ON join_use_case_asset_component.use_case_id = use_case.uc_id
ORDER BY asset_category.ac_id, asset_type";
$result = mysqli_query($conn, $sql);

$assets = [];

if (mysqli_num_rows($result) > 0) {
    echo '<label for="dropdown">Asset Category - Asset Type => Asset Component</label><br>';
    echo '<select id="dropdown" style="width: 500px;" name="assetInputs[]" multiple>';

    while ($row = mysqli_fetch_assoc($result)) {
        // Output each option in the dropdown
        echo '<option value="' . htmlspecialchars($row['acom_id']) . '">' . $row['asset_category_category'] . ' - ' . $row['asset_type'] . ' => ' . $row['component_name'] . '</option>';
    }

    echo '</select>';
    echo '<br><br><br>';
}

echo '<p><b>What Job Will the Described Solution Do?</b></p><br>';

echo '<label>Workstream Name</label><br>';
echo '<input type="text" name="workstreamNameS" id="workstreamNameS" style="width: 500px;" placeholder="*Enter Name of Workstream..."><br><br>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the value of the textbox
    $workstreamNameS = isset($_POST["workstreamNameS"]) ? $_POST["workstreamNameS"] : "";

    // Use the value as needed
    echo "Textbox Value: " . htmlspecialchars($workstreamNameS);
}

$sql = "SELECT workstream_trigger_id, workstream_trigger FROM workstream_trigger";
$result = mysqli_query($conn, $sql);

$workstreamTriggerS = ""; // Initialize as a single value


if(mysqli_num_rows($result) > 0) {
    echo '<label for="dropdown">Workstream Trigger</label><br>';
    echo '<select id="dropdown" style="width: 500px;" name="workTriggerS" placeholder="test">';

    while($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['workstream_trigger'] . '">' . $row['workstream_trigger'] . '</option>';
    }

    echo '</select>';
    echo '<br><br>';
}

$sql = "SELECT vc_id, vc_name FROM value_chain";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    echo '<label for="dropdown">Value Chain Components</label><br>';
    echo '<select id="dropdown" style="width: 500px;" name="valueChainS[]" multiple>';

    while($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['vc_id'] . '">' . $row['vc_name'] . '</option>';
    }

    echo '</select>';
    echo '<br><br>';

}

echo '<input type="checkbox" name="realClientS"></input>';
echo '<label for="checkbox">Has this solution been installed at a utility before?</label><br><br>';

echo '<label>If yes, which one(s)?</label><br>';
echo '<input type="text" id="utilityNameS" name="utilityNameS" style="width: 500px;" placeholder="*Enter Utilities(s)..."><br><br>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the value of the textbox
    $utilityNameS = isset($_POST["utilityNameS"]) ? $_POST["utilityNameS"] : "";

    // Use the value as needed
    echo "Textbox Value: " . htmlspecialchars($utilityNameS) .'<br><br>';
}

echo '<label for="pdfUpload">Upload Case Study (Optional).</label><br>';
echo '<input type="file" id="pdfUpload" name="caseStudyS" accept=".pdf"><br><br>';
//echo '<button type="submit">Upload</button>';
echo '<br><br>';
/*
echo '<form method="post" action="#" enctype="multipart/form-data">';
echo '<label for="pdfUpload">Upload Case Study (Optional).</label><br>';
echo '<input type="file" id="pdfUpload" name="caseStudyS" accept=".pdf"><br><br>';
echo '<button type="submit">Upload</button>';
echo '</form>';
echo '<br><br>';
*/


echo '<b>What Benefits and/or KPIs Are Improved By the Described Solution?</b><br><br>';

$sql = "SELECT ben_id, benefit_name FROM benefit";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    echo '<label for="dropdown">Benefits</label><br>';
    echo '<select id="dropdown" style="width: 500px;" name="benefitS[]" multiple>';

    while($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['ben_id'] . '">' . $row['benefit_name'] . '</option>';
    }

    echo '</select>';
    echo '<br><br>';

}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<button type="submit" name="submit">Submit</button>'; // Submit button added
echo '</form>'; // Form closing tag added

if(isset($_POST['submit'])) {
    $workstreamTriggerS = isset($_POST['workTriggerS']) ? $_POST['workTriggerS'] : '';

    // Get the selected assets
    $selectedAssets = isset($_POST['assetInputs']) ? $_POST['assetInputs'] : [];

    // Convert the selected assets array to a CSV string
    $assetsCSV = implode(',', $selectedAssets);

    // Collect selected value chain components
    $valueChain = isset($_POST['valueChainS']) ? $_POST['valueChainS'] : [];

    // Convert the selected value chain components array to a CSV string
    $valueChainCSV = implode(',', $valueChain);

    $realClientS = $selectedAssets = isset($_POST['realClientS']) ? $_POST['realClientS'] : '';

    // Collect selected value chain components
    $benefits = isset($_POST['benefitS']) ? $_POST['benefitS'] : [];

    // Convert the selected value chain components array to a CSV string
    $benefitsCSV = implode(',', $benefits);


    // Handle PDF file upload
    $caseStudyContent = '';
    if (isset($_FILES['caseStudyS']) && $_FILES['caseStudyS']['size'] > 0) {
        // Get the content of the uploaded PDF file
        $caseStudyContent = file_get_contents($_FILES['caseStudyS']['tmp_name']);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Your existing code...
    

    
        // Use prepared statements to avoid SQL injection
        $sql = "INSERT INTO raw_user_use_case_upload (created_at, acom_id, workstream_name, workstream_trigger, value_chain, real_client, client_utilities, case_study, benefits) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sssssssss", $dateTime, $assetsCSV, $workstreamNameS, $workstreamTriggerS, $valueChainCSV, $realClientS, $utilityNameS, $caseStudyContent, $benefitsCSV);

        // Execute statement
        mysqli_stmt_execute($stmt);

        // Close statement
        mysqli_stmt_close($stmt);
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>


</head>

<body>

<h3>
    <?php
    output_username();
    ?>
</h3>

    <h3>Login</h3>

    <form action="includes/login.inc.php" method="post">
        <input type="text" name="username" placeholder="Username"><br>
        <input type="password" name="pwd" placeholder="Password"><br>
        <button>Login</button>
    </form>

    <?php
    check_login_errors();
    ?>

    <h3>Signup</h3>
    <form action="includes/signup.inc.php" method="post">
        <?php
        signup_inputs()
        ?>
        <br>
        <button>Signup</button>
    </form>

    <?php
    check_signup_errors();
    ?>

<h3>Logout</h3>

<form action="includes/logout.inc.php" method="post">
    <button>Logout</button>
</form>

</body>

</html>


