<!DOCTYPE html>
<html>

<head>
    <title>Inbox and Outbox Data</title>
    <link rel="stylesheet" href="includes/reset.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="includes/style.css">
</head>

<body>
    <?php
    include_once 'includes/dbh.inc.php';
    include_once 'includes/header.inc.php';
    include_once 'includes/session.inc.php';
    ?>

    <?php
    echo '<div class="main-div">';
    echo '<div>';
    echo '<div class="expand-bar-1">';
    echo '<strong>General Use Case Information</strong>';
    echo '</div>';
    echo '<br>';
    echo '<div class="notifications-container">';
    echo '<form method="post" action="#" enctype="multipart/form-data">';
    echo '<div class="outside-of-box">';
    echo '<div class="middle-side-inset submit-header"><strong>Which Asset(s) Does the Described Technologies Provide Solutions For?</strong></div>';
    //echo '<br>';


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

    echo '<div class="table-default">';
    if (mysqli_num_rows($result) > 0) { //??
        echo '<label class="box-titles" for="dropdown""><b>Asset Category</b> - <i>Asset Type</i> => Asset Component</label><br>';
        echo '<div class="box-areas">';
        echo '<select class="box" id="dropdown" name="assetInputs[]" multiple required>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . htmlspecialchars($row['acom_id']) . '">' . $row['asset_category_category'] . ' - ' . $row['asset_type'] . ' => ' . $row['component_name'] . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }
    echo '<p class="box-subtext" style="margin: 0;"><i>If applicable to all types of a certain category - such as Generation assets, for example - select all rows that include "Generation." If applicable only to specific asset types or components, select only those that apply.</i></p><br>';
    echo '</div>';
    echo '</div>';
    echo '<br>';
    echo '<div class="outside-of-box" style="padding-top: 1px;">';
    echo '<div class="middle-side-inset submit-header"><strong>What Job Will the Described Solution Do?</strong></div>';
    echo '<div class="table-default">';
    echo '<label class="box-titles">Workstream Name</label><br>'; //??
    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<input type="text" style="width: 100%;" name="workstreamNameS" id="workstreamNameS" placeholder="*Enter Name of Workstream..." required>';
    echo '</div>';
    echo '</div>';
    echo '<div class="box-subtext">';
    echo '<p style="margin: 0;"><i>(e.g., "Generator plant operator rounds.")</i></p><br>';
    echo '</div>';

    //echo '<div class="table-default">';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $workstreamNameS = isset($_POST["workstreamNameS"]) ? $_POST["workstreamNameS"] : "";
    }

    $sql = "SELECT workstream_trigger_id, workstream_trigger FROM workstream_trigger";
    $result = mysqli_query($conn, $sql);

    $workstreamTriggerS = "";
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles" for="dropdown">Workstream Trigger</label><br>';

        echo '<div class="box-areas">';
        echo '<select id="dropdown" name="workTriggerS" required>';
        echo '<option value="" disabled selected>Select One...</option>';
        echo '</div>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['workstream_trigger'] . '">' . $row['workstream_trigger'] . '</option>';
        }
        echo '</select>';
    }
    echo '<p class="box-subtext" style="margin: 0;"><i>(For example: what type of event causes the above workstream to be initiated?)</i></p><br>';

    $sql = "SELECT vc_id, vc_name FROM value_chain";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles" for="dropdown">Value Chain Components</label><br>';
        echo '<div class="box-areas">';
        echo '<select class="box" id="dropdown" name="valueChainS[]" multiple required>';

        while ($row = mysqli_fetch_assoc($result)) {
            //echo '<div>';
            echo '<option value="' . $row['vc_id'] . '">' . $row['vc_name'] . '</option>';
            echo '</div>';
        }
        echo '</select>';
    }
    //echo '</div>';
    echo '</div>';
    echo '<p class="box-subtext" style="margin: 0;"><i>(For example: which value chain component(s) can this use case add value to?)</i></p><br>';

    echo '<input type="checkbox" name="realClientS"></input>';
    echo '<label class="box-titles" for="checkbox">Has this solution been installed at a utility before?</label><br><br>';

    echo '<label class="box-titles">If yes, which one(s)?</label><br>';
    echo '<div class="box-areas">';
    echo '<input class="box" type="text" id="utilityNameS" name="utilityNameS" placeholder="*Enter Utilities(s)..."></input></div><br><br>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $utilityNameS = isset($_POST["utilityNameS"]) ? $_POST["utilityNameS"] : "";
    }

    echo '<label class="box-titles" for="pdfUpload">Upload Case Study (Optional).</label><br>';
    echo '<div class="box">';
    echo '<input type="file" id="pdfUpload" name="caseStudyS" accept=".pdf"><br><br>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<br><br>';
    echo '<div class="outside-of-box" style="padding-top: 1px;">';
    echo '<div class="middle-side-inset submit-header">';
    echo '<strong>What Benefits and/or KPIs Are Improved By the Described Solution?</strong>';
    echo '</div>';
    echo '<br>';

    $sql = "SELECT ben_id, benefit_name FROM benefit";
    $result = mysqli_query($conn, $sql);
    echo '<div class="table-default">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles" for="dropdown">Benefits</label><br>';
        echo '<div class="box-areas">';
        echo '<select class="box" id="dropdown" name="benefitS[]" multiple required>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['ben_id'] . '">' . $row['benefit_name'] . '</option>';
        }
        echo '</select>';
    }
    echo '</div>';
    echo '<p class="box-subtext" style="margin: 0;"><i>(Select all that apply).</i></p><br>';


    $sql = "SELECT kpi_id, kpi_name FROM kpi";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles" for="dropdown">Use Case KPIs</label><br>';
        echo '<div class="box-areas">';
        echo '<select class="box" id="dropdown" name="kpiS[]" multiple required>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['kpi_id'] . '">' . $row['kpi_name'] . '</option>';
        }
        echo '</select>';
    }
    echo '</div>';
    echo '<p class="box-subtext" style="margin: 0;"><i>(Select all that apply).</i></p><br>';
    echo '</div>';
    echo '</div>';
    echo '<br><br>';


    echo '<div class="outside-of-box" style="padding-top: 1px;">';
    echo '<div class="middle-side-inset submit-header">';
    echo '<strong>Describe the Components in the Solution Technology Stack</strong>';
    echo '</div>';
    echo '<div class="table-default">';

    echo '<label class="alt-label"></label>';

    echo '<div class="box-areas">';
    echo '<input class="box" type="text" id="useCaseNameS" name="useCaseNameS" placeholder="*Use Case Description..." required>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $useCaseNameS = isset($_POST["useCaseNameS"]) ? $_POST["useCaseNameS"] : "";
    }
    echo '</input>';
    echo '</div>';
    echo '<p class="box-subtext" style="margin: 0;"><i>A Use Case is described by adding its dominant technical capability to the constituency it serves. (e.g., "Voice-assisted work orders for generator plant operator rounds").</i></p><br>';

    echo '<label class="alt-label"></label>';
    echo '<div class="box-areas">';
    echo '<input class="box" type="text" id="techstackS" name="techstackS"" placeholder="*Enter Techstack Name..." required>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $techstackS = isset($_POST["techstackS"]) ? $_POST["useCaseNameS"] : "";
    }
    echo '</div>';
    echo '<p class="box-subtext" style="margin: 0;"><i>A techstack is described by adding its main technical capabilit(ies) to its key enabling products (e.g., "Voice-assisted work orders with Datch and Maximo.")</i></p><br>';
    echo '</div>';
    echo '</div>';
    echo '<br><br>';


    echo '<div class="outside-of-box" style="padding-top: 1px;">';
    echo '<div class="middle-side-inset submit-header">';
    echo '<strong>Miscellaneous Information</strong>';
    echo '</div>';
    //echo '<br>';

    $sql = "SELECT force_type_id, force_type_name FROM force_type";
    $result = mysqli_query($conn, $sql);

    echo '<div class="table-default">';
    echo '<div class="box-areas">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles" for="dropdown">What is the primary force driving this use case\'s adoption?</label><br>';
        echo '<select id="dropdown" name="drivingForceS" required>';
        echo '<option disabled selected>Select One...</option>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['force_type_id'] . '">' . $row['force_type_name'] . '</option>';
        }
        echo '</select>';
    }
    echo '</div>';
    echo '<div class="box-subtext">';
    echo '<p style="margin: 0; font-weight: normal;"><i>1. If adoption is driven by a legal or regulatory requirement, select "Regulatory."</i></p>';
    echo '<p style="margin: 0; font-weight: normal;"><i>2. If adoption is not legally required, but is motivated by a policy incentive of some kind, select "Policy Incentive."</i></p>';
    echo '<p style="margin: 0; font-weight: normal;"><i>3. If neither of the first two options apply, select "Economic."</i></p><br>';
    echo '</div>';

    $sql = "SELECT force_id, force_name FROM force_item";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles" for="dropdown">What are the Best Descriptions of the Driving Force Identified Above?</label><br>';

        echo '<div class="box-areas">';
        echo '<select class="box" id="dropdown" name="forceDescriptionS[]" multiple required>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['force_id'] . '">' . $row['force_name'] . '</option>';
        }
        echo '</select>';
        echo '<br><br>';
    }
    echo '</div>';
    $sql = "SELECT prog_id, program_name FROM program";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles" for="dropdown">What Utility Programs Could This Solution Feasibly Support? Select all that apply.</label><br>';
        echo '<div class="box-areas">';
        echo '<select class="box" id="dropdown" name="utilityProgramS[]" multiple required>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['prog_id'] . '">' . $row['program_name'] . '</option>';
        }
        echo '</select>';
        echo '<br><br>';
    }
    echo '</div>';

    echo '<label class="box-titles">Please Enter Any Miscellaneous Information That\'s Relevant and Was Not Captured By This Form.</label><br>';

    echo '<div class="box-areas">';
    echo '<input class="box" type="text" id="miscInfoS" name="miscInfoS"" placeholder="Additional Information...">';
    echo '</div>';
    echo '</div>';
    echo '<label class="alt-label"></label><br><br>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $miscInfoS = isset($_POST["miscInfoS"]) ? $_POST["miscInfoS"] : "";
    }

    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '<br>';
    echo '<p class="expand-bar-1"><b>Techstack Information</b></p>';
    echo '<br>';

    echo '<div class="notifications-container">';
    echo '<div class="outside-of-box">';

    echo '<p class="middle-side-inset submit-header"><b>Product 1 Information</b></p>';

    echo '<label class="alt-label"></label>';

    echo '<div class="table-default">';
    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">1. </label>';
    echo '<input class="text-input" type="text" id="product1S" name="product1S" placeholder="*Enter Product 1 Name...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product1S = isset($_POST["product1S"]) ? $_POST["product1S"] : "";
    }
    echo '<p class="indented-text" style="margin: 0;"><i>(e.g., "Maximo.")</i></p><br>';


    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">2. </label>';
    echo '<input class="text-input" type="text" id="vendorNameS1" name="vendorNameS1"" placeholder="*Enter Product 1 Vendor(s)...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vendorNameS1 = isset($_POST["vendorNameS1"]) ? $_POST["vendorNameS1"] : "";
    }
    echo '<p class="indented-text" style="margin: 0;"><i>(e.g., "IBM.") (Enter multiple if product belongs to a joint venture between two or more firms.)</i></p><br>';

    $sql = "SELECT tech_component_id, component_name FROM techstack_component";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">3. </label>';
        echo '<select class="text-input" id="dropdown" name="stack1dropS[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['tech_component_id'] . '">' . $row['component_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select>';
    }
    echo '</div>';
    echo '</div>';
    echo '<p class="indented-text" style="margin: 0;"><i>Which layer(s) in the techstack for THIS USE CASE best describe the product above?</i></p><br>';

    $sql = "SELECT inst_id, instantiation_name FROM instantiation";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">4. </label>';
    if (mysqli_num_rows($result) > 0) {
        echo '<select class="text-input" id="dropdown" name="comp1S[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['inst_id'] . '">' . $row['instantiation_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';
    echo '<p class="indented-text" style="margin: 0;"><i>Which additional components apply to this product, for this use case only?</i></p><br>';

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">5. </label>';
    echo '<input class="text-input" type="text" id="prod1MiscS" name="prod1MiscS" placeholder="*Enter Misc Info Not Captured Above...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prod1MiscS = isset($_POST["prod1MiscS"]) ? $_POST["prod1MiscS"] : "";
    }
    echo '<p class="indented-text" style="margin: 0;"><i>(Can include missing layers or components in the above lists, or other relevant factors.)</i></p>';
    echo '</div>';
    echo '</div>';

    echo '<div class="outside-of-box">';
    echo '<div>';
    echo '<br>';
    echo '</div>';
    echo '<p class="middle-side-inset submit-header"><b>Product 2 Information</b></p>';

    echo '<div class="table-default">';
    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">1. </label>';
    echo '<input class="text-input" type="text" id="product2S" name="product2S" placeholder="*Enter Product 2 Name..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product2S = isset($_POST["product2S"]) ? $_POST["product2S"] : "";
    }

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">2. </label>';
    echo '<input class="text-input" type="text" id="vendorNameS2" name="vendorNameS2" placeholder="*Enter Product 2 Vendor(s)..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vendorNameS2 = isset($_POST["vendorNameS2"]) ? $_POST["vendorNameS2"] : "";
    }

    $sql = "SELECT tech_component_id, component_name FROM techstack_component";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">3. </label>';
        echo '<select class="text-input" id="dropdown" name="stack2dropS[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['tech_component_id'] . '">' . $row['component_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    $sql = "SELECT inst_id, instantiation_name FROM instantiation";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">4. </label>';
        echo '<select class="text-input" id="dropdown" name="comp2S[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['inst_id'] . '">' . $row['instantiation_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">5. </label>';
    echo '<input class="text-input" type="text" id="prod2MiscS" name="prod2MiscS" placeholder="*Enter Misc Info Not Captured Above...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prod2MiscS = isset($_POST["prod2MiscS"]) ? $_POST["prod2MiscS"] : "";
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="outside-of-box">';
    echo '<div>';
    echo '<br>';
    echo '</div>';
    echo '<p class="middle-side-inset submit-header"><b>Product 3 Information</b></p>';

    echo '<div class="table-default">';
    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">1. </label>';
    echo '<input class="text-input" type="text" id="product3S" name="product3S" placeholder="*Enter Product 3 Name..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product3S = isset($_POST["product3S"]) ? $_POST["product3S"] : "";
    }

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">2. </label>';
    echo '<input class="text-input" type="text" id="vendorNameS3" name="vendorNameS3" placeholder="*Enter Product 3 Vendor(s)..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vendorNameS3 = isset($_POST["vendorNameS3"]) ? $_POST["vendorNameS3"] : "";
    }

    $sql = "SELECT tech_component_id, component_name FROM techstack_component";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">3. </label>';
        echo '<select class="text-input" id="dropdown" name="stack3dropS[]" multiple><br>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['tech_component_id'] . '">' . $row['component_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    $sql = "SELECT inst_id, instantiation_name FROM instantiation";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">4. </label>';
        echo '<select class="text-input" id="dropdown" name="comp3S[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['inst_id'] . '">' . $row['instantiation_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">5. </label>';
    echo '<input class="text-input" type="text" id="prod3MiscS" name="prod3MiscS" placeholder="*Enter Misc Info Not Captured Above...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prod3MiscS = isset($_POST["prod3MiscS"]) ? $_POST["prod3MiscS"] : "";
    }
    echo '</div>';

    echo '<div class="outside-of-box">';
    echo '<div>';
    echo '</div>';
    echo '<br>';
    echo '</div>';
    echo '<p class="middle-side-inset submit-header"><b>Product 4 Information</b></p>';

    echo '<div class="table-default">';
    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">1. </label>';
    echo '<input class="text-input" type="text" id="product4S" name="product4S" placeholder="*Enter Product 4 Name..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product4S = isset($_POST["product4S"]) ? $_POST["product4S"] : "";
    }

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">2. </label>';
    echo '<input class="text-input" type="text" id="vendorNameS4" name="vendorNameS4" placeholder="*Enter Product 4 Vendor(s)..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vendorNameS4 = isset($_POST["vendorNameS4"]) ? $_POST["vendorNameS4"] : "";
    }

    $sql = "SELECT tech_component_id, component_name FROM techstack_component";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">3. </label>';
        echo '<select class="text-input" id="dropdown" name="stack4dropS[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['tech_component_id'] . '">' . $row['component_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select>';
    }
    echo '</div>';
    echo '</div>';

    $sql = "SELECT inst_id, instantiation_name FROM instantiation";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">4. </label>';
        echo '<select class="text-input" id="dropdown" name="comp4S[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['inst_id'] . '">' . $row['instantiation_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">5. </label>';
    echo '<input class="text-input" type="text" id="prod4MiscS" name="prod4MiscS" placeholder="*Enter Misc Info Not Captured Above...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prod4MiscS = isset($_POST["prod4MiscS"]) ? $_POST["prod4MiscS"] : "";
    }
    echo '</div>';

    echo '<div class="outside-of-box">';
    echo '<div>';
    echo '</div>';
    echo '<br>';
    echo '</div>';
    echo '<p class="middle-side-inset submit-header"><b>Product 5 Information</b></p>';

    echo '<div class="table-default">';
    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">1. </label>';
    echo '<input class="text-input" type="text" id="product5S" name="product5S" placeholder="*Enter Product 5 Name..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product5S = isset($_POST["product5S"]) ? $_POST["product5S"] : "";
    }

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">2. </label>';
    echo '<input class="text-input" type="text" id="vendorNameS5" name="vendorNameS5" placeholder="*Enter Product 5 Vendor(s)..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vendorNameS5 = isset($_POST["vendorNameS5"]) ? $_POST["vendorNameS5"] : "";
    }

    $sql = "SELECT tech_component_id, component_name FROM techstack_component";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">3. </label>';
        echo '<select class="text-input" id="dropdown" name="stack5dropS[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['tech_component_id'] . '">' . $row['component_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    $sql = "SELECT inst_id, instantiation_name FROM instantiation";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">4. </label>';
        echo '<select class="text-input" id="dropdown" name="comp5S[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['inst_id'] . '">' . $row['instantiation_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">5. </label>';
    echo '<input class="text-input" type="text" id="prod5MiscS" name="prod5MiscS" placeholder="*Enter Misc Info Not Captured Above...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prod5MiscS = isset($_POST["prod5MiscS"]) ? $_POST["prod5MiscS"] : "";
    }
    echo '</div>';

    echo '<div class="outside-of-box">';
    echo '<div>';
    echo '</div>';
    echo '<br>';
    echo '</div>';
    echo '<p class="middle-side-inset submit-header"><b>Product 6 Information</b></p>';

    echo '<div class="table-default">';
    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">1. </label>';
    echo '<input class="text-input" type="text" id="product6S" name="product6S" placeholder="*Enter Product 6 Name..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product6S = isset($_POST["product6S"]) ? $_POST["product6S"] : "";
    }

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">2. </label>';
    echo '<input class="text-input" type="text" id="vendorNameS6" name="vendorNameS6" placeholder="*Enter Product 6 Vendor(s)..."><br>';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $vendorNameS6 = isset($_POST["vendorNameS6"]) ? $_POST["vendorNameS6"] : "";
    }

    $sql = "SELECT tech_component_id, component_name FROM techstack_component";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">3. </label>';
        echo '<select class="text-input" id="dropdown" name="stack6dropS[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['tech_component_id'] . '">' . $row['component_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    $sql = "SELECT inst_id, instantiation_name FROM instantiation";
    $result = mysqli_query($conn, $sql);

    echo '<div class="box-areas">';
    echo '<div class="box">';
    if (mysqli_num_rows($result) > 0) {
        echo '<label class="box-titles">4. </label>';
        echo '<select class="text-input" id="dropdown" name="comp6S[]" multiple>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['inst_id'] . '">' . $row['instantiation_name'] . '</option>';
        }
        echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
        echo '</select><br>';
    }
    echo '</div>';
    echo '</div>';

    echo '<div class="box-areas">';
    echo '<div class="box">';
    echo '<label class="box-titles">5. </label>';
    echo '<input class="text-input" type="text" id="prod6MiscS" name="prod6MiscS" placeholder="*Enter Misc Info Not Captured Above...">';
    echo '</div>';
    echo '</div>';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prod6MiscS = isset($_POST["prod6MiscS"]) ? $_POST["prod6MiscS"] : "";
    }
    echo '</div>';


    /*
echo '<p class="middle-side-inset submit-header"><b>Product 7 Information</b></p>';

echo '<div class="box-areas">';
echo '<div class="box">';
echo '<label class="box-titles">1. </label>';
echo '<input class="text-input" type="text" id="product7S" name="product7S" placeholder="*Enter Product 7 Name..."><br>';
echo '</div>';
echo '</div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product7S = isset($_POST["product7S"]) ? $_POST["product7S"] : "";
}

echo '<div class="box-areas">';
echo '<div class="box">';
echo '<label class="box-titles">2. </label>';
echo '<input class="text-input" type="text" id="vendorNameS7" name="vendorNameS7" placeholder="*Enter Product 7 Vendor(s)..."><br>';
echo '</div>';
echo '</div>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vendorNameS7 = isset($_POST["vendorNameS7"]) ? $_POST["vendorNameS7"] : "";
}

$sql = "SELECT tech_component_id, component_name FROM techstack_component";
$result = mysqli_query($conn, $sql);

echo '<div class="box-areas">';
echo '<div class="box">';
if (mysqli_num_rows($result) > 0) {
    echo '<label class="box-titles">3. </label>';
    echo '<select class="text-input" id="dropdown" name="stack7dropS[]" multiple>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['tech_component_id'] . '">' . $row['component_name'] . '</option>';
    }
    echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
    echo '</select><br>';
}
echo '</div>';
echo '</div>';

$sql = "SELECT inst_id, instantiation_name FROM instantiation";
$result = mysqli_query($conn, $sql);

echo '<div class="box-areas">';
echo '<div class="box">';
if (mysqli_num_rows($result) > 0) {
    echo '<label class="box-titles">4. </label>';
    echo '<select class="text-input" id="dropdown" name="comp7S[]" multiple>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['inst_id'] . '">' . $row['instantiation_name'] . '</option>';
    }
    echo '<option value="donotKnow">None Of These / Don\'t Know</option>';
    echo '</select><br>';
}
echo '</div>';
echo '</div>';

echo '<div class="box-areas">';
echo '<div class="box">';
echo '<label class="box-titles">5. </label>';
echo '<input class="text-input" type="text" id="prod7MiscS" name="prod7MiscS" placeholder="*Enter Misc Info Not Captured Above...">';
echo '</div>';
echo '</div>';
echo '<br>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prod7MiscS = isset($_POST["prod7MiscS"]) ? $_POST["prod7MiscS"] : "";
}
echo '</div>';
echo '</div>';
*/
    echo '</div>';

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    echo '<div style="text-align: right;">';
    echo '<button type="submit" name="submit">Submit</button>';
    echo '</div>';
    echo '</div>';
    echo '</form>';

    if (isset($_POST['submit'])) {
        $workstreamTriggerS = isset($_POST['workTriggerS']) ? $_POST['workTriggerS'] : '';
        $selectedAssets = isset($_POST['assetInputs']) ? $_POST['assetInputs'] : [];
        $assetsCSV = implode(',', $selectedAssets);
        $valueChain = isset($_POST['valueChainS']) ? $_POST['valueChainS'] : [];
        $valueChainCSV = implode(',', $valueChain);
        $realClientS = $selectedAssets = isset($_POST['realClientS']) ? $_POST['realClientS'] : '';
        $benefits = isset($_POST['benefitS']) ? $_POST['benefitS'] : [];
        $benefitsCSV = implode(',', $benefits);
        $kpis = isset($_POST['kpiS']) ? $_POST['kpiS'] : [];
        $kpisCSV = implode(',', $kpis);
        $caseStudyContent = '';

        if (isset($_FILES['caseStudyS']) && $_FILES['caseStudyS']['size'] > 0) {
            $caseStudyContent = file_get_contents($_FILES['caseStudyS']['tmp_name']);
        }

        $drivingForceS = isset($_POST['drivingForceS']) ? $_POST['drivingForceS'] : '';
        $forceDescriptionS = isset($_POST['forceDescriptionS']) ? $_POST['forceDescriptionS'] : [];
        $forceDescriptionSCSV = implode(',', $forceDescriptionS);
        $utilityProgramS = isset($_POST['utilityProgramS']) ? $_POST['utilityProgramS'] : [];
        $utilityProgramSCSV = implode(',', $utilityProgramS);
        $stack1dropS = isset($_POST['stack1dropS']) ? $_POST['stack1dropS'] : [];
        $stack1dropSCSV = implode(',', $stack1dropS);
        $comp1S = isset($_POST['comp1S']) ? $_POST['comp1S'] : [];
        $comp1SCSV = implode(',', $comp1S);
        $stack2dropS = isset($_POST['stack2dropS']) ? $_POST['stack2dropS'] : [];
        $stack2dropSCSV = implode(',', $stack2dropS);
        $comp2S = isset($_POST['comp2S']) ? $_POST['comp2S'] : [];
        $comp2SCSV = implode(',', $comp2S);
        $stack3dropS = isset($_POST['stack3dropS']) ? $_POST['stack3dropS'] : [];
        $stack3dropSCSV = implode(',', $stack3dropS);
        $comp3S = isset($_POST['comp3S']) ? $_POST['comp3S'] : [];
        $comp3SCSV = implode(',', $comp3S);
        $stack4dropS = isset($_POST['stack4dropS']) ? $_POST['stack4dropS'] : [];
        $stack4dropSCSV = implode(',', $stack4dropS);
        $comp4S = isset($_POST['comp4S']) ? $_POST['comp4S'] : [];
        $comp4SCSV = implode(',', $comp4S);
        $stack5dropS = isset($_POST['stack5dropS']) ? $_POST['stack5dropS'] : [];
        $stack5dropSCSV = implode(',', $stack5dropS);
        $comp5S = isset($_POST['comp5S']) ? $_POST['comp5S'] : [];
        $comp5SCSV = implode(',', $comp5S);
        $stack6dropS = isset($_POST['stack6dropS']) ? $_POST['stack6dropS'] : [];
        $stack6dropSCSV = implode(',', $stack6dropS);
        $comp6S = isset($_POST['comp6S']) ? $_POST['comp6S'] : [];
        $comp6SCSV = implode(',', $comp6S);
        $stack7dropS = isset($_POST['stack7dropS']) ? $_POST['stack7dropS'] : [];
        $stack7dropSCSV = implode(',', $stack7dropS);
        $comp7S = isset($_POST['comp7S']) ? $_POST['comp7S'] : [];
        $comp7SCSV = implode(',', $comp7S);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $sql = "INSERT INTO raw_user_use_case_upload (created_at, session_id, acom_id, workstream_name, workstream_trigger, value_chain, real_client, client_utilities, case_study, benefits, kpis, solution_description, techstack_name, driving_force, force_description, utility_program, misc_info, product1_name, product1_vendors, product1_layers, product1_components, product2_name, product2_vendors, product2_layers, product2_components, product3_name, product3_vendors, product3_layers, product3_components, product4_name, product4_vendors, product4_layers, product4_components, product5_name, product5_vendors, product5_layers, product5_components, product6_name, product6_vendors, product6_layers, product6_components, product7_name, product7_vendors, product7_layers, product7_components) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssssssssssssssssssssssssssssssssssssssssssss", $dateTime, $username, $assetsCSV, $workstreamNameS, $workstreamTriggerS, $valueChainCSV, $realClientS, $utilityNameS, $caseStudyContent, $benefitsCSV, $kpisCSV, $useCaseNameS, $techstackS, $drivingForceS, $forceDescriptionSCSV, $utilityProgramSCSV, $miscInfoS, $product1S, $vendorNameS1, $stack1dropSCSV, $comp1SCSV, $product2S, $vendorNameS2, $stack2dropSCSV, $comp2SCSV, $product3S, $vendorNameS3, $stack3dropSCSV, $comp3SCSV, $product4S, $vendorNameS4, $stack4dropSCSV, $comp4SCSV, $product5S, $vendorNameS5, $stack5dropSCSV, $comp5SCSV, $product6S, $vendorNameS6, $stack6dropSCSV, $comp6SCSV, $product7S, $vendorNameS7, $stack7dropSCSV, $comp7SCSV);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }



    /*
$sql = "SELECT at_id, asset_type FROM asset_type";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    echo '<label for="dropdown">Asset Type</label><br>';
    echo '<select id="dropdown" name="selected_options[]" multiple>';

    while($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['at_id'] . '">' . $row['asset_type'] . '</option>';
    }

    echo '</select>';
    echo '<br><br>';

}

$sql = "SELECT acom_id, component_name FROM asset_component";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    echo '<label for="dropdown">Asset Component</label><br>';
    echo '<select id="dropdown" name="selected_options[]" multiple>';

    while($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['acom_id'] . '">' . $row['component_name'] . '</option>';
    }

    echo '</select>';
    echo '<br><br><br><br>';
}



    // use html special characters to echo text back to the /submitted.php page, as seen below.
    echo "Textbox Value: " . htmlspecialchars($vendorNameS7) .'<br><br>';

*/
    ?>
</body>

</html>