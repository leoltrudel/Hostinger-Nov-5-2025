<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="css/bootstrap.min.css"> -->
    <title>Utility Project</title>
</head>

<body>
    <?php
    include_once 'includes/dbh.inc.php';
    include_once 'includes/header.inc.php';
    include_once 'includes/session.inc.php';
    ?>
    <section class="index-intro">
        <?php
        /*   if (isset($_SESSION["useruid"])) {
                    echo "<p>Hello there, " . $_SESSION["useruid"] ."</p>";
                }
                else {
                    header("location: /query_tests/signup.php");
                    exit();
                } */
        ?>
    </section>

    <?php
    include_once 'includes/search.inc.php';
    ?>


    <!--
        <p>Infraststructure Type</p>
        <input type="checkbox" name="infrastructureType[]" value="Traditional Infrastructure"> Traditional Infrastructure<br>
        <input type="checkbox" name="infrastructureType[]" value="New Infrastructure"> New Infrastructure<br>
        <p>Force</p>
        <input type="checkbox" name="forceType[]" value="Economic"> Economic<br>
        <input type="checkbox" name="forceType[]" value="Policy Incentive"> Policy Incentive<br>
        <input type="checkbox" name="forceType[]" value="Regulatory"> Regulatory<br>
        <p>Force Item</p>

        <input type="checkbox" name="forceItem[]" value="Renewable Portfolio Standards (RPS)"> Renewable Portfolio Standards (RPS)<br>
        <input type="checkbox" name="forceItem[]" value="FERC Order 2222"> FERC Order 2222<br>
        <input type="checkbox" name="forceItem[]" value="Automation"> Automation<br>



        <p>Benefit</p>
        <input type="checkbox" name="benefitArray[]" value="Increase Customer Satisfaction"> Increase Customer Satisfaction<br>
        <input type="checkbox" name="benefitArray[]" value="Increase Customer Count"> Increase Customer Count<br>
        <input type="checkbox" name="benefitArray[]" value="Increase Reliability"> Increase Reliability<br>
        <input type="checkbox" name="benefitArray[]" value="Increase Resiliency"> Increase Resiliency<br>
        <input type="checkbox" name="benefitArray[]" value="Reduce O&M Costs"> Reduce O&M Costs<br>
        <input type="checkbox" name="benefitArray[]" value="Improve Customer Engagement"> Improve Customer Engagement<br>
        <input type="checkbox" name="benefitArray[]" value="Reduce Risk"> Reduce Risk<br>
        <input type="checkbox" name="benefitArray[]" value="Increase ROI"> Increase ROI<br>
        <input type="checkbox" name="benefitArray[]" value="Extend Useful Life of Assets"> Extend Useful Life of Assets<br>
        <input type="checkbox" name="benefitArray[]" value="Increase O&M Efficiency"> Increase O&M Efficiency<br>
        <input type="checkbox" name="benefitArray[]" value="Increase Uptime"> Increase Uptime<br>
        <input type="checkbox" name="benefitArray[]" value="Decrease Unplanned Downtime"> Decrease Unplanned Downtime<br>
        <input type="checkbox" name="benefitArray[]" value="Increase Revenue"> Increase Revenue<br>
        <input type="checkbox" name="benefitArray[]" value="Improve Safety"> Improve Safety<br>
        <input type="checkbox" name="benefitArray[]" value="Improve Compliance"> Improve Compliance<br>
        <input type="checkbox" name="benefitArray[]" value="Increase Security"> Increase Security<br>




        <p>KPI</p>
        <input type="checkbox" name="kpiArray[]" value="Annual labor cost per device"> Annual labor cost per device<br>
        <input type="checkbox" name="kpiArray[]" value="Average cost per job category"> Average cost per job category<br>
        <input type="checkbox" name="kpiArray[]" value="Average cost per job category"> Average cost per job category<br>
        <input type="checkbox" name="kpiArray[]" value="Average cost per megawatt produced"> Average cost per megawatt produced<br>
        <input type="checkbox" name="kpiArray[]" value="Average labor hours per device per year"> Average labor hours per device per year<br>
        <input type="checkbox" name="kpiArray[]" value="Average maintenance cost per mile of pipe/line/cable"> Average maintenance cost per mile of pipe/line/cable<br>
        <input type="checkbox" name="kpiArray[]" value="Average number of days each work order is past due"> Average number of days each work order is past due<br>
        <input type="checkbox" name="kpiArray[]" value="Average number of labor hours to complete a maintenance task"> Average number of labor hours to complete a maintenance task<br>
        <input type="checkbox" name="kpiArray[]" value="Average response time to fix breaks"> Average response time to fix breaks<br>
        <input type="checkbox" name="kpiArray[]" value="Average revenue per megawatt produced"> Average revenue per megawatt produced<br>
        <input type="checkbox" name="kpiArray[]" value="Average time to settle a rate case"> Average time to settle a rate case<br>
        <input type="checkbox" name="kpiArray[]" value="Consumption analyzed by units consumed and target reduction achieved"> Consumption analyzed by units consumed and target reduction achieved<br>
        <input type="checkbox" name="kpiArray[]" value="Crew productivity"> Crew productivity<br>
        <input type="checkbox" name="kpiArray[]" value="Electrical grid load"> Electrical grid load<br>
        <input type="checkbox" name="kpiArray[]" value="Equipment failure rate"> Equipment failure rate<br>
        <input type="checkbox" name="kpiArray[]" value="Equipment unavailability, hours per year: sustained fault"> Equipment unavailability, hours per year: sustained fault<br>
        <input type="checkbox" name="kpiArray[]" value="Equipment unavailability, house per year: temporary fault"> Equipment unavailability, house per year: temporary fault<br>
        <input type="checkbox" name="kpiArray[]" value="Equipment unavailability, house per year: unplanned maintenance"> Equipment unavailability, house per year: unplanned maintenance<br>
        <input type="checkbox" name="kpiArray[]" value="Maintenance backlog"> Maintenance backlog<br>
        <input type="checkbox" name="kpiArray[]" value="Maintenance cost as a percentage of manufacturing cost"> Maintenance cost as a percentage of manufacturing cost<br>
        <input type="checkbox" name="kpiArray[]" value="Maintenance technician's skill level improvement, year-over-year"> Maintenance technician's skill level improvement, year-over-year<br>
        <input type="checkbox" name="kpiArray[]" value="Mean time to repair"> Mean time to repair<br>
        <input type="checkbox" name="kpiArray[]" value="Number of complaints received by type"> Number of complaints received by type<br>
        <input type="checkbox" name="kpiArray[]" value="Number of customers who were cut off due to violations of regulations"> Number of customers who were cut off due to violations of regulations<br>
        <input type="checkbox" name="kpiArray[]" value="Number of disconnections"> Number of disconnections<br>
        <input type="checkbox" name="kpiArray[]" value="Number of pending work orders"> Number of pending work orders<br>
        <input type="checkbox" name="kpiArray[]" value="Number of staff per 1,000 customer connections"> Number of staff per 1,000 customer connections<br>
        <input type="checkbox" name="kpiArray[]" value="Outage time per event"> Outage time per event<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage of customers that would characterize their bills as accurate and timely"> Percentage of customers that would characterize their bills as accurate and timely<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage of possible power revenue billed"> Percentage of possible power revenue billed<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage reduction in number of complaints to the local regulatory body"> Percentage reduction in number of complaints to the local regulatory body<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage reduction in number of employee injuries"> Percentage reduction in number of employee injuries<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage of maintenance work orders requirement rework"> Percentage of maintenance work orders requirement rework<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage reduction in number of equipment failures"> Percentage reduction in number of equipment failures<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage of scheduled man-hours to total man-hours"> Percentage of scheduled man-hours to total man-hours<br>
        <input type="checkbox" name="kpiArray[]" value="Percentage of man-hours used for proactive work"> Percentage of man-hours used for proactive work<br>
        <input type="checkbox" name="kpiArray[]" value="Profit redistribution (rural electric coops)"> Profit redistribution (rural electric coops)<br>
        <input type="checkbox" name="kpiArray[]" value="Reduction in hazardous liquid spill notification time"> Reduction in hazardous liquid spill notification ttime<br>
        <input type="checkbox" name="kpiArray[]" value="Reduction or stabilization in rates (municipally owned utilities)"> Reduction or stabilization in rates (municipally owned utilities)<br>
        <input type="checkbox" name="kpiArray[]" value="Station unavailability: planned maintenance"> Station unavailability: planned maintenance<br>
        <input type="checkbox" name="kpiArray[]" value="Station unavailability: sustained fault"> Station unavailability: sustained fault<br>
        <input type="checkbox" name="kpiArray[]" value="Station unavailability: temporary fault"> Station unavailability: temporary fault<br>
        <input type="checkbox" name="kpiArray[]" value="Total shareholder returns (investor owned utilities)"> Total shareholder returns (investor owned utilities)<br>
        <input type="checkbox" name="kpiArray[]" value="Total time to complete new customer connections"> Total time to complete new customer connections<br>
        <input type="checkbox" name="kpiArray[]" value="Transformer/pump station reliability"> Transformer/pump station reliability<br>
        <input type="checkbox" name="kpiArray[]" value="Voltage deviations per year"> Voltage deviations per year<br>
        <p>Program</p>
        <input type="checkbox" name="programArray[]" value="Electric Vehicle Charging"> Electric Vehicle Charging<br>
        <input type="checkbox" name="programArray[]" value="Digital Substation"> Digital Substation<br>
        <input type="checkbox" name="programArray[]" value="Digital Customer Experience (CX)"> Digital Customer Experience (CX)<br>
        <input type="checkbox" name="programArray[]" value="Digital Customer Management"> Digital Customer Management<br>
        <input type="checkbox" name="programArray[]" value="Microgrid Development"> Microgrid Development<br>
        <input type="checkbox" name="programArray[]" value="Solar Farm Operations"> Solar Farm Operations<br>
        <input type="checkbox" name="programArray[]" value="Wind Farm Management"> Wind Farm Management<br>
        <input type="checkbox" name="programArray[]" value="Transmission Inspection Automation"> Transmission Inspection Automation<br>
        <input type="checkbox" name="programArray[]" value="Distribution Inspection Automation"> Distribution Inspection Automation<br>
        <p>Vendor</p>
        <input type="checkbox" name="companyArray[]" value="Alabama Power"> Alabama Power<br>
        <input type="checkbox" name="companyArray[]" value="Datch"> Datch<br>
        <input type="checkbox" name="companyArray[]" value="Buzz Solutions"> Buzz Solutions<br>
        <input type="checkbox" name="companyArray[]" value="New York Power Authority."> New York Power Authority.<br>
        <input type="checkbox" name="companyArray[]" value="IBM"> IBM<br>
        <input type="checkbox" name="companyArray[]" value="Microsoft"> Microsoft<br>
        <input type="checkbox" name="companyArray[]" value="Apple"> Apple<br>
        <input type="checkbox" name="companyArray[]" value="Skydio"> Skydio<br>
        <p>Product</p>
        <input type="checkbox" name="productArray[]" value="Datch Voice Assistant"> Datch Voice Assistant<br>
        <input type="checkbox" name="productArray[]" value="Maximo"> Maximo<br>
        <input type="checkbox" name="productArray[]" value="Apple iPad"> Apple iPad<br>
        <input type="checkbox" name="productArray[]" value="Surface"> Surface<br>
        <input type="checkbox" name="productArray[]" value="Apple iPhone"> Apple iPhone<br>
        <input type="checkbox" name="productArray[]" value="PowerAI"> PowerAI<br>
        <input type="checkbox" name="productArray[]" value="Skydio 2+"> Skydio 2+<br>
        <input type="checkbox" name="productArray[]" value="Skydio X2"> Skydio X2<br>
        <input type="checkbox" name="productArray[]" value="Skydio Dock"> Skydio Dock<br>
        <input type="checkbox" name="productArray[]" value="Skydio 3D Scan"> Skydio 3D Scan<br>
        <input type="checkbox" name="productArray[]" value="Skydio Cloud"> Skydio Cloud<br>
        <input type="checkbox" name="productArray[]" value="Skydio Autonomy"> Skydio Autonomy<br>
        <p>Techstack Attributes</p>
        <input type="checkbox" name="techstackAttribute[]" value=".csv"> .csv<br>
        <input type="checkbox" name="techstackAttribute[]" value="JSON"> JSON<br>
        <input type="checkbox" name="techstackAttribute[]" value="SOC2"> SOC2<br>
        <input type="checkbox" name="techstackAttribute[]" value="Single Sign-On (SSO)"> Single Sign-On (SSO)<br>
        <p>Value Chain</p>
        <input type="checkbox" name="valueChain[]" value="Planning"> Planning<br>
        <input type="checkbox" name="valueChain[]" value="Construction & Implementation"> Construction & Implementation<br>
        <input type="checkbox" name="valueChain[]" value="Operations & Maintenance"> Operations & Maintenance<br>
        <input type="checkbox" name="valueChain[]" value="Decommissioning"> Decommissioning<br>
        <input type="checkbox" name="valueChain[]" value="Customer"> Customer<br>
        <input type="checkbox" name="valueChain[]" value="Administrative"> Administrative<br>
        <input type="checkbox" name="valueChain[]" value="Technical"> Technical<br>
        <p>Browse By:</p>
        <input type="checkbox" name="browseBy[]" value="Case Study"> Case Study<br>
        <input type="checkbox" name="browseBy[]" value="Favorites"> Favorites<br><br>-->
    <br>
    <button type="submit" name="submitQuery">Submit Query</button><br><br>
    <button type="button" onclick="window.location.reload();" name="clearAll">Clear All</button><br>
    <script>
        function toggleOptions(id) {
            var options = document.getElementById(id);
            if (options.style.display === "none") {
                options.style.display = "block";
            } else {
                options.style.display = "none";
            }
        }
    </script>
</body>

</html>