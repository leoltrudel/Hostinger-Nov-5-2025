<?php
require_once 'favorites.inc.php';
//require_once 'session.inc.php';
//require_once 'addnewlist.inc.php';
?>
<?php

$techIDArray = array();
while ($row3 = mysqli_fetch_assoc($result)) {
    $techIDArray[] = $row3['csv_values'];
}


$techIDArray = implode(',', $techIDArray);

$sql = "SELECT tech_id, 
                GROUP_CONCAT(DISTINCT CONCAT(verified.v_status, '<br>') 
                ORDER BY verified.v_status SEPARATOR '') 
                AS csv_values
                FROM verified
                INNER JOIN techstack_name
                ON verified.verified_id = techstack_name.verified
                INNER JOIN join_use_case_techstack
                ON techstack_name.tech_id = join_use_case_techstack.techstack_id
                INNER JOIN use_case
                ON join_use_case_techstack.use_case_id = use_case.uc_id
                WHERE tech_id 
                IN (" . $techIDArray . ")
                GROUP BY tech_id";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $status[] = $row3['csv_values'] . '<br>';
}

$sql = "SELECT DISTINCT verified, techstack_name 
                FROM techstack_name 
                INNER JOIN join_use_case_techstack
                ON techstack_name.tech_id = join_use_case_techstack.techstack_id
                INNER JOIN use_case
                ON join_use_case_techstack.use_case_id = use_case.uc_id
                WHERE uc_id = {$idValue}";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $techstackName[] = $row3['techstack_name'];
}

//echo '<br>';
//echo $techIDArray;
$sql = "SELECT techstack_name.tech_id, 
                GROUP_CONCAT(DISTINCT CONCAT(component_name, '<br>') 
                ORDER BY component_name SEPARATOR '') 
                AS csv_values
                FROM techstack_component
                INNER JOIN techstack_summary
                ON techstack_component.tech_component_id = techstack_summary.comp_id
                INNER JOIN techstack_name
                ON techstack_summary.tech_id = techstack_name.tech_id
                WHERE techstack_name.tech_id 
                IN (" . $techIDArray . ")
                GROUP BY techstack_name.tech_id";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $techstackLayer[] = $row3['csv_values'] . '<br>';
}

$sql = "SELECT techstack_name.tech_id, 
            GROUP_CONCAT(DISTINCT CONCAT(instantiation_name, '<br>') 
            ORDER BY instantiation_name SEPARATOR '') AS csv_values
            FROM instantiation
            INNER JOIN techstack_summary
            ON instantiation.inst_id = techstack_summary.method_id
            INNER JOIN techstack_name
            ON techstack_summary.tech_id = techstack_name.tech_id
            WHERE techstack_name.tech_id IN (" . $techIDArray . ")
            GROUP BY techstack_name.tech_id";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $layerFeatures[] = $row3['csv_values'] . '<br>';
}

$sql = "SELECT techstack_name.tech_id, 
                GROUP_CONCAT(DISTINCT CONCAT(com_name, '<br>') 
                ORDER BY com_name SEPARATOR '') 
                AS csv_values
                FROM company
                INNER JOIN join_product_company
                ON company.com_id = join_product_company.company_id
                INNER JOIN product
                ON join_product_company.product_id = product.prod_id
                INNER JOIN techstack_summary
                ON product.prod_id = techstack_summary.prod_id
                INNER JOIN techstack_name
                ON techstack_summary.tech_id = techstack_name.tech_id
                WHERE techstack_name.tech_id 
                IN (" . $techIDArray . ")
                GROUP BY techstack_name.tech_id";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $vendor[] = $row3['csv_values'] . '<br>';
}

$sql = "SELECT techstack_name.tech_id, 
                GROUP_CONCAT(DISTINCT CONCAT(product_name, '<br>') 
                ORDER BY product_name SEPARATOR '') 
                AS csv_values
                FROM product
                INNER JOIN techstack_summary
                ON product.prod_id = techstack_summary.prod_id
                INNER JOIN techstack_name
                ON techstack_summary.tech_id = techstack_name.tech_id
                WHERE techstack_name.tech_id 
                IN (" . $techIDArray . ")
                GROUP BY techstack_name.tech_id;";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $products[] = $row3['csv_values'] . '<br>';
}

$sql = "SELECT techstack_name.tech_id, 
                GROUP_CONCAT(DISTINCT CONCAT(product_description, '<br>') 
                ORDER BY product_description SEPARATOR '') 
                AS csv_values
                FROM product
                INNER JOIN techstack_summary
                ON product.prod_id = techstack_summary.prod_id
                INNER JOIN techstack_name
                ON techstack_summary.tech_id = techstack_name.tech_id
                WHERE techstack_name.tech_id 
                IN (" . $techIDArray . ")
                GROUP BY techstack_name.tech_id;";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $prodDesc[] = $row3['csv_values'] . '<br>';
}

