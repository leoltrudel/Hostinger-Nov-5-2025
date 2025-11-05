<?php
echo '<form action="searchresults.php" method="POST">';
echo '<div class="asset-hierarchy">';

$sql = "SELECT * FROM asset_category";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $acID[] = $row['ac_id'];
        $acDesc[] = $row['asset_category_category'];
    }
}

$sql = "SELECT * FROM asset_type ORDER BY asset_type";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $atID[] = $row['at_id'];
        $acMap[] = $row['ac_id'];
        $atDesc[] = $row['asset_type'];
    }
}

$sql = "SELECT * FROM asset_component ORDER BY component_name";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $acompMap[] = $row['asset_type_id'];
        $acompDesc[] = $row['component_name'];
    }
}

//echo '<p><strong>Asset Hierarchy</strong></p>';

// Add the "Asset" tier
$assetTiers = ["<strong>Assets</strong>"];
foreach ($assetTiers as $tier) {
    echo '<div class="asset-hierarchy">';
    echo '<div class="asset-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="assetCategoryName[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="asset-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left
    foreach ($acID as $key => $id) {
        echo '<div class="asset-category">';
        echo '<input type="checkbox" id="category-' . $acDesc[$key] . '" name="assetCategory[]" value="' . $acDesc[$key] . '" onclick="toggleOptions(\'type-' . $acDesc[$key] . '\')">';
        echo '<label for="category-' . $acDesc[$key] . '">' . $acDesc[$key] . '</label>';
        echo '<div class="asset-type-list" id="type-' . $acDesc[$key] . '" style="display: none; padding-left: 20px;">'; // Add padding-left
        foreach ($acMap as $mapKey => $mapValue) {
            if ($mapValue == $id) {
                echo '<div class="asset-type">';
                echo '<input type="checkbox" id="type-' . $atDesc[$mapKey] . '" name="assetType[]" value="' . $atDesc[$mapKey] . '" onclick="toggleOptions(\'component-' . $atDesc[$mapKey] . '\')">';
                echo '<label for="type-' . $atDesc[$mapKey] . '">' . $atDesc[$mapKey] . '</label>';
                echo '<div class="asset-component-list" id="component-' . $atDesc[$mapKey] . '" style="display: none; padding-left: 20px;">'; // Add padding-left
                foreach ($acompMap as $compKey => $compValue) {
                    if ($compValue == $atID[$mapKey]) {
                        echo '<div class="asset-component">';
                        echo '<input type="checkbox" id="component-' . $acompDesc[$compKey] . '" name="assetComponent[]" value="' . $acompDesc[$compKey] . '">';
                        echo '<label for="component-' . $acompDesc[$compKey] . '">' . $acompDesc[$compKey] . '</label>';
                        echo '</div>'; // End of asset-component
                    }
                }
                echo '</div>'; // End of asset-component-list
                echo '</div>'; // End of asset-type
            }
        }
        echo '</div>'; // End of asset-type-list
        echo '</div>'; // End of asset-category
    }
    echo '</div>'; // End of asset-hierarchy
}

echo '<div class="benefit-hierarchy">';

// Fetch data for the first tier (Infrastructure Type)
$sql = "SELECT * FROM benefit";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $benefitID[] = $row['ben_id'];
        $benefitName[] = $row['benefit_name'];
    }
}

// Add the "benefit" tier
$benefitTiers = ["<strong>Benefit</strong>"];
foreach ($benefitTiers as $tier) {
    echo '<div class="benefit-hierarchy">';
    echo '<div class="benefit-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="benefitCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="benefit-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to Infrastructure Type) using the provided code
    foreach ($benefitID as $key => $id) {
        echo '<div class="benefit-type">';
        echo '<input type="checkbox" id="type-' . $benefitName[$key] . '" name="benefitArray[]" value="' . $benefitName[$key] . '" onclick="toggleOptions(\'option-' . $benefitName[$key] . '\')">';
        echo '<label for="type-' . $benefitName[$key] . '">' . $benefitName[$key] . '</label>';
        echo '<div class="benefit-option-list" id="option-' . $benefitName[$key] . '" style="display: none; padding-left: 20px;">'; // Add padding-left
        // Output the third tier (Options related to each Infrastructure Type)
        // Note: You may need to fetch and display data for this tier
        echo '</div>'; // End of infrastructure-option-list
        echo '</div>'; // End of infrastructure-type
    }

    echo '</div>'; // End of infrastructure-type-list
    echo '</div>'; // End of infrastructure-category
}

