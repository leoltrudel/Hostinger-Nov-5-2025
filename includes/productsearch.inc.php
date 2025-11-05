<?php
include_once 'searchresults.inc.php';

if (!$conn) {
    echo "Database Error";
}

$queryNumber = "SELECT MAX(query_no) as `maxqueryno` FROM `query_log_product`";
$queryNumberResult = mysqli_query($conn, $queryNumber);
$queryNumberArray = mysqli_fetch_array($queryNumberResult);
$queryNumberIncrement = $queryNumberArray['maxqueryno'];
$queryNumberIncrement = intval($queryNumberIncrement);
$queryNumberIncrement++;
// REQUIRED MODIFICATIONS
// 1. You need to extend each search from use_case to product; the direction of these extensions will vary, but use case needs to be preserved in all cases to align with 2. 
// 2. You need to change the conditional in the WHERE clause to include the array that contains use cases saved in Favorites

//This successfully sends checkbox-selected data from 'Asset Category' to the query_log_product table
if (isset($_POST['assetCategory']) and is_array($_POST['assetCategory'])) {
    foreach ($_POST['assetCategory'] as $assCat) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$assCat', 'Asset Category');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$aCNumber determines if any checkboxes under 'Asset Category' have been selected, and if so, activates the following loop.
$aCNumber = isset($_POST['assetCategory']);

//This loop intakes data from checked checkboxes under 'Asset Category', and outputs associated use case id's from the use_case table.
$arr0 = array();
// if($aCNumber > 0) {   
if ($aCNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN asset_category
    ON query_log_product.attribute = asset_category.asset_category_category
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Asset Category' 
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr0[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr0[] = $row;
        }
    }
}

$arr0 = array_column($arr0, 'prod_id');
//$arr0 = array_unique($arr0);

//This successfully sends checkbox-selected data from 'Asset Type' to the query_log table
if (isset($_POST['assetType']) and is_array($_POST['assetType'])) {
    foreach ($_POST['assetType'] as $assTyp) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$assTyp', 'Asset Type');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$aCNumber determines if any checkboxes under 'Asset Type' have been selected, and if so, activates the following loop.
$aTNumber = isset($_POST['assetType']);

//This loop intakes data from checked checkboxes under 'Asset Type', and outputs associated use case id's from the use_case table.
$arr1 = array();
if ($aTNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN asset_type
    ON query_log_product.attribute = asset_type.asset_type
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Asset Type' 
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr1[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr1[] = $row;
        }
    }
}
// Changes multidimentional array to single dimentional array
$arr1 = array_column($arr1, 'prod_id');

