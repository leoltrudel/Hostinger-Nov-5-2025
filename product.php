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

    if (!isset($_SESSION["id"])) {
        echo 'Please login to access this page.';
        die();
    }

    if (isset($_GET['product'])) {
        $productID = $_GET['product'];
    } else {
        echo "No product ID found.";
        die();
    }

    $sql = "SELECT prod_id, created_at, product_name, product_description, screenshot FROM product WHERE prod_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);
    $stmt->execute();

    // Bind only the columns you selected
    $stmt->bind_result($productID, $createdAt, $prodName, $prodDesc, $screenshot);

    $stmt->fetch();
    $stmt->close();


    echo '<div>'; // Start new wrapper div with light blue background

    echo '<div class="container even-container">';
    echo '<div class="box left-box" style="background-color: white;">';
    echo '<div class="expand-bar-1"><strong>Product Overview</strong></div>';
    echo '<div class="shaded-box">';
    // Content for the left container
    echo '<strong>Product Name</strong><br>';
    echo $prodName . '<br><br>';

    echo '<strong>Product Description</strong><br>';
    if (!$prodDesc) {
        echo '<i>(No product description provided).</i><br><br>';
    } else {
        echo $prodDesc;
    }

    $sql = "SELECT com_name, com_id, logo FROM company