echo '</div>'; // End of infrastructure-hierarchy

$verifiedTiers = ["<strong>Financing Options</strong>"];
foreach ($verifiedTiers as $tier) {
    echo '<div class="grant-eligibility-hierarchy">';
    echo '<div class="grant-eligibility-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="grantEligibilityCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="grant-eligibility-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier 
    $options = ["Grant", "Loan", "Other"];
    foreach ($options as $option) {
        echo '<div class="grant-eligibility-type">';
        echo '<input type="checkbox" id="type-' . $option . '" name="grantEligibility[]" value="' . $option . '" onclick="toggleOptions(\'option-' . $option . '\')">';
        echo '<label for="type-' . $option . '">' . $option . '</label>';
        echo '<div class="grant-eligibility-option-list" id="option-' . $option . '" style="display: none; padding-left: 20px;">'; // Add padding-left

        // You can add more options or data for this tier if needed.

        echo '</div>'; // End of grant-eligibility-option-list
        echo '</div>'; // End of grant-eligibility-type
    }

    echo '</div>'; // End of grant-eligibility-type-list
    echo '</div>'; // End of grant-eligibility-category
    echo '</div>'; // End of grant-eligibility-hierarchy
}

echo '<div class="force-hierarchy">';

// Fetch data for the first tier (Force Type)
$sql = "SELECT * FROM force_type";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $forceTypeID[] = $row['force_type_id'];
        $forceTypeName[] = $row['force_type_name'];
    }
}

// Fetch data for the third tier (Force Items)
$sql = "SELECT force_id, force_type_id, force_name, force_description FROM force_item";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $forceItemID[] = $row['force_id'];
        $forceItemTypeID[] = $row['force_type_id'];
        $forceItemName[] = $row['force_name'];
        $forceItemDesc[] = $row['force_description'];
    }
}

// Add the "Force Type" tier
$forceTiers = ["<strong>Force Type</strong>"];
foreach ($forceTiers as $tier) {
    echo '<div class="force-hierarchy">';
    echo '<div class="force-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="forceCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="force-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to Force Type) using the provided code
    foreach ($forceTypeID as $key => $id) {
        echo '<div class="force-type">';
        echo '<input type="checkbox" id="type-' . $forceTypeName[$key] . '" name="forceType[]" value="' . $forceTypeName[$key] . '" onclick="toggleOptions(\'item-' . $forceTypeName[$key] . '\')">';
        echo '<label for="type-' . $forceTypeName[$key] . '">' . $forceTypeName[$key] . '</label>';
        echo '<div class="force-item-list" id="item-' . $forceTypeName[$key] . '" style="display: none; padding-left: 20px;">'; // Add padding-left

        // Check if $forceItemTypeID is set and not empty before iterating
        if (isset($forceItemTypeID) && !empty($forceItemTypeID)) {
            // Output the third tier (Items related to each Force Type)
            foreach ($forceItemTypeID as $itemKey => $itemValue) {
                if ($itemValue == $id) {
                    echo '<div class="force-item">';
                    echo '<input type="checkbox" id="item-' . $forceItemName[$itemKey] . '" name="forceItem[]" value="' . $forceItemName[$itemKey] . '">';
                    echo '<label for="item-' . $forceItemName[$itemKey] . '">' . $forceItemName[$itemKey] . '</label>';
                    echo '</div>'; // End of force-item
                }
            }
        }

        echo '</div>'; // End of force-item-list
        echo '</div>'; // End of force-type
    }

    echo '</div>'; // End of force-type-list
    echo '</div>'; // End of force-category
}