//This successfully sends checkbox-selected data from 'Asset Component' to the query_log table
if (isset($_POST['assetComponent']) and is_array($_POST['assetComponent'])) {
    foreach ($_POST['assetComponent'] as $assCom) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$assCom', 'Asset Component');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$aCNumber determines if any checkboxes under 'Asset Component' have been selected, and if so, activates the following loop.
$aComNumber = isset($_POST['assetComponent']);

//This loop intakes data from checked checkboxes under 'Asset Component', and outputs associated use case id's from the use_case table.
$arr2 = array();
if ($aComNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN asset_component
    ON query_log_product.attribute = asset_component.component_name
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Asset Component' 
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr2[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr2[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr2 = array_column($arr2, 'prod_id');

//This successfully sends checkbox-selected data from 'Infraststructure Type' to the query_log table
if (isset($_POST['infrastructureType']) and is_array($_POST['infrastructureType'])) {
    foreach ($_POST['infrastructureType'] as $infraType) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$infraType', 'Infraststructure Type');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Infraststructure Type' have been selected, and if so, activates the following loop.
$infraTypeNumber = isset($_POST['infrastructureType']);

//This loop intakes data from checked checkboxes under 'Infraststructure Type', and outputs associated use case id's from the use_case table.
$arr3 = array();
if ($infraTypeNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN infrastructure_type
    ON query_log_product.attribute = infrastructure_type.infra_type_name
    INNER JOIN asset_component
    ON asset_component.infrastructure_type_id = infrastructure_type.infra_type_id
    INNER JOIN join_use_case_asset_component
    ON join_use_case_asset_component.use_case_id = asset_component.acom_id
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Infraststructure Type' 
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr3[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr3[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr3 = array_column($arr3, 'prod_id');

//This successfully sends checkbox-selected data from 'Force' to the query_log_product table
if (isset($_POST['forceType']) and is_array($_POST['forceType'])) {
    foreach ($_POST['forceType'] as $forceType) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$forceType', 'Force');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Force' have been selected, and if so, activates the following loop.
$forceTypeNumber = isset($_POST['forceType']);

//This loop intakes data from checked checkboxes under 'Force', and outputs associated use case id's from the use_case table.
$arr4 = array();
if ($forceTypeNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN force_type
    ON query_log_product.attribute = force_type.force_type_name
    INNER JOIN force_item
    ON force_type.force_type_id = force_item.force_type_id
    INNER JOIN join_use_case_force_item
    ON join_use_case_force_item.force_item_id = force_item.force_id
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Force' 
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr4[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr4[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr4 = array_column($arr4, 'prod_id');

//This successfully sends checkbox-selected data from 'Force Item' to the query_log_product table
if (isset($_POST['forceItem']) and is_array($_POST['forceItem'])) {
    foreach ($_POST['forceItem'] as $forceItem) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$forceItem', 'Force Item');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Force Item' have been selected, and if so, activates the following loop.
$forceItemNumber = isset($_POST['forceItem']);

//This loop intakes data from checked checkboxes under 'Force Item', and outputs associated use case id's from the use_case table.
$arr5 = array();
if ($forceItemNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN force_item
    ON query_log_product.attribute = force_item.force_name
    INNER JOIN join_use_case_force_item
    ON join_use_case_force_item.force_item_id = force_item.force_id
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Force Item'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr5[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr5[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr5 = array_column($arr5, 'prod_id');

//This successfully sends checkbox-selected data from 'Benefit' to the query_log_product table
if (isset($_POST['benefitArray']) and is_array($_POST['benefitArray'])) {
    foreach ($_POST['benefitArray'] as $benefitArray) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$benefitArray', 'Benefit');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Benefit' have been selected, and if so, activates the following loop.
$benefitNumber = isset($_POST['benefitArray']);

//This loop intakes data from checked checkboxes under 'Benefit', and outputs associated use case id's from the use_case table.
$arr6 = array();
if ($benefitNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN benefit
    ON query_log_product.attribute = benefit.benefit_name
    INNER JOIN join_use_case_benefit
    ON join_use_case_benefit.benefit = benefit.ben_id
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Benefit'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr6[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr6[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr6 = array_column($arr6, 'prod_id');

//This successfully sends checkbox-selected data from 'KPI' to the query_log_product table
if (isset($_POST['kpiArray']) and is_array($_POST['kpiArray'])) {
    foreach ($_POST['kpiArray'] as $kpiArray) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$kpiArray', 'KPI');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'KPI' have been selected, and if so, activates the following loop.
$kpiNumber = isset($_POST['kpiArray']);

//This loop intakes data from checked checkboxes under 'KPI', and outputs associated use case id's from the use_case table.
$arr7 = array();
if ($kpiNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN kpi
    ON query_log_product.attribute = kpi.kpi_name
    INNER JOIN join_use_case_kpi
    ON join_use_case_kpi.kpi_id = kpi.kpi_id
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'KPI'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr7[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr7[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr7 = array_column($arr7, 'prod_id');

//This successfully sends checkbox-selected data from 'Program' to the query_log_product table
if (isset($_POST['programArray']) and is_array($_POST['programArray'])) {
    foreach ($_POST['programArray'] as $programArray) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$programArray', 'Program');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Program' have been selected, and if so, activates the following loop.
$programNumber = isset($_POST['programArray']);

//This loop intakes data from checked checkboxes under 'Program', and outputs associated use case id's from the use_case table.
$arr8 = array();
if ($programNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN program
    ON query_log_product.attribute = program.program_name
    INNER JOIN join_use_case_program
    ON join_use_case_program.program_id = program.prog_id
    INNER JOIN use_case
    ON join_use_case_program.use_case_id = use_case.uc_id
    INNER JOIN join_use_case_techstack
    ON use_case.uc_id = join_use_case_techstack.use_case_id
    INNER JOIN techstack_name
    ON join_use_case_techstack.techstack_id = techstack_name.tech_id
    INNER JOIN techstack_summary
    ON techstack_name.tech_id = techstack_summary.tech_id
    INNER JOIN product
    ON techstack_summary.prod_id = product.prod_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Program'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr8[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr8[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr8 = array_column($arr8, 'prod_id');

//This successfully sends checkbox-selected data from 'Vendor' to the query_log_product table
if (isset($_POST['companyArray']) and is_array($_POST['companyArray'])) {
    foreach ($_POST['companyArray'] as $companyArray) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$companyArray', 'Vendor');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Vendor' have been selected, and if so, activates the following loop.
$vendorNumber = isset($_POST['companyArray']);

//This loop intakes data from checked checkboxes under 'Vendor', and outputs associated use case id's from the use_case table.
$arr9 = array();
if ($vendorNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN company
    ON query_log_product.attribute = company.com_name
    INNER JOIN join_product_company
    ON join_product_company.company_id = company.com_id
    INNER JOIN product
    ON join_product_company.product_id = product.prod_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Vendor'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr9[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr9[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr9 = array_column($arr9, 'prod_id');
/*echo '$queryNumberIncrement = ';
echo $queryNumberIncrement;
echo '<br>';
echo 'prod_id array = ';
var_dump($arr9); */

//This successfully sends checkbox-selected data from 'Product' to the query_log_product table
if (isset($_POST['productArray']) and is_array($_POST['productArray'])) {
    foreach ($_POST['productArray'] as $productArray) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$productArray', 'Product');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Product' have been selected, and if so, activates the following loop.
$productNumber = isset($_POST['productArray']);

//This loop intakes data from checked checkboxes under 'Product', and outputs associated use case id's from the use_case table.
$arr10 = array();
if ($productNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN product
    ON query_log_product.attribute = product.product_name
    WHERE query_no = $queryNumberIncrement
    AND class = 'Product'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr10[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr10[] = $row;
        }
    }
}

//echo $queryNumberIncrement;

// Changes multidimentional array to single dimentional array
$arr10 = array_column($arr10, 'prod_id');

//This successfully sends checkbox-selected data from 'Techstack Attribute' to the query_log_product table
if (isset($_POST['techstackAttribute']) and is_array($_POST['techstackAttribute'])) {
    foreach ($_POST['techstackAttribute'] as $techstackAttribute) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$techstackAttribute', 'Techstack Attribute');";
        mysqli_query($conn, $testQueryPost);
    }
}

//$infraTypeNumber determines if any checkboxes under 'Techstack Attribute' have been selected, and if so, activates the following loop.
$techstackNumber = isset($_POST['techstackAttribute']);

//This loop intakes data from checked checkboxes under 'Techstack Attribute', and outputs associated use case id's from the use_case table.
$arr11 = array();
if ($techstackNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN instantiation
    ON query_log_product.attribute = instantiation.instantiation_name
    INNER JOIN techstack_summary
    ON instantiation.inst_id = techstack_summary.method_id
    INNER JOIN product
    ON techstack_summary.prod_id = product.prod_id
    WHERE query_no = $queryNumberIncrement
    AND class = 'Techstack Attribute'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr11[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr11[] = $row;
        }
    }
}

// Changes multidimentional array to single dimentional array
$arr11 = array_column($arr11, 'prod_id');

//This successfully sends checkbox-selected data from 'Value Chain' to the query_log_product table
if (isset($_POST['valueChain']) and is_array($_POST['valueChain'])) {
    foreach ($_POST['valueChain'] as $valueChain) {
        $testQueryPost = "INSERT INTO query_log_product (ts, user, query_no, attribute, class) VALUES ('$dateTime', '$username', '$queryNumberIncrement', '$valueChain', 'Value Chain');";
        mysqli_query($conn, $testQueryPost);
    }
}
//echo $dateTime;

//$infraTypeNumber determines if any checkboxes under 'Value Chain' have been selected, and if so, activates the following loop.
$valueChainNumber = isset($_POST['valueChain']);

//This loop intakes data from checked checkboxes under 'Value Chain', and outputs associated use case id's from the use_case table.
$arr12 = array();
if ($valueChainNumber > 0) {
    $sql = "SELECT DISTINCT product.prod_id
    FROM query_log_product
    INNER JOIN value_chain
    ON query_log_product.attribute = value_chain.vc_name
    INNER JOIN join_use_case_value_chain
    ON join_use_case_value_chain.value_chain_id = value_chain.vc_id
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
    WHERE query_no = $queryNumberIncrement
    AND class = 'Value Chain'
    ORDER BY product.prod_id";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr12[] = $row;
        }
    }
} else {
    $sql = "SELECT prod_id FROM product";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr12[] = $row;
        }
    }
}

$arr12 = array_column($arr12, 'prod_id');

// $results = Array of Keys
$indexArray = array_intersect($arr0, $arr1, $arr2, $arr3, $arr4, $arr5, $arr6, $arr7, $arr8, $arr9, $arr10, $arr11, $arr12);
$indexArray = array_unique($indexArray, SORT_REGULAR);
$indexArray = array_merge($indexArray, array());

$indexArrayString = implode(',', $indexArray);
/*echo '<br>';
echo '$indexArrayString = ';
echo $indexArrayString;*/
$vendorIndexArray = [];
if (!empty($indexArrayString)) {
    $vendorSQL = "SELECT company_id, product_id FROM join_product_company WHERE product_id IN ($indexArrayString)";
    $vendorSQLResult = mysqli_query($conn, $vendorSQL);
    if ($vendorSQLResult && mysqli_num_rows($vendorSQLResult) > 0) {
        while ($row = mysqli_fetch_assoc($vendorSQLResult)) {
            $vendorIndexArray[] = $row['company_id'];
        }
        $vendorIndexArray = array_unique($vendorIndexArray);
    } else {
        $vendorIndexArray = 9999999999999999999;
    }
}
//var_dump($vendorIndexArray);

/*
$vendorSQL = "SELECT company.com_id 
FROM company
INNER JOIN join_product_company
ON company.com_id = join_product_company.company_id
INNER JOIN product
ON join_product_company.product_id = product.prod_id
INNER JOIN techstack_summary
ON product.prod_id = techstack_summary.prod_id
INNER JOIN techstack_name
ON techstack_summary.tech_id = techstack_name.tech_id
INNER JOIN join_use_case_techstack
ON techstack_name.tech_id = join_use_case_techstack.techstack_id
INNER JOIN use_case
ON join_use_case_techstack.use_case_id = use_case.uc_id
WHERE use_case.uc_id 
IN ( $indexArrayString )
";
*/






/*echo '<br>';
echo '$vendorIndexArrays!!! = ';
var_dump($vendorIndexArray);
echo '<br>';*/

/*
echo '$indexArray';
print_r($indexArray);
echo '<br>';
echo '<br>';
echo "Asset Category: ";
print_r($arr0);
echo '<br>';
echo '<br>';
echo "Asset Type: ";
print_r($arr1);
echo '<br>';
echo '<br>';
echo "Asset Component: ";
print_r($arr2);
echo '<br>';
echo '<br>';
echo "Infrastructure ";
print_r($arr3);
echo '<br>';
echo '<br>';
echo "Force: ";
print_r($arr4);
echo '<br>';
echo '<br>';
echo "Force Type (subset): ";
print_r($arr5);
echo '<br>';
echo '<br>';
echo "Benefit: ";
print_r($arr6);
echo '<br>';
echo '<br>';
echo "KPI: ";
print_r($arr7);
echo '<br>';
echo '<br>';
echo "Program: ";
print_r($arr8);
echo '<br>';
echo '<br>';
echo "Vendor: ";
print_r($arr9);
echo '<br>';
echo '<br>';
echo "Product (Subset): ";
print_r($arr10);
echo '<br>';
echo '<br>';
echo "Technical Attributes: ";
print_r($arr11);
echo '<br>';
echo '<br>';
echo "Value Chain: ";
print_r($arr12);
*/


if (isset($_GET['id'])) {
    $idValue = $_GET['id'];

    //echo $idValue;
} else {
    $idValue = 0;
}