$techIDArray = explode(",", $techIDArray);
?>
<div class="summary-container">
    <div class="summary">
        <?php
        echo '<p><strong>Techstack Summary</strong></p>';

        echo "<form class='table-default' action='' method='POST'>";
        echo '<table>';
        echo '<tr>';
        echo '<th>Status</th>';
        echo '<th>Techstack Name</th>';
        echo '<th>Techstack Layer</th>';
        echo '<th>Techstack Layer Features</th>';
        echo '<th>Vendors</th>';
        echo '<th>Products</th>';
        echo '<th>';
        //echo '<label for="techstackFavoritesList"></label><br>';
        echo '<select name="techstackFavoritesList" id="techstackFavoritesList">';

        $techstackFavoritesList = techstackArrayDropdown($result2, $resultCheck2);

        if (!empty($techstackFavoritesList)) {
            foreach ($techstackFavoritesList as $favorite) {
                echo "<option value='$favorite[1]'>$favorite[0]</option>";
            }
            echo '</select>';
        }
        echo '</select><br>';
        echo '<div>';
        echo '<button type="submit" name="addToFavoritesTech" value="addToFavoritesTech">Add To Favorites</button>';
        echo '</div>';
        echo '</th>';
        echo '</tr>';

        $numRows = count($status);

        for ($i = 0; $i < $numRows; $i++) {
            echo '<tr>';
            echo '<td>' . $status[$i] . '</td>';
            echo "<td><a href='/utilityproject/query_tests/techstack.php?techstack={$techIDArray[$i]}'>$techstackName[$i]</td>";
            echo '<td>' . $techstackLayer[$i] . '</td>';
            echo '<td>' . $layerFeatures[$i] . '</td>';
            echo '<td>' . $vendor[$i] . '</td>';
            echo '<td>' . $products[$i] . '</td>';
            echo '<td><input type ="checkbox" name="techid[]" value="' . $techIDArray[$i] . '"></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</form>';
        ?>
    </div>
</div>
<?php
//echo '<br><br>';

$techIDArray = implode(',', $techIDArray);

$sql = "SELECT DISTINCT com_id, com_name, com_description 
                FROM company
                INNER JOIN join_product_company
                ON company.com_id = join_product_company.company_id
                INNER JOIN product
                ON join_product_company.product_id = product.prod_id
                INNER JOIN techstack_summary
                ON product.prod_id = techstack_summary.prod_id
                INNER JOIN techstack_name
                ON techstack_summary.tech_id = techstack_name.tech_id
                WHERE techstack_name.tech_id 
                IN (" . $techIDArray . ")";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $vendID[] = $row3['com_id'];
    $vendor1[] = $row3['com_name'];
    $vendorDesc[] = $row3['com_description'];
}

$techIDArray = explode(",", $techIDArray);
/*
                echo '<p><strong>Vendor Summary</strong></p>';
                
                echo "<form action='' method='POST' id='mainForm'>";
                echo '<table border="1">';
                echo '<tr>';
                echo '<th style="width: 200px;">Vendor Name</th>';
                echo '<th style="width: 350px;">Vendor Description</th>';
                echo '<th>';
                echo '<label for="vendorFavoritesList">Add to Favorites:</label><br>';
                echo '<select name="vendorFavoritesList" id="vendorFavoritesList">'; */
if (!empty($result3)) {
    $vendorFavoritesList = vendorArrayDropdown($result3, $resultCheck3);
}

/*
                   if(!empty($vendorFavoritesList)) {
                        foreach($vendorFavoritesList as $favorite) {
                            echo "<option value='$favorite[1]'>$favorite[0]</option>";
                        }
                        echo '</select>';
                   }
                echo '</select><br>';
                echo '<div>';
                echo '<button type="submit" name="addToFavoritesVendor" value="addToFavoritesVendor">Add To Favorites</button>';
                echo '</div>';
                echo '</th>';
                echo '</tr>'; */

$numRows = count($vendID);
/*       
                for ($i = 0; $i < $numRows; $i++) {
                    echo '<tr>';
                    echo "<td><a href='/query_tests/entity.php?entity={$vendID[$i]}'>$vendor1[$i]</td>";
                    echo "<td>".$vendorDesc[$i]."</td>";
                    echo '<td><input type ="checkbox" name="vendid[]" value="'.$vendID[$i].'"></td>'; 
                    echo '</tr>';
                }
                
                echo '</table>';
                echo '</form>';
                echo '<br><br>';
*/
$techIDArray = implode(',', $techIDArray);

$sql = "SELECT DISTINCT product.prod_id, product_name, product_description
                FROM product
                INNER JOIN techstack_summary
                ON product.prod_id = techstack_summary.prod_id
                INNER JOIN techstack_name
                ON techstack_summary.tech_id = techstack_name.tech_id
                WHERE techstack_name.tech_id 
                IN (" . $techIDArray . ")";

$result = mysqli_query($conn, $sql);