echo '</div>'; // End of force-hierarchy

echo '<div class="infrastructure-hierarchy">';

// Fetch data for the first tier (Infrastructure Type)
$sql = "SELECT * FROM infrastructure_type";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $infraID[] = $row['infra_type_id'];
        $infraName[] = $row['infra_type_name'];
        $infraDesc[] = $row['infra_type_description'];
    }
}

// Add the "Infrastructure Type" tier
$infrastructureTiers = ["<strong>Infrastructure Type</strong>"];
foreach ($infrastructureTiers as $tier) {
    echo '<div class="infrastructure-hierarchy">';
    echo '<div class="infrastructure-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="infrastructureCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="infrastructure-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to Infrastructure Type) using the provided code
    foreach ($infraID as $key => $id) {
        echo '<div class="infrastructure-type">';
        echo '<input type="checkbox" id="type-' . $infraName[$key] . '" name="infrastructureType[]" value="' . $infraName[$key] . '" onclick="toggleOptions(\'option-' . $infraName[$key] . '\')">';
        echo '<label for="type-' . $infraName[$key] . '">' . $infraName[$key] . '</label>';
        echo '<div class="infrastructure-option-list" id="option-' . $infraName[$key] . '" style="display: none; padding-left: 20px;">'; // Add padding-left
        // Output the third tier (Options related to each Infrastructure Type)
        // Note: You may need to fetch and display data for this tier
        echo '</div>'; // End of infrastructure-option-list
        echo '</div>'; // End of infrastructure-type
    }

    echo '</div>'; // End of infrastructure-type-list
    echo '</div>'; // End of infrastructure-category
}

echo '</div>'; // End of infrastructure-hierarchy

echo '<div class="kpi-hierarchy">';

// Fetch data for the first tier (KPI)
$sql = "SELECT kpi_id, kpi_name, kpi_description FROM kpi ORDER BY kpi_name";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $kpiID[] = $row['kpi_id'];
        $kpiName[] = $row['kpi_name'];
        $kpiDesc[] = $row['kpi_description'];
    }
}

// Add the "KPI" tier
$kpiTiers = ["<strong>KPI</strong>"];
foreach ($kpiTiers as $tier) {
    echo '<div class="kpi-hierarchy">';
    echo '<div class="kpi-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="kpiCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="kpi-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to KPI)
    foreach ($kpiID as $key => $id) {
        echo '<div class="kpi">';
        echo '<input type="checkbox" id="kpi-' . $kpiName[$key] . '" name="kpiArray[]" value="' . $kpiName[$key] . '">';
        echo '<label for="kpi-' . $kpiName[$key] . '">' . $kpiName[$key] . '</label>';
        echo '</div>'; // End of kpi
    }

    echo '</div>'; // End of kpi-list
    echo '</div>'; // End of kpi-category
}

echo '</div>'; // End of kpi-hierarchy

echo '<div class="program-hierarchy">';

// Fetch data for the first tier (Program)
$sql = "SELECT prog_id, program_name, program_description FROM program ORDER BY program_name";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $programID[] = $row['prog_id'];
        $programName[] = $row['program_name'];
        $programDesc[] = $row['program_description'];
    }
}

// Add the "Program" tier
$programTiers = ["<strong>Program</strong>"];
foreach ($programTiers as $tier) {
    echo '<div class="program-hierarchy">';
    echo '<div class="program-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="programCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="program-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to Program)
    foreach ($programID as $key => $id) {
        echo '<div class="program">';
        echo '<input type="checkbox" id="program-' . $programName[$key] . '" name="programArray[]" value="' . $programName[$key] . '">';
        echo '<label for="program-' . $programName[$key] . '">' . $programName[$key] . '</label>';
        echo '</div>'; // End of program
    }

    echo '</div>'; // End of program-list
    echo '</div>'; // End of program-category
}

