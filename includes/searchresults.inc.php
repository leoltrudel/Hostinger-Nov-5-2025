<?php
include_once 'dbh.inc.php';
include_once 'session.inc.php';

if (!$conn) {
    echo "Database Error";
}
?>



<?php

$queryNumber = "SELECT MAX(query_no) as `maxqueryno` FROM `query_log`";
$queryNumberResult = mysqli_query($conn, $queryNumber);
$queryNumberArray = mysqli_fetch_array($queryNumberResult);
$queryNumberIncrement = $queryNumberArray['maxqueryno'];
$queryNumberIncrement = intval($queryNumberIncrement);
$queryNumberIncrement++;

//This successfully sends checkbox-selected data from 'Asset Category' to the query_log table
if (isset($_POST['assetCategory']) and is_array($_POST['assetCategory'])) {
    foreach ($_POST['assetCategory'] as $assCat) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$assCat', 'Asset Category');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$aCNumber determines if any checkboxes under 'Asset Category' have been selected, and if so, activates the following loop.
$aCNumber = isset($_POST['assetCategory']);

//This loop intakes data from checked checkboxes under 'Asset Category', and outputs associated use case id's from the use_case table.
$arr0 = array();
if ($aCNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN asset_category
    ON query_log.attribute = asset_category.asset_category_category
    INNER JOIN asset_type
    ON asset_category.ac_id = asset_type.ac_id
    INNER JOIN asset_component
    ON asset_type.at_id = asset_component.asset_type_id
    INNER JOIN join_use_case_asset_component
    ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
    INNER JOIN use_case
    ON join_use_case_asset_component.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Asset Category' 
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr0[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr0[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr0 = array_column($arr0, 'uc_id');

//This successfully sends checkbox-selected data from 'Asset Type' to the query_log table
if (isset($_POST['assetType']) and is_array($_POST['assetType'])) {
    foreach ($_POST['assetType'] as $assTyp) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$assTyp', 'Asset Type');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$aCNumber determines if any checkboxes under 'Asset Type' have been selected, and if so, activates the following loop.
$aTNumber = isset($_POST['assetType']);

//This loop intakes data from checked checkboxes under 'Asset Type', and outputs associated use case id's from the use_case table.
$arr1 = array();
if ($aTNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN asset_type
    ON query_log.attribute = asset_type.asset_type
    INNER JOIN asset_component
    ON asset_type.at_id = asset_component.asset_type_id
    INNER JOIN join_use_case_asset_component
    ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
    INNER JOIN use_case
    ON join_use_case_asset_component.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Asset Type' 
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr1[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr1[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr1 = array_column($arr1, 'uc_id');

// $uniqueArray provides unique values from the array in the above while loop (e.g., $arr[] = $row;)
//$auniqueArray1 = array_unique($arr1, SORT_REGULAR);

//This successfully sends checkbox-selected data from 'Asset Component' to the query_log table
if (isset($_POST['assetComponent']) and is_array($_POST['assetComponent'])) {
    foreach ($_POST['assetComponent'] as $assCom) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$assCom', 'Asset Component');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$aCNumber determines if any checkboxes under 'Asset Component' have been selected, and if so, activates the following loop.
$aComNumber = isset($_POST['assetComponent']);

//This loop intakes data from checked checkboxes under 'Asset Component', and outputs associated use case id's from the use_case table.
$arr2 = array();
if ($aComNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN asset_component
    ON query_log.attribute = asset_component.component_name
    INNER JOIN join_use_case_asset_component
    ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
    INNER JOIN use_case
    ON join_use_case_asset_component.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Asset Component' 
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr2[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr2[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr2 = array_column($arr2, 'uc_id');

//This successfully sends checkbox-selected data from 'Infraststructure Type' to the query_log table
if (isset($_POST['infrastructureType']) and is_array($_POST['infrastructureType'])) {
    foreach ($_POST['infrastructureType'] as $infraType) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$infraType', 'Infraststructure Type');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Infraststructure Type' have been selected, and if so, activates the following loop.
$infraTypeNumber = isset($_POST['infrastructureType']);

//This loop intakes data from checked checkboxes under 'Infraststructure Type', and outputs associated use case id's from the use_case table.
$arr3 = array();
if ($infraTypeNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN infrastructure_type
    ON query_log.attribute = infrastructure_type.infra_type_name
    INNER JOIN asset_component
    ON asset_component.infrastructure_type_id = infrastructure_type.infra_type_id
    INNER JOIN join_use_case_asset_component
    ON join_use_case_asset_component.use_case_id = asset_component.acom_id
    INNER JOIN use_case
    ON join_use_case_asset_component.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Infraststructure Type' 
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr3[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr3[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr3 = array_column($arr3, 'uc_id');

//This successfully sends checkbox-selected data from 'Force' to the query_log table
if (isset($_POST['forceType']) and is_array($_POST['forceType'])) {
    foreach ($_POST['forceType'] as $forceType) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$forceType', 'Force');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Force' have been selected, and if so, activates the following loop.
$forceTypeNumber = isset($_POST['forceType']);

//This loop intakes data from checked checkboxes under 'Force', and outputs associated use case id's from the use_case table.
$arr4 = array();
if ($forceTypeNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN force_type
    ON query_log.attribute = force_type.force_type_name
    INNER JOIN force_item
    ON force_type.force_type_id = force_item.force_type_id
    INNER JOIN join_use_case_force_item
    ON join_use_case_force_item.force_item_id = force_item.force_id
    INNER JOIN use_case
    ON join_use_case_force_item.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Force' 
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr4[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr4[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr4 = array_column($arr4, 'uc_id');

//This successfully sends checkbox-selected data from 'Force Item' to the query_log table
if (isset($_POST['forceItem']) and is_array($_POST['forceItem'])) {
    foreach ($_POST['forceItem'] as $forceItem) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$forceItem', 'Force Item');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Force Item' have been selected, and if so, activates the following loop.
$forceItemNumber = isset($_POST['forceItem']);

//This loop intakes data from checked checkboxes under 'Force Item', and outputs associated use case id's from the use_case table.
$arr5 = array();
if ($forceItemNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN force_item
    ON query_log.attribute = force_item.force_name
    INNER JOIN join_use_case_force_item
    ON join_use_case_force_item.force_item_id = force_item.force_id
    INNER JOIN use_case
    ON join_use_case_force_item.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Force Item'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr5[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr5[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr5 = array_column($arr5, 'uc_id');

//This successfully sends checkbox-selected data from 'Benefit' to the query_log table
if (isset($_POST['benefitArray']) and is_array($_POST['benefitArray'])) {
    foreach ($_POST['benefitArray'] as $benefitArray) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$benefitArray', 'Benefit');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Benefit' have been selected, and if so, activates the following loop.
$benefitNumber = isset($_POST['benefitArray']);

//This loop intakes data from checked checkboxes under 'Benefit', and outputs associated use case id's from the use_case table.
$arr6 = array();
if ($benefitNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN benefit
    ON query_log.attribute = benefit.benefit_name
    INNER JOIN join_use_case_benefit
    ON join_use_case_benefit.benefit = benefit.ben_id
    INNER JOIN use_case
    ON join_use_case_benefit.use_case = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Benefit'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr6[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr6[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr6 = array_column($arr6, 'uc_id');

//This successfully sends checkbox-selected data from 'KPI' to the query_log table
if (isset($_POST['kpiArray']) and is_array($_POST['kpiArray'])) {
    foreach ($_POST['kpiArray'] as $kpiArray) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$kpiArray', 'KPI');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'KPI' have been selected, and if so, activates the following loop.
$kpiNumber = isset($_POST['kpiArray']);

//This loop intakes data from checked checkboxes under 'KPI', and outputs associated use case id's from the use_case table.
$arr7 = array();
if ($kpiNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN kpi
    ON query_log.attribute = kpi.kpi_name
    INNER JOIN join_use_case_kpi
    ON join_use_case_kpi.kpi_id = kpi.kpi_id
    INNER JOIN use_case
    ON join_use_case_kpi.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'KPI'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr7[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr7[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr7 = array_column($arr7, 'uc_id');

//This successfully sends checkbox-selected data from 'Program' to the query_log table
if (isset($_POST['programArray']) and is_array($_POST['programArray'])) {
    foreach ($_POST['programArray'] as $programArray) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$programArray', 'Program');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Program' have been selected, and if so, activates the following loop.
$programNumber = isset($_POST['programArray']);

//This loop intakes data from checked checkboxes under 'Program', and outputs associated use case id's from the use_case table.
$arr8 = array();
if ($programNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN program
    ON query_log.attribute = program.program_name
    INNER JOIN join_use_case_program
    ON join_use_case_program.program_id = program.prog_id
    INNER JOIN use_case
    ON join_use_case_program.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Program'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr8[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr8[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr8 = array_column($arr8, 'uc_id');

//This successfully sends checkbox-selected data from 'Vendor' to the query_log table
if (isset($_POST['companyArray']) and is_array($_POST['companyArray'])) {
    foreach ($_POST['companyArray'] as $companyArray) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$companyArray', 'Vendor');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Vendor' have been selected, and if so, activates the following loop.
$vendorNumber = isset($_POST['companyArray']);

//This loop intakes data from checked checkboxes under 'Vendor', and outputs associated use case id's from the use_case table.
$arr9 = array();
if ($vendorNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN company
    ON query_log.attribute = company.com_name
    INNER JOIN join_product_company
    ON join_product_company.company_id = company.com_id
    INNER JOIN product
    ON join_product_company.product_id = product.prod_id
    INNER JOIN techstack_summary
    ON product.prod_id =techstack_summary.prod_id
    INNER JOIN techstack_name
    ON techstack_summary.tech_id = techstack_name.tech_id
    INNER JOIN join_use_case_techstack
    ON techstack_name.tech_id = join_use_case_techstack.techstack_id
    INNER JOIN use_case
    ON join_use_case_techstack.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Vendor'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr9[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr9[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr9 = array_column($arr9, 'uc_id');

//This successfully sends checkbox-selected data from 'Product' to the query_log table
if (isset($_POST['productArray']) and is_array($_POST['productArray'])) {
    foreach ($_POST['productArray'] as $productArray) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$productArray', 'Product');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Product' have been selected, and if so, activates the following loop.
$productNumber = isset($_POST['productArray']);

//This loop intakes data from checked checkboxes under 'Product', and outputs associated use case id's from the use_case table.
$arr10 = array();
if ($productNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN product
    ON query_log.attribute = product.product_name
    INNER JOIN techstack_summary
    ON product.prod_id =techstack_summary.prod_id
    INNER JOIN techstack_name
    ON techstack_summary.tech_id = techstack_name.tech_id
    INNER JOIN join_use_case_techstack
    ON techstack_name.tech_id = join_use_case_techstack.techstack_id
    INNER JOIN use_case
    ON join_use_case_techstack.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Product'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr10[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr10[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr10 = array_column($arr10, 'uc_id');

//This successfully sends checkbox-selected data from 'Techstack Attribute' to the query_log table
if (isset($_POST['techstackAttribute']) and is_array($_POST['techstackAttribute'])) {
    foreach ($_POST['techstackAttribute'] as $techstackAttribute) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$techstackAttribute', 'Techstack Attribute');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Techstack Attribute' have been selected, and if so, activates the following loop.
$techstackNumber = isset($_POST['techstackAttribute']);

//This loop intakes data from checked checkboxes under 'Techstack Attribute', and outputs associated use case id's from the use_case table.
$arr11 = array();
if ($techstackNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN instantiation
    ON query_log.attribute = instantiation.instantiation_name
    INNER JOIN techstack_summary
    ON instantiation.inst_id = techstack_summary.method_id
    INNER JOIN techstack_name
    ON techstack_summary.tech_id = techstack_name.tech_id
    INNER JOIN join_use_case_techstack
    ON techstack_name.tech_id = join_use_case_techstack.techstack_id
    INNER JOIN use_case
    ON join_use_case_techstack.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Techstack Attribute'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr11[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr11[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr11 = array_column($arr11, 'uc_id');

//This successfully sends checkbox-selected data from 'Value Chain' to the query_log table
if (isset($_POST['valueChain']) and is_array($_POST['valueChain'])) {
    foreach ($_POST['valueChain'] as $valueChain) {
        $testQueryPost = "INSERT INTO query_log (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$valueChain', 'Value Chain');";
        mysqli_query($conn, $testQueryPost);
    }
}
//echo $dateTime;

//$infraTypeNumber determines if any checkboxes under 'Value Chain' have been selected, and if so, activates the following loop.
$valueChainNumber = isset($_POST['valueChain']);

//This loop intakes data from checked checkboxes under 'Value Chain', and outputs associated use case id's from the use_case table.
$arr12 = array();
if ($valueChainNumber > 0) {
    $sql = "SELECT uc_id
    FROM query_log
    INNER JOIN value_chain
    ON query_log.attribute = value_chain.vc_name
    INNER JOIN join_use_case_value_chain
    ON join_use_case_value_chain.value_chain_id = value_chain.vc_id
    INNER JOIN use_case
    ON join_use_case_value_chain.use_case_id = use_case.uc_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Value Chain'
    ORDER BY id
    DESC";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr12[] = $row;
        }
    }
} else {
    $sql = "SELECT uc_id FROM use_case";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr12[] = $row;
        }
    }
}

$arr12 = array_column($arr12, 'uc_id');

// $results = Array of Keys
$indexArray = array_intersect($arr0, $arr1, $arr2, $arr3, $arr4, $arr5, $arr6, $arr7, $arr8, $arr9, $arr10, $arr11, $arr12);
$indexArray = array_unique($indexArray, SORT_REGULAR);
/*
$indexArrayString = implode(',', $indexArray);

$vendorSQL = "SELECT company.com_id 
FROM company
INNER JOIN join_product_company
ON company.com_id = join_product_company.company_id
INNER JOIN product
ON join_product_company.product_id = product.prod_id
WHERE product.prod_id 
IN ( $indexArrayString )
";

$vendorSQLResult = mysqli_query($conn, $vendorSQL);

if ($vendorSQLResult && mysqli_num_rows($vendorSQLResult) > 0) {
    while ($row = mysqli_fetch_assoc($vendorSQLResult)) {
        $vendorIndexArray[] = $row['com_id'];
    }
}

$vendorIndexArray = array_unique($vendorIndexArray);
*/
//print_r($indexArray);




/*
$ucQueryNumber = "SELECT MAX(query_no) as `maxqueryno`, use_case_log_id FROM `use_case_log` WHERE submitted_by = $userid";
$ucQueryNumberResult = mysqli_query($conn, $ucQueryNumber);
$ucQueryNumberArray = mysqli_fetch_array($ucQueryNumberResult);
$ucQueryNumberIncrement = $ucQueryNumberArray['maxqueryno'];
$ucQueryNumberIncrement = intval($ucQueryNumberIncrement);
$ucQueryNumberIncrement++;

foreach($indexArray as $index) {
    $submitToDatabase = "INSERT INTO use_case_log (query_no, created_at, use_case_unique_id, submitted_by) VALUES ('$ucQueryNumberIncrement', '$dateTime', '$index', '$userid')";
mysqli_query($conn, $submitToDatabase);
}*/



//print_r($indexArray);

/* OLD CODE THAT WORKS! IF THIS NEW CODE, ABOVE, BUGS OUT, SCRAP IT AND REPLACE IT WITH THIS!!
// Changes multidimensional array to single dimensional array
$arr12 = array_column($arr12, 'uc_id');

// $results = Array of Keys
$indexArray=array_intersect($arr0,$arr1,$arr2,$arr3,$arr4,$arr5,$arr6,$arr7,$arr8,$arr9,$arr10,$arr11,$arr12);
$indexArray = array_unique($indexArray, SORT_REGULAR);
$indexArray = implode(",", $indexArray);
$query = "SELECT * FROM use_case WHERE uc_id IN (". $indexArray . ")";
$result = mysqli_query($conn, $query);
$indexArray = explode(",", $indexArray);
/*
$dataArray = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $dataArray[$row['uc_id']] = $row;
    }
} 

//$dataArray = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $ucIDValue[] = $row['uc_id'];
        $ucNameValue[] = $row['use_case_name'];
        $ucDescriptionValue[] = $row['use_case_description'];
    }
} else {
    echo "No Results Found.";
}
*/
// THAT'S RIGHT, REPLACE IT WITH THIS!!


//print_r($ucNameValue);

/*
foreach ($indexArray as $indexValue) {
    if (isset($dataArray[$indexValue])) {
        $row = $dataArray[$indexValue];
        echo "Use Case Name: {$row['use_case_name']}<br>";
        echo "Description: {$row['use_case_description']}\n <br><br>";
    } else {
        echo "No data found for ID $indexValue\n";
    }
}
*/


if (isset($_GET['id'])) {
    $idValue = $_GET['id'];

    //echo $idValue;
} else {
    $idValue = 0;
}


//$_SESSION['$id'] = $idValue;