INNER JOIN join_product_company
ON company.com_id = join_product_company.company_id
INNER JOIN product
ON join_product_company.product_id = product.prod_id
WHERE prod_id = $productID";

    $result = mysqli_query($conn, $sql);

    $company = [];
    $comLogo = [];

    if (!empty($result)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $company[] = $row['com_name'];
            $comLogo[] = $row['logo'];  // Store the raw logo data
            $comID[] = $row['com_id'];
        }
    }

    echo '<br><br>';
    // Start output
    echo '<strong>Company</strong><br>';

    // Echo out company names, separated by commas
    echo '<a href="entity.php?entity=' . implode(', ', array_map('htmlspecialchars', $comID)) . '">' . implode(', ', array_map('htmlspecialchars', $company)) . '</a><br><br>';

    // Start output for logos
    echo '<strong>Logo</strong><br>';

    foreach ($company as $index => $name) {
        $logo = base64_encode($comLogo[$index]);  // Encode each logo individually

        // Output each logo
        echo '<a href="entity.php?entity=' . implode(', ', array_map('htmlspecialchars', $comID)) . '"><img src="data:image/svg+xml;base64,' . $logo . '" style="width: 100px; height: auto; margin-right: 10px;"></a>';
    }

    echo '<br><br>';
    echo '<strong>' . $prodName . ' Screenshot</strong>';
    echo '<br>';
    // Check if the screenshot is not empty
    if (!empty($screenshot)) {
        // Base64 encode the screenshot and display it as an image
        $encodedScreenshot = base64_encode($screenshot);
        echo '<img src="data:image/png;base64,' . $encodedScreenshot . '" style="max-width: 75%; height: auto;">';
    } else {
        echo '<i>(No screenshot available).</i>';
    }
    echo '</div>';
    echo '</div>';
    echo '<div class="box right-box" style="background-color: white;">';

    // Content for the right container
    ?>
    <div class="tab-box">
        <div class="tabs">
            <input type="radio" id="tab1" name="tab-control" checked>
            <input type="radio" id="tab2" name="tab-control">
            <input type="radio" id="tab3" name="tab-control">

            <ul>
                <li title="Tab 1">
                    <label for="tab1" role="button">
                        <span>Applications</span>
                    </label>
                </li>
                <li title="Tab 2">
                    <label for="tab2" role="button">
                        <span>Technical</span>
                    </label>
                </li>
                <li title="Tab 3">
                    <label for="tab3" role="button">
                        <span>Economic</span>
                    </label>
                </li>
            </ul>

            <!--   <div class="slider">
                <div class="indicator"></div>
            </div> -->

            <div class="content"> <!-- 1 -->
                <section>
                    <?php
                    $sql = "SELECT asset_category.ac_id, asset_category.asset_category_category, asset_type.at_id, asset_type.asset_type, asset_component.acom_id, asset_component.component_name
                FROM asset_category
                INNER JOIN asset_type
                ON asset_category.ac_id = asset_type.ac_id
                INNER JOIN asset_component
                ON asset_type.at_id = asset_component.asset_type_id
                INNER JOIN join_use_case_asset_component
                ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
                INNER JOIN use_case
                ON join_use_case_asset_component.use_case_id = use_case.uc_id
                INNER JOIN join_use_case_techstack
                ON use_case.uc_id = join_use_case_techstack.use_case_id
                INNER JOIN techstack_name
                ON join_use_case_techstack.techstack_id = techstack_name.tech_id
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";

                    $result = mysqli_query($conn, $sql);

                    $nestedData = [];

                    // Step 2: Group the data
                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $acID = $row['ac_id'];
                            $acName = $row['asset_category_category'];
                            $atID = $row['at_id'];
                            $atName = $row['asset_type'];
                            $acomID = $row['acom_id'];
                            $acomName = $row['component_name'];

                            // Group the data into nested arrays
                            if (!isset($nestedData[$acName])) {
                                $nestedData[$acName] = [];
                            }

                            if (!isset($nestedData[$acName][$atName])) {
                                $nestedData[$acName][$atName] = [];
                            }

                            $nestedData[$acName][$atName][] = $acomName;
                        }
                    }
                    if (!empty($acID)) {
                        echo '<h4>Applicable Assets</h4>';
                    }
                    ?>

                    <!-- Step 3: Echo the nested structure -->
                    <div id="nested-data"> <!-- 2 -->
                        <?php foreach ($nestedData as $acName => $atArray) : ?>
                            <div class="category"> <!-- 3 -->
                                <span class="toggle-button" onclick="toggleVisibility(this)">[+]</span>
                                <strong><?php echo $acName; ?></strong><br>

                                <div class="type hidden"> <!-- 4 -->
                                    <?php foreach ($atArray as $atName => $acomArray) : ?>
                                        <div class="sub-category"> <!-- 5 -->
                                            <span class="toggle-button indented" onclick="toggleVisibility(this)">[+]</span>
                                            <strong><?php echo $atName; ?></strong><br>

                                            <div class="components hidden"> <!-- 6 -->
                                                <?php foreach ($acomArray as $acomName) : ?>
                                                    &emsp;&emsp;- <?php echo $acomName; ?><br>
                                                <?php endforeach; ?>
                                            </div> <!-- 5 -->
                                        </div> <!-- 4 -->
                                    <?php endforeach; ?>
                                </div> <!-- 3 -->
                            </div> <!-- 2 -->
                        <?php endforeach; ?>
                    </div> <!-- 1 -->
                    <?php
                    if (!empty($acID)) {
                        echo '<br>';
                    }
                    $sql = "SELECT *
                FROM infrastructure_type
                INNER JOIN asset_component
                ON infrastructure_type.infra_type_id = asset_component.infrastructure_type_id
                INNER JOIN join_use_case_asset_component
                ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
                INNER JOIN use_case
                ON join_use_case_asset_component.use_case_id = use_case.uc_id
                INNER JOIN join_use_case_techstack
                ON use_case.uc_id = join_use_case_techstack.use_case_id
                INNER JOIN techstack_name
                ON join_use_case_techstack.techstack_id = techstack_name.tech_id
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";

                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $infraID[] = $row['infra_type_id'];
                            $infraName[] = $row['infra_type_name'];
                        }
                    }

                    if (!empty($infraID)) {
                        echo '<h4>Applicable Infrastructure</h4>';
                        $infraName = array_unique($infraName);
                        foreach ($infraName as $value) {
                            echo $value;
                            echo '<br>';
                        }
                        echo '<br>';
                    }

                    //echo '<br>';
                    $sql = "SELECT *
                FROM value_chain
                INNER JOIN join_use_case_value_chain
                ON value_chain.vc_id = join_use_case_value_chain.value_chain_id
                INNER JOIN use_case
                ON join_use_case_value_chain.use_case_id = use_case.uc_id
                INNER JOIN join_use_case_techstack
                ON use_case.uc_id = join_use_case_techstack.use_case_id
                INNER JOIN techstack_name
                ON join_use_case_techstack.techstack_id = techstack_name.tech_id
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";

                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $vcID[] = $row['vc_id'];
                            $vcName[] = $row['vc_name'];
                        }
                    }

                    if (!empty($vcID)) {
                        echo '<h4>Applicable Value Chain Segments</h4>';
                        $vcName = array_unique($vcName);
                        foreach ($vcName as $value) {
                            echo $value;
                            echo '<br>';
                        }
                        echo '<br>';
                    }


                    //echo '<br>';
                    $sql = "SELECT *
                FROM use_case
                INNER JOIN join_use_case_techstack
                ON use_case.uc_id = join_use_case_techstack.use_case_id
                INNER JOIN techstack_name
                ON join_use_case_techstack.techstack_id = techstack_name.tech_id
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";

                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $ucID[] = $row['uc_id'];
                            $ucName[] = $row['use_case_name'];
                        }
                    }
                    if (!empty($ucID)) {
                        echo '<h4>Known Use Cases</h4>';
                        $ucName = array_unique($ucName);
                        foreach ($ucName as $value) {
                            echo $value;
                            echo '<br>';
                        }
                        echo '<br>';
                    }
                    ?>

                    <!-- Step 4: Add the necessary CSS -->
                    </p>
                </section>
                <section>

                    <?php
                    echo '<h4>Relevant Components</h4>';
                    $sql = "SELECT *
                FROM techstack_component
                INNER JOIN techstack_summary
                ON techstack_component.tech_component_id = techstack_summary.comp_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";
                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $compID[] = $row['tech_component_id'];
                            $compName[] = $row['component_name'];
                        }
                    }
                    if (!empty($compID)) {
                        $compName = array_unique($compName);
                        foreach ($compName as $value) {
                            echo $value;
                            echo '<br>';
                        }
                        echo '<br>';
                    }


                    echo '<h4>Applicable Methods</h4>';
                    $sql = "SELECT * 
                FROM instantiation
                INNER JOIN techstack_summary
                ON instantiation.inst_id = techstack_summary.method_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";
                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $instID[] = $row['inst_id'];
                            $instName[] = $row['instantiation_name'];
                        }
                    }

                    $instName = array_unique($instName);
                    foreach ($instName as $value) {
                        echo $value;
                        echo '<br>';
                    }
                    echo '<br>';
                    echo '<h4>Known Techstacks</h4>';
                    $comID = implode(',', $comID);
                    $sql = "SELECT techstack_name.tech_id, techstack_name
                FROM techstack_name
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                INNER JOIN join_product_company
                ON product.prod_id = join_product_company.product_id
                WHERE product.prod_id = $productID";
                    $comID = explode(',', $comID);

                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $stackID[] = $row['tech_id'];
                            $stackName[] = $row['techstack_name'];
                        }
                    }

                    $stackName = array_unique($stackName);
                    foreach ($stackName as $value) {
                        echo $value;
                        echo '<br>';
                    }
                    ?>
                </section>
                <section>

                    <?php
                    echo '<h4>Known Benefits</h4>';
                    $sql = "SELECT * 
                FROM benefit
                INNER JOIN join_use_case_benefit
                ON benefit.ben_id = join_use_case_benefit.benefit
                INNER JOIN use_case
                ON join_use_case_benefit.use_case = use_case.uc_id
                INNER JOIN join_use_case_techstack
                ON use_case.uc_id = join_use_case_techstack.use_case_id
                INNER JOIN techstack_name
                ON join_use_case_techstack.techstack_id = techstack_name.tech_id
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";
                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $benID[] = $row['ben_id'];
                            $benName[] = $row['benefit_name'];
                        }
                    }

                    $benName = array_unique($benName);
                    foreach ($benName as $value) {
                        echo $value;
                        echo '<br>';
                    }
                    echo '<br>';
                    echo '<h4>Applicable KPIs</h4>';
                    $sql = "SELECT * 
                FROM kpi
                INNER JOIN join_use_case_kpi
                ON kpi.kpi_id = join_use_case_kpi.kpi_id
                INNER JOIN use_case
                ON join_use_case_kpi.use_case_id = use_case.uc_id
                INNER JOIN join_use_case_techstack
                ON use_case.uc_id = join_use_case_techstack.use_case_id
                INNER JOIN techstack_name
                ON join_use_case_techstack.techstack_id = techstack_name.tech_id
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";
                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $kpiID[] = $row['kpi_id'];
                            $kpiName[] = $row['kpi_name'];
                        }
                    }

                    $kpiName = array_unique($kpiName);
                    foreach ($kpiName as $value) {
                        echo $value;
                        echo '<br>';
                    }
                    echo '<br>';
                    echo '<h4>Impetus for Adoption</h4>';

                    $sql = "SELECT force_type.force_type_id, force_type.force_type_name, force_item.force_id, force_item.force_name, force_item.force_description
                FROM force_type
                INNER JOIN force_item
                ON force_type.force_type_id = force_item.force_type_id
                INNER JOIN join_use_case_force_item
                ON force_item.force_id = join_use_case_force_item.force_item_id
                INNER JOIN use_case
                ON join_use_case_force_item.use_case_id = use_case.uc_id
                INNER JOIN join_use_case_techstack
                ON use_case.uc_id = join_use_case_techstack.use_case_id
                INNER JOIN techstack_name
                ON join_use_case_techstack.techstack_id = techstack_name.tech_id
                INNER JOIN techstack_summary
                ON techstack_name.tech_id = techstack_summary.tech_id
                INNER JOIN product
                ON techstack_summary.prod_id = product.prod_id
                WHERE product.prod_id = $productID";
                    $result = mysqli_query($conn, $sql);

                    if (!empty($result)) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $forceTypeID[] = $row['force_type_id'];
                            $forceTypeName[] = $row['force_type_name'];
                            $forceItemID[] = $row['force_id'];
                            $forceItemName[] = $row['force_name'];
                            $forceItemDesc[] = $row['force_description'];
                        }
                    }

                    $forceItemName = array_unique($forceItemName);

                    foreach ($forceItemName as $value) {
                        echo $value;
                        echo '<br>';
                    }

                    ?>
                </section>
            </div>
        </div>
    </div> <!-- Close the new wrapper div -->


</html>

<?php

echo '</div>';
echo '</div>';
echo '</div>'; // Close the new wrapper div


$conn->close();
?>

<!-- Main containers at top of page side-by-side -->


<!-- Expand / Contract + -  -->
<script>
    function toggleVisibility(element) {
        var content = element.parentNode.querySelector('.hidden');
        if (content) {
            if (content.style.display === "none" || content.style.display === "") {
                content.style.display = "block";
                element.textContent = '[-]';
            } else {
                content.style.display = "none";
                element.textContent = '[+]';
            }
        }
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Retrieve the saved tab from localStorage
        var savedTab = localStorage.getItem('selectedTab');

        // If there's a saved tab, activate it
        if (savedTab) {
            document.getElementById(savedTab).checked = true;
        }

        // Save the selected tab to localStorage when a tab is clicked
        var tabs = document.querySelectorAll('input[name="tab-control"]');
        tabs.forEach(function(tab) {
            tab.addEventListener('change', function() {
                localStorage.setItem('selectedTab', this.id);
            });
        });
    });
</script>
</body>

</html>