echo '</div>'; // End of program-hierarchy

$sql = "SELECT inst_id, instantiation_name, inst_description FROM instantiation";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $instID[] = $row['inst_id'];
        $instName[] = $row['instantiation_name'];
        $instDesc[] = $row['inst_description'];
    }
}

// Add the "Instantiation" tier
$instantiationTiers = ["<strong>Technical Attributes</strong>"];
foreach ($instantiationTiers as $tier) {
    echo '<div class="instantiation-hierarchy">';
    echo '<div class="instantiation-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="instantiationCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="instantiation-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to Instantiation Type) using the provided code
    foreach ($instID as $key => $id) {
        echo '<div class="instantiation-type">';
        echo '<input type="checkbox" id="type-' . $instName[$key] . '" name="techstackAttribute[]" value="' . $instName[$key] . '" onclick="toggleOptions(\'option-' . $instName[$key] . '\')">';
        echo '<label for="type-' . $instName[$key] . '">' . $instName[$key] . '</label>';
        echo '<div class="instantiation-option-list" id="option-' . $instName[$key] . '" style="display: none; padding-left: 20px;">'; // Add padding-left
        // Output the third tier (Options related to each Instantiation Type)
        // Note: You may need to fetch and display data for this tier
        echo '</div>'; // End of instantiation-option-list
        echo '</div>'; // End of instantiation-type
    }

    echo '</div>'; // End of instantiation-type-list
    echo '</div>'; // End of instantiation-category
}

echo '</div>'; // End of instantiation-hierarchy
//echo '</div>'; // End of instantiation-hierarchy

// Fetch data for the first tier (Value Chain)
$sql = "SELECT * FROM value_chain";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $vcID[] = $row['vc_id'];
        $vcName[] = $row['vc_name'];
    }
}

// Add the "Value Chain" tier
$valueChainTiers = ["<strong>Value Chain</strong>"];
foreach ($valueChainTiers as $tier) {
    echo '<div class="value-chain-hierarchy">';
    echo '<div class="value-chain-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="valueChainCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="value-chain-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to Value Chain) using the provided code
    foreach ($vcID as $key => $id) {
        echo '<div class="value-chain-type">';
        echo '<input type="checkbox" id="type-' . $vcName[$key] . '" name="valueChain[]" value="' . $vcName[$key] . '" onclick="toggleOptions(\'option-' . $vcName[$key] . '\')">';
        echo '<label for="type-' . $vcName[$key] . '">' . $vcName[$key] . '</label>';
        echo '<div class="value-chain-option-list" id="option-' . $vcName[$key] . '" style="display: none; padding-left: 20px;">'; // Add padding-left
        // Output the third tier (Options related to each Value Chain)
        // Note: You may need to fetch and display data for this tier
        echo '</div>'; // End of value-chain-option-list
        echo '</div>'; // End of value-chain-type
    }

    echo '</div>'; // End of value-chain-type-list
    echo '</div>'; // End of value-chain-category
}

echo '</div>'; // End of value-chain-hierarchy

echo '<div class="vendor-solution-hierarchy">';