while ($row3 = mysqli_fetch_assoc($result)) {
    $newProducts[] = $row3['product_name'];
    $newProdID[] = $row3['prod_id'];
    $newProdDesc[] = $row3['product_description'];
}

$techIDArray = explode(",", $techIDArray);
/*
                echo '<p><strong>Product Summary</strong></p>';
                
                echo "<form action='' method='POST' id='mainForm'>";
                echo '<table border="1">';
                echo '<tr>';
                echo '<th style="width: 200px;">Product Name</th>';
                echo '<th style="width: 350px;">Product Description</th>';
                echo '<th>';
                echo '<label for="productFavoritesList">Add to Favorites:</label><br>';
                echo '<select name="productFavoritesList" id="productFavoritesList">';
*/
if (!empty($result90)) {
    $productFavoritesList = productArrayDropdown($result90, $resultCheck90);
}

/*
                   if(!empty($productFavoritesList)) {
                        foreach($productFavoritesList as $favorite) {
                          //  echo "<option value='$favorite[1]'>$favorite[0]</option>";
                        }
                        echo '</select>';
                   } >/
                echo '</select><br>';
                echo '<div>';
                echo '<button type="submit" name="addToFavoritesProduct" value="addToFavoritesProduct">Add To Favorites</button>';
                echo '</div>';
                echo '</th>';
                echo '</tr>';
            */
$numRows = count($vendID);
$numRowsProd = count($newProdID);
/*    
                for ($i = 0; $i < $numRows; $i++) {
                    echo '<tr>';
                    echo "<td><a href='/query_tests/product.php?product={$newProdID[$i]}'>$newProducts[$i]</td>";
                    echo "<td>".$newProdDesc[$i]."</td>";
                    echo '<td><input type ="checkbox" name="prodid[]" value="'.$newProdID[$i].'"></td>'; 
                    echo '</tr>';
                }
                
                echo '</table>';
                echo '</form>';
                
                echo '<br><br>';
                
                */
?>

<div class="summary-container">
    <div class="summary" style="margin-right: 20px;">
        <p><strong>Vendor Summary</strong></p>
        <form action='' method='POST' class='table-default' id='vendorForm'>
            <table border="1">
                <tr>
                    <th style="width: 200px;">Vendor Name</th>
                    <th style="width: 350px;">Vendor Description</th>
                    <th style="width: 100px;">
                        <!--<label for="vendorFavoritesList"></label><br>-->
                        <select name="vendorFavoritesList" id="vendorFavoritesList">
                            <?php
                            if (!empty($vendorFavoritesList)) {
                                foreach ($vendorFavoritesList as $favorite) {
                                    echo "<option value='$favorite[1]'>$favorite[0]</option>";
                                }
                            }
                            ?>
                        </select><br>
                        <button type="submit" name="addToFavoritesVendor" value="addToFavoritesVendor">Add To Favorites</button>
                    </th>
                </tr>
                <?php
                for ($i = 0; $i < $numRows; $i++) {
                    echo '<tr>';
                    echo "<td><a href='/utilityproject/query_tests/entity.php?entity={$vendID[$i]}'>$vendor1[$i]</a></td>";
                    echo "<td>{$vendorDesc[$i]}</td>";
                    echo '<td><input type ="checkbox" name="vendid[]" value="' . $vendID[$i] . '"></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </form>
    </div>
    <div class="summary" style="margin-right: 30px;">
        <p><strong>Product Summary</strong></p>
        <form action='' method='POST' class='table-default' id='productForm'>
            <table border="1">
                <tr>
                    <th style="width: 200px;">Product Name</th>
                    <th style="width: 350px;">Product Description</th>
                    <th style="width: 100px;">
                        <!--<label for="productFavoritesList"></label><br>-->
                        <select name="productFavoritesList" id="productFavoritesList">
                            <?php
                            // Loop through product favorites list and create options
                            if (!empty($productFavoritesList)) {
                                foreach ($productFavoritesList as $favorite) {
                                    echo "<option value='$favorite[1]'>$favorite[0]</option>";
                                }
                            }
                            ?>
                        </select><br>
                        <button type="submit" name="addToFavoritesProduct" value="addToFavoritesProduct">Add To Favorites</button>
                    </th>
                </tr>
                <?php
                // Loop through product data and create table rows
                for ($i = 0; $i < $numRowsProd; $i++) {
                    echo '<tr>';
                    echo "<td><a href='/utilityproject/query_tests/product.php?product={$newProdID[$i]}'>$newProducts[$i]</a></td>";
                    echo "<td>{$newProdDesc[$i]}</td>";
                    echo '<td><input type ="checkbox" name="prodid[]" value="' . $newProdID[$i] . '"></td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </form>
    </div>
</div>



<!--
                <script>
                    function submitForm(action) {
                        document.getElementById("mainForm").action = action;
                        document.getElementById("mainForm").submit();
                    }
                </script>
                -->