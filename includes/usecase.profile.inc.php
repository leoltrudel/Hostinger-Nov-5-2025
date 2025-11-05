<?php
include_once 'addnewlist.inc.php';
?>
<section>
    <!-- <h1>Use Case Profile</h1> -->
    <div class="summary-container">
        <div>
            <h2>Use Case Details</h2>
            <dl>
                <dt><strong>Use Case Name:</strong></dt>
                <dd><a>
                        <?php
                        $sql = "SELECT use_case_name FROM use_case WHERE uc_id ={$idValue};";
                        $result = mysqli_query($conn, $sql);
                        $resultCheck = mysqli_num_rows($result);
                        if ($resultCheck > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo $row['use_case_name'];
                            }
                        }
                        echo '<br><br>';

                        ?>
                    </a></dd>


                <!--<dt>Favorite:</dt>
            <dd>Your Favorite</dd>-->

                <dt><strong>Use Case Description:</strong></dt>
                <dd><a>
                        <?php
                        $sql = "SELECT use_case_description FROM use_case WHERE uc_id ={$idValue};";
                        $result = mysqli_query($conn, $sql);
                        $resultCheck = mysqli_num_rows($result);
                        if ($resultCheck > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo $row['use_case_description'];
                            }
                        }
                        echo '<br><br>';
                        ?>
                    </a></dd>
            </dl>
        </div>
        <?php
        echo '<div style="position: absolute; top: 1000; right: 5%;">' . $formOutput . '</div>';
        ?>
    </div>
    <div class="summary-container" title="test">
        <div class="scrollable-lists-container">
            <?php
            // Execute the first query to fetch asset categories
            $sql = "SELECT DISTINCT asset_category_category 
                FROM asset_category 
                INNER JOIN asset_type ON asset_category.ac_id = asset_type.ac_id
                INNER JOIN asset_component ON asset_type.at_id = asset_component.asset_type_id
                INNER JOIN join_use_case_asset_component ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
                INNER JOIN use_case ON join_use_case_asset_component.use_case_id = use_case.uc_id
                WHERE uc_id = {$idValue};";
            $result = mysqli_query($conn, $sql);

            // Check if there are any results
            if (mysqli_num_rows($result) > 0) {
                echo '<div class="list-container" style="margin-left: -2%; margin-right: 5px;">'; // Adjust margin-right -->
                echo '<h><strong>Asset Category</strong></h><br><br>';
                // Output the first scrollable list (Asset Category)
                echo '<div class="scrollable-list">';
                echo '<ul>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<li>' . $row['asset_category_category'] . '</li>';
                }
                echo '</ul>';
                echo '</div>'; // End of Asset Category list
                echo '</div>'; // End of first list container

                // Reset the mysqli_data_seek to fetch results from the beginning
                mysqli_data_seek($result, 0);
            } else {
                echo 'This use case is asset-lite.';
            }

            // Execute the second query to fetch asset types
            $sql = "SELECT DISTINCT asset_type 
                FROM asset_type 
                INNER JOIN asset_component ON asset_type.at_id = asset_component.asset_type_id
                INNER JOIN join_use_case_asset_component ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
                INNER JOIN use_case ON join_use_case_asset_component.use_case_id = use_case.uc_id
                WHERE uc_id = {$idValue};";
            $result = mysqli_query($conn, $sql);

            // Check if there are any results
            if (mysqli_num_rows($result) > 0) {
                // Output the second label (Asset Type)
                echo '<div class="list-container" style="margin-left: 5px; margin-right: 5px;">'; // Adjust margin-left and margin-right -->
                echo '<h><strong>Asset Type</strong></h><br><br>';
                // Output the second scrollable list (Asset Type)
                echo '<div class="scrollable-list">';
                echo '<ul>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<li>' . $row['asset_type'] . '</li>';
                }
                echo '</ul>';
                echo '</div>'; // End of Asset Type list
                echo '</div>'; // End of second list container

                // Reset the mysqli_data_seek to fetch results from the beginning
                mysqli_data_seek($result, 0);
            }

            // Execute the third query to fetch asset components
            $sql = "SELECT DISTINCT component_name
                FROM asset_component
                INNER JOIN join_use_case_asset_component ON asset_component.acom_id = join_use_case_asset_component.asset_component_id
                INNER JOIN use_case ON join_use_case_asset_component.use_case_id = use_case.uc_id
                WHERE uc_id = {$idValue};";
            $result = mysqli_query($conn, $sql);

            // Check if there are any results
            if (mysqli_num_rows($result) > 0) {
                // Output the third label (Asset Component)
                echo '<div class="list-container" style="margin-left: 5px; margin-right: -2%;">'; // Adjust margin-left and margin-right
                echo '<h><strong>Asset Component</strong></h><br><br>';
                // Output the third scrollable list (Asset Component)
                echo '<div class="scrollable-list">';
                echo '<ul>';
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<li>' . $row['component_name'] . '</li>';
                }
                echo '</ul>';
                echo '</div>'; // End of Asset Component list
                echo '</div>'; // End of third list container
            }
            ?>
        </div>
    </div>
    <?php
    //echo '<br>';
    //echo '<div style="position: absolute; top: 1000; right: 30px;">' . $formOutput . '</div>';


    $sql = "SELECT DISTINCT tech_id
                    AS csv_values
                    FROM techstack_name 
                    INNER JOIN join_use_case_techstack
                    ON techstack_name.tech_id = join_use_case_techstack.techstack_id
                    INNER JOIN use_case
                    ON join_use_case_techstack.use_case_id = use_case.uc_id
                    WHERE uc_id = {$idValue}";

    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);

    // This gets the tech_id, based on the uc_id in the query string. It also gets the techstack_name and verified columns. 
    $techIDArray = array();

    if ($resultCheck > 0) {
        require 'includes/usecase.techstack_table.inc.php';
        while ($arrayVariable = mysqli_fetch_assoc($result)) {
            $techIDArray[] = $arrayVariable;
        }
    }

    ?>
    </table>

    <?php
    // Execute the first query
    $sqlBenefit = "SELECT DISTINCT benefit_name
                FROM benefit
                INNER JOIN join_use_case_benefit
                ON join_use_case_benefit.benefit = benefit.ben_id
                INNER JOIN use_case
                ON join_use_case_benefit.use_case = use_case.uc_id
                WHERE uc_id ={$idValue}";
    $resultBenefit = mysqli_query($conn, $sqlBenefit);
    $benefitData = "";
    if (mysqli_num_rows($resultBenefit) > 0) {
        while ($row = mysqli_fetch_assoc($resultBenefit)) {
            $benefitData .= $row['benefit_name'] . "

<br>";
        }
    }

    $sqlKPI = "SELECT DISTINCT kpi_name
    FROM kpi
    INNER JOIN join_use_case_kpi
    ON kpi.kpi_id = join_use_case_kpi.kpi_id
    INNER JOIN use_case
    ON join_use_case_kpi.use_case_id = use_case.uc_id
    WHERE uc_id ={$idValue}";
    $resultKPI = mysqli_query($conn, $sqlKPI);
    $kpiData = "";
    if (mysqli_num_rows($resultKPI) > 0) {
        while ($row = mysqli_fetch_assoc($resultKPI)) {
            $kpiData .= $row['kpi_name'] . "

<br>";
        }
    }

    // Execute the second query
    $sqlChangeCatalyst = "SELECT DISTINCT force_type_name
                        FROM force_type
                        INNER JOIN force_item
                        ON force_type.force_type_id = force_item.force_type_id
                        INNER JOIN join_use_case_force_item
                        ON join_use_case_force_item.force_item_id = force_item.force_id
                        INNER JOIN use_case
                        ON join_use_case_force_item.use_case_id = use_case.uc_id
                        WHERE uc_id ={$idValue}";
    $resultChangeCatalyst = mysqli_query($conn, $sqlChangeCatalyst);
    $changeCatalystData = "";
    if (mysqli_num_rows($resultChangeCatalyst) > 0) {
        while ($row = mysqli_fetch_assoc($resultChangeCatalyst)) {
            $changeCatalystData .= $row['force_type_name'] . "

<br>";
        }
    }

    // Execute the third query
    $sqlChangeDrivers = "SELECT DISTINCT force_name
                    FROM force_item
                    INNER JOIN join_use_case_force_item
                    ON join_use_case_force_item.force_item_id = force_item.force_id
                    INNER JOIN use_case
                    ON join_use_case_force_item.use_case_id = use_case.uc_id
                    WHERE uc_id ={$idValue}";
    $resultChangeDrivers = mysqli_query($conn, $sqlChangeDrivers);
    $changeDriversData = "";
    if (mysqli_num_rows($resultChangeDrivers) > 0) {
        while ($row = mysqli_fetch_assoc($resultChangeDrivers)) {
            $changeDriversData .= $row['force_name'] . "

<br>";
        }
    }

    // Execute the fourth query
    //$kpiData = '{Placeholder}<br>';
    ?>
    <div class="summary-container">
        <div class="summary">
            <h><br><strong>Economic Attributes</strong><br><br></h>
            <div class='table-default'>
                <?php
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Benefit</th>";
                echo "<th>KPIs</th>";
                echo "<th>Change Catalyst</th>";
                echo "<th>Change Drivers</th>";
                echo "</tr>";
                echo "<tr>";
                echo "<td>$benefitData</td>";
                echo "<td>$kpiData</td>";
                echo "<td>$changeCatalystData</td>";
                echo "<td>$changeDriversData</td>";
                echo "</tr>";
                echo "</table>";
                ?>
            </div>
        </div>
        <?php

        // Execute the first query
        $sqlProgram = "SELECT DISTINCT program_name 
    FROM program 
    INNER JOIN join_use_case_program
    ON join_use_case_program.program_id = program.prog_id
    INNER JOIN use_case
    ON join_use_case_program.use_case_id = use_case.uc_id
    WHERE uc_id ={$idValue};";
        $resultProgram = mysqli_query($conn, $sqlProgram);
        $programData = "";
        if (mysqli_num_rows($resultProgram) > 0) {
            while ($row = mysqli_fetch_assoc($resultProgram)) {
                $programData .= $row['program_name'] . "

<br>";
            }
        }

        // Execute the second query
        $sqlValueChain = "SELECT DISTINCT vc_name 
    FROM value_chain 
    INNER JOIN join_use_case_value_chain
    ON join_use_case_value_chain.value_chain_id = value_chain.vc_id
    INNER JOIN use_case
    ON join_use_case_value_chain.use_case_id = use_case.uc_id
    WHERE uc_id ={$idValue}
    ORDER BY vc_id;";
        $resultValueChain = mysqli_query($conn, $sqlValueChain);
        $valueChainData = "";
        if (mysqli_num_rows($resultValueChain) > 0) {
            while ($row = mysqli_fetch_assoc($resultValueChain)) {
                $valueChainData .= $row['vc_name'] . "

<br>";
            }
        }

        $sqlWorkstream = "SELECT DISTINCT workstream_name 
        FROM workstream 
        INNER JOIN join_use_case_workstream
        ON join_use_case_workstream.workstream_number = workstream.work_id
        INNER JOIN use_case
        ON join_use_case_workstream.use_case_number = use_case.uc_id
        WHERE uc_id ={$idValue}
        ORDER BY workstream_name;";
        $resultWorkstream = mysqli_query($conn, $sqlWorkstream);
        $workstreamData = "";
        if (mysqli_num_rows($resultWorkstream) > 0) {
            while ($row = mysqli_fetch_assoc($resultWorkstream)) {
                $workstreamData .= $row['workstream_name'] . "
    
    <br>";
            }
        }

        $sqlTrigger = "SELECT DISTINCT workstream_trigger 
        FROM workstream_trigger
        INNER JOIN join_workstream_workstream_trigger
        ON workstream_trigger.workstream_trigger_id = join_workstream_workstream_trigger.trigger_id
        INNER JOIN workstream
        ON join_workstream_workstream_trigger.workstream_id = workstream.work_id 
        INNER JOIN join_use_case_workstream
        ON join_use_case_workstream.workstream_number = workstream.work_id
        INNER JOIN use_case
        ON join_use_case_workstream.use_case_number = use_case.uc_id
        WHERE uc_id ={$idValue}
        ORDER BY workstream_trigger;";
        $resultTrigger = mysqli_query($conn, $sqlTrigger);
        $triggerData = [];
        if (mysqli_num_rows($resultTrigger) > 0) {
            while ($row = mysqli_fetch_assoc($resultTrigger)) {
                $triggerData[] = $row['workstream_trigger'];
                echo "<br>";
            }
        }
        ?>
        <?php
        //     $workstreamData = '{Placeholder}<br>';
        //$WorkTriggerData = '{Placeholder}<br>';
        ?>
        <div class="summary">
            <h><br><strong>Internal Attributes</strong><br><br></h>
            <div class='table-default'>
                <?php
                echo "<table border='1'>";
                echo "<tr>";
                echo "<th>Program</th>";
                echo "<th>Value Chain</th>";
                echo "<th>Workstream</th>";
                echo "<th>Workstream Trigger</th>";
                echo "</tr>";
                echo "<tr>";
                echo "<td>$programData</td>";
                echo "<td>$valueChainData</td>";
                echo "<td>$workstreamData</td>";
                echo "<td>";
                foreach ($triggerData as $trigger) {
                    echo $trigger;
                    echo '<br>';
                }
                echo "</td>";
                echo "</tr>";
                echo "</table>";
                ?>
            </div>
        </div>
</section>
</div>

<?php