// Fetch data for the first tier (Vendor Solution)
$sql = "SELECT com_id, com_name, com_description, prod_id, product_name, product_description
            FROM company 
            INNER JOIN join_product_company
            ON company.com_id = join_product_company.company_id
            INNER JOIN product
            ON join_product_company.product_id = product.prod_id
            ORDER BY com_name";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    // Initialize arrays to store unique vendor names and their associated products
    $uniqueVendors = [];
    $vendorProducts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $vendorName = $row['com_name'];
        $productName = $row['product_name'];

        // Store unique vendor names
        if (!in_array($vendorName, $uniqueVendors)) {
            $uniqueVendors[] = $vendorName;
        }

        // Store products associated with each vendor
        $vendorProducts[$vendorName][] = $productName;
    }

    // Add the "Vendor Solution" tier
    $vendorSolutionTiers = ["<strong>Vendor Solution</strong>"];
    foreach ($vendorSolutionTiers as $tier1) {
        echo '<div class="vendor-solution-hierarchy">';
        echo '<div class="vendor-solution-category">';
        echo '<input type="checkbox" id="category-' . $tier1 . '" name="vendorSolutionCategory[]" value="' . $tier1 . '" onclick="toggleOptions(\'vendor-' . $tier1 . '\')">';
        echo '<label for="category-' . $tier1 . '">' . $tier1 . '</label>';
        echo '<div class="vendor-list" id="vendor-' . $tier1 . '" style="display: none; padding-left: 20px;">'; // Add padding-left

        // Output the second tier (Vendor Name)
        foreach ($uniqueVendors as $vendor) {
            echo '<div class="vendor">';
            echo '<input type="checkbox" id="vendor-' . $vendor . '" name="companyArray[]" value="' . $vendor . '" onclick="toggleOptions(\'product-' . $vendor . '\')">';
            echo '<label for="vendor-' . $vendor . '">' . $vendor . '</label>';
            echo '<div class="product-list" id="product-' . $vendor . '" style="display: none; padding-left: 20px;">'; // Add padding-left

            // Output the third tier (Product Name)
            foreach ($vendorProducts[$vendor] as $product) {
                echo '<div class="product">';
                echo '<input type="checkbox" id="product-' . $product . '" name="productArray[]" value="' . $product . '">';
                echo '<label for="product-' . $product . '">' . $product . '</label>';
                echo '</div>'; // End of product
            }

            echo '</div>'; // End of product-list
            echo '</div>'; // End of vendor
        }

        echo '</div>'; // End of vendor-list
        echo '</div>'; // End of vendor-solution-category
    }
}

$sql = "SELECT verified_id, v_status FROM verified";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

// Initialize arrays before using them
$verifiedID = [];
$verifiedStatus = [];

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $verifiedID[] = $row['verified_id'];
        $verifiedStatus[] = $row['v_status'];
    }
}

// Add the "Verified Status" tier
$verifiedTiers = ["<strong>Verified Status</strong>"];
foreach ($verifiedTiers as $tier) {
    echo '<div class="verified-status-hierarchy">';
    echo '<div class="verified-status-category">';
    echo '<input type="checkbox" id="category-' . $tier . '" name="verifiedStatusCategory[]" value="' . $tier . '" onclick="toggleOptions(\'type-' . $tier . '\')">';
    echo '<label for="category-' . $tier . '">' . $tier . '</label>';
    echo '<div class="verified-status-type-list" id="type-' . $tier . '" style="display: none; padding-left: 20px;">'; // Add padding-left

    // Output the second tier (Options related to Verified Status)
    foreach ($verifiedID as $key => $id) {
        echo '<div class="verified-status-type">';
        echo '<input type="checkbox" id="type-' . $verifiedStatus[$key] . '" name="verifiedStatus[]" value="' . $verifiedStatus[$key] . '" onclick="toggleOptions(\'option-' . $verifiedStatus[$key] . '\')">';
        echo '<label for="type-' . $verifiedStatus[$key] . '">' . $verifiedStatus[$key] . '</label>';
        echo '<div class="verified-status-option-list" id="option-' . $verifiedStatus[$key] . '" style="display: none; padding-left: 20px;">'; // Add padding-left

        // Output the third tier (Options related to each Verified Status)
        // Note: You may need to fetch and display data for this tier if needed.
        echo '</div>'; // End of verified-status-option-list
        echo '</div>'; // End of verified-status-type
    }

    echo '</div>'; // End of verified-status-type-list
    echo '</div>'; // End of verified-status-category
}

echo '</div>'; // End of verified-status-hierarchy


echo '</form>';
/*
?>

<?php 
$sql = "SELECT * FROM value_chain";
$result = mysqli_query($conn, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $vcID[] = $row['vc_id'];
        $vcName[] = $row['vc_name'];
    }
} */