    <style>
        /* General table styles for all tables */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        /* Header background and bold text */
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Highlight row for "Your score" */
        .highlight {
            font-weight: bold;
        }

        /* Green and red color coding */
        .green {
            color: green;
        }

        .red {
            color: red;
        }

        /* Specific styling for the "Reliability Metrics" table */
        .reliability-metrics th:nth-child(n+2) {
            width: 14.28%;
            /* Ensures even width across the six columns for the second row */
        }

        .reliability-metrics .second-row th {
            table-layout: fixed;
        }

        /* For all other tables, make sure columns are spaced evenly */
        table:not(.reliability-metrics) {
            table-layout: fixed;
        }

        table:not(.reliability-metrics) th,
        table:not(.reliability-metrics) td {
            width: 14.28%;
            /* Evenly space the columns for all other tables */
        }
    </style>
    <div class="table-default">
        <table class="data-table-1 reliability-metrics">
            <tr>
                <strong>Reliability Metrics</strong>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th colspan="3"> All Events (Last 12 Months)</th>
                    <th colspan="3"> Without Major Events (Last 12 Months)</th>
                </tr>
                <tr class="second-row">
                    <th></th>
                    <th><input type="checkbox"> SAIDI (minutes per year)</th>
                    <th><input type="checkbox"> SAIFI (times per year)</th>
                    <th><input type="checkbox"> CAIDI (minutes per interruption)</th>
                    <th><input type="checkbox"> SAIDI (minutes per year)</th>
                    <th><input type="checkbox"> SAIFI (times per year)</th>
                    <th><input type="checkbox"> CAIDI (minutes per interruption)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">211.4</td>
                    <td class="highlight">1.181</td>
                    <td class="highlight">148.1</td>
                    <td class="highlight">118.1</td>
                    <td class="highlight">1.521</td>
                    <td class="highlight">102.7</td>
                </tr>
                <tr>
                    <td>All US</td>
                    <td style="color: green;">215.7</td>
                    <td style="color: green;">1.200</td>
                    <td style="color: green;">179.8</td>
                    <td style="color: red;">106.1</td>
                    <td style="color: red;">0.992</td>
                    <td style="color: green;">106.9</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: green;">226.7</td>
                    <td style="color: red;">0.922</td>
                    <td style="color: green;">153.8</td>
                    <td style="color: red;">104.4</td>
                    <td style="color: red;">1.003</td>
                    <td style="color: green;">103.3</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: green;">232.6</td>
                    <td style="color: green;">1.416</td>
                    <td style="color: green;">189.2</td>
                    <td style="color: red;">108.9</td>
                    <td style="color: red;">1.046</td>
                    <td style="color: green;">124.7</td>
                </tr>
            </tbody>
        </table>
    </div>

    <br>
    <div class="table-default">
        <table class="data-table-1">
            <tr>
                <strong>Customer Metrics</strong>
            </tr>
            <tr>
                <th></th>
                <th><input type="checkbox"> Revenue per Customer</th>
                <th><input type="checkbox"> Customer Growth Rate</th>
                <th><input type="checkbox"> Capital Expenditure per Customer</th>
                <th><input type="checkbox"> O&M Cost per Customer</th>
                <th><input type="checkbox"> Net Income per Customer</th>
                <th><input type="checkbox"> Operating Expense per Customer</th>
            </tr>
            <tr>
                <td class="highlight">Your score</td>
                <td class="highlight">$1,668</td>
                <td class="highlight">1.43%</td>
                <td class="highlight">$338</td>
                <td class="highlight">$588</td>
                <td class="highlight">$42</td>
                <td class="highlight">$700</td>
            </tr>
            <tr>
                <td>Custom peers</td>
                <td class="green">$1,598</td>
                <td class="green">1.03%</td>
                <td class="green">$402</td>
                <td class="red">$516</td>
                <td class="red">$50</td>
                <td class="red">$630</td>
            </tr>
            <tr>
                <td>Peers by region</td>
                <td class="red">$1,670</td>
                <td class="green">1.26%</td>
                <td class="green">$422</td>
                <td class="red">$587</td>
                <td class="red">$51</td>
                <td class="red">$610</td>
            </tr>
            <tr>
                <td>Peers by size</td>
                <td class="green">$1,626</td>
                <td class="green">1.31%</td>
                <td class="green">$359</td>
                <td class="red">$478</td>
                <td class="red">$69</td>
                <td class="green">$720</td>
            </tr>
        </table>
        <br>
        <table class="data-table-1">
            <tr>
                <strong></strong>
            </tr>
            <thead>
                <!-- <tr>
                <th colspan="6">Customers per Mile of Distribution Line</th>
            </tr>-->
                <tr>
                    <th></th>
                    <th><input type="checkbox"> Customers per Mile of Distribution Line</th>
                    <th><input type="checkbox"> Energy Sales per Customer</th>
                    <th><input type="checkbox"> Distribution Cost per Customer</th>
                    <th><input type="checkbox"> Cost of Distribution Maintenance per Customer</th>
                    <th><input type="checkbox"> Operating Margin per Customer</th>
                    <th><input type="checkbox"> Peak Demand per Customer</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">30</td>
                    <td class="highlight">9.8MWh</td>
                    <td class="highlight">$222</td>
                    <td class="highlight">$196</td>
                    <td class="highlight">$74</td>
                    <td class="highlight">1.6kW</td>
                </tr>
                <tr>
                    <td>Custom peers</td>
                    <td style="color: green;">21</td>
                    <td style="color: green;">9.6MWh</td>
                    <td style="color: red;">$212</td>
                    <td style="color: green;">$209</td>
                    <td style="color: green;">$74</td>
                    <td style="color: red;">1.4kW</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: red;">35</td>
                    <td style="color: green;">9.5MWh</td>
                    <td style="color: red;">$188</td>
                    <td style="color: green;">$216</td>
                    <td style="color: green;">$55</td>
                    <td style="color: red;">1.5kW</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: green;">24</td>
                    <td style="color: green;">9.6MWh</td>
                    <td style="color: red;">$164</td>
                    <td style="color: green;">$216</td>
                    <td style="color: green;">$66</td>
                    <td style="color: red;">1.5kW</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-default">
        <table class="data-table-1">
            <tr>
                <strong>Transmission (Operational) Metrics</strong>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th><input type="checkbox"> Transmission Availability</th>
                    <th><input type="checkbox"> Transmission Congestion Ratio</th>
                    <th><input type="checkbox"> Transmission Line Loss Ratio</th>
                    <th><input type="checkbox"> Transmission Line Load Factor</th>
                    <th><input type="checkbox"> Transmission Line Utilization Factor</th>
                    <th><input type="checkbox"> Average Age of Transmission Lines</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">99.1%</td>
                    <td class="highlight">12.9%</td>
                    <td class="highlight">3.40%</td>
                    <td class="highlight">5.44%</td>
                    <td class="highlight">58.2%</td>
                    <td class="highlight">31</td>
                </tr>
                <tr>
                    <td>Custom peers</td>
                    <td style="color: red;">99.3%</td>
                    <td style="color: red;">10.9%</td>
                    <td style="color: red;">2.88%</td>
                    <td style="color: green;">5.61%</td>
                    <td style="color: green;">55.0%</td>
                    <td style="color: green;">35</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: red;">99.3%</td>
                    <td style="color: red;">11.2%</td>
                    <td style="color: green;">3.52%</td>
                    <td style="color: red;">5.39%</td>
                    <td style="color: green;">54.3%</td>
                    <td style="color: green;">32</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: red;">99.2%</td>
                    <td style="color: red;">11.1%</td>
                    <td style="color: green;">4.00%</td>
                    <td style="color: green;">5.85%</td>
                    <td style="color: green;">55.5%</td>
                    <td style="color: green;">32</td>
                </tr>
            </tbody>
        </table>
        <br>
        <table class="data-table-1">
            <tr>
                <strong>Transmission (Financial) Metrics</strong>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th><input type="checkbox"> Transmission System Investment per Customer</th>
                    <th><input type="checkbox"> Transmission Return on Investment (ROI)</th>
                    <th><input type="checkbox"> Transmission Cost per MWh Delivered</th>
                    <th><input type="checkbox"> O&M Cost per Mile of Transmission Line</th>
                    <th><input type="checkbox"> Transmission Capital Expenditure per Mile</th>
                    <th><input type="checkbox"> Energy Delivered via Transmission Lines per Mile (per annum)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">$450</td>
                    <td class="highlight">8.5%</td>
                    <td class="highlight">$12.76</td>
                    <td class="highlight">$7,117</td>
                    <td class="highlight">$675,945</td>
                    <td class="highlight">17,993MWh</td>
                </tr>
                <tr>
                    <td>Custom peers</td>
                    <td style="color: green;">$383</td>
                    <td style="color: red;">8.6%</td>
                    <td style="color: red;">$11.52</td>
                    <td style="color: red;">$6,069</td>
                    <td style="color: green;">$837,189</td>
                    <td style="color: green;">15,887MWh</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: red;">$461</td>
                    <td style="color: red;">8.8%</td>
                    <td style="color: red;">$10.28</td>
                    <td style="color: red;">$5,833</td>
                    <td style="color: green;">$815,191</td>
                    <td style="color: green;">14,686MWh</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: red;">$480</td>
                    <td style="color: red;">8.8%</td>
                    <td style="color: green;">$13.55</td>
                    <td style="color: red;">$6,171</td>
                    <td style="color: green;">$944,227</td>
                    <td style="color: green;">14,993MWh</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-default">
        <table class="data-table-1">
            <tr>
                <strong>Substation Metrics</strong>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th><input type="checkbox"> Transmission Substation 1 Capacity per Mile</th>
                    <th><input type="checkbox"> Transmission Substation 2 Capacity per Mile</th>
                    <th><input type="checkbox"> Transmission Substation 3 Capacity per Mile</th>
                    <th><input type="checkbox"> Transmission Substation 1 Maintenance Cost per MVA</th>
                    <th><input type="checkbox"> Transmission Substation 2 Maintenance Cost per MVA</th>
                    <th><input type="checkbox"> Transmission Substation 3 Maintenance Cost per MVA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">3.5MVA</td>
                    <td class="highlight">2.4MVA</td>
                    <td class="highlight">2.3MVA</td>
                    <td class="highlight">$3,286</td>
                    <td class="highlight">$3,388</td>
                    <td class="highlight">$6,428</td>
                </tr>
                <tr>
                    <td>Custom peers</td>
                    <td style="color: green;">3.1MVA</td>
                    <td style="color: red;">3.1MVA</td>
                    <td style="color: green;">3.1MVA</td>
                    <td style="color: green;">$5,544</td>
                    <td style="color: green;">$5,544</td>
                    <td style="color: red;">$5,544</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: green;">2.8MVA</td>
                    <td style="color: red;">2.8MVA</td>
                    <td style="color: green;">2.8MVA</td>
                    <td style="color: green;">$5,212</td>
                    <td style="color: green;">$5,212</td>
                    <td style="color: red;">$5,212</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: green;">2.9MVA</td>
                    <td style="color: red;">2.9MVA</td>
                    <td style="color: green;">2.9MVA</td>
                    <td style="color: green;">$5,878</td>
                    <td style="color: green;">$5,878</td>
                    <td style="color: red;">$5,878</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-default">
        <table class="data-table-1">
            <tr>
                <strong>Generation Plant 1 Metrics</strong>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th><input type="checkbox"> Capacity Factor</th>
                    <th><input type="checkbox"> Load Factor</th>
                    <th><input type="checkbox"> Fuel Cost per MWh</th>
                    <th><input type="checkbox"> Plant Availability Factor</th>
                    <th><input type="checkbox"> Generation O&M Costs per MWh</th>
                    <th><input type="checkbox"> O&M Cost per MW of Capacity</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">58%</td>
                    <td class="highlight">65%</td>
                    <td class="highlight">$26</td>
                    <td class="highlight">94%</td>
                    <td class="highlight">$2.61</td>
                    <td class="highlight">$28,449</td>
                </tr>
                <tr>
                    <td>Custom peers</td>
                    <td style="color: green;">52%</td>
                    <td style="color: green;">55%</td>
                    <td style="color: red;">$24</td>
                    <td style="color: green;">90%</td>
                    <td style="color: green;">$2.99</td>
                    <td style="color: green;">$34,562</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: green;">54%</td>
                    <td style="color: green;">57%</td>
                    <td style="color: green;">$31</td>
                    <td style="color: green;">89%</td>
                    <td style="color: green;">$3.14</td>
                    <td style="color: green;">$33,889</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: green;">52%</td>
                    <td style="color: green;">61%</td>
                    <td style="color: red;">$23</td>
                    <td style="color: green;">92%</td>
                    <td style="color: green;">$3.48</td>
                    <td style="color: green;">$31,040</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-default">
        <table class="data-table-1">
            <tr>
                <strong>Distribution Metrics</strong>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th><input type="checkbox"> Distribution Line Cost per Mile</th>
                    <th><input type="checkbox"> Distribution Line Loss Ratio</th>
                    <th><input type="checkbox"> Distribution O&M Cost per Mile</th>
                    <th><input type="checkbox"> Distribution O&M Cost per Customer</th>
                    <th><input type="checkbox"> Distribution Capital Expenditure per Mile</th>
                    <th><input type="checkbox"> Distribution Load Factor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">$270,482</td>
                    <td class="highlight">4.85%</td>
                    <td class="highlight">$18,833</td>
                    <td class="highlight">$219</td>
                    <td class="highlight">$217,430</td>
                    <td class="highlight">50.1%</td>
                </tr>
                <tr>
                    <td>Custom peers</td>
                    <td style="color: green;">$465,553</td>
                    <td style="color: green;">4.98%</td>
                    <td style="color: red;">$14,085</td>
                    <td style="color: red;">$195</td>
                    <td style="color: red;">$380,584</td>
                    <td style="color: red;">59.5%</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: red;">$241,792</td>
                    <td style="color: green;">5.32%</td>
                    <td style="color: red;">$15,927</td>
                    <td style="color: red;">$204</td>
                    <td style="color: green;">$410,482</td>
                    <td style="color: red;">52.8%</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: green;">$323,367</td>
                    <td style="color: green;">5.84%</td>
                    <td style="color: red;">$12,058</td>
                    <td style="color: green;">$222</td>
                    <td style="color: red;">$99,343</td>
                    <td style="color: red;">58.3%</td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <div class="table-default">
        <table class="data-table-1">
            <tr>
                <strong>Employee Metrics</strong>
            </tr>
            <thead>
                <tr>
                    <th></th>
                    <th><input type="checkbox"> Revenue per Employee</th>
                    <th><input type="checkbox"> O&M Cost per Employee</th>
                    <th><input type="checkbox"> Customers per Employee</th>
                    <th><input type="checkbox"> Net Income per Employee</th>
                    <th><input type="checkbox"> Operating Expense per Employee</th>
                    <th><input type="checkbox"> Customers per Full-Time Equivalent (FTE)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="highlight">Your score</td>
                    <td class="highlight">$816,521</td>
                    <td class="highlight">$320,008</td>
                    <td class="highlight">370</td>
                    <td class="highlight">$55,792</td>
                    <td class="highlight">$673,761</td>
                    <td class="highlight">874</td>
                </tr>
                <tr>
                    <td>Custom peers</td>
                    <td style="color: red;">$903,609</td>
                    <td style="color: green;">$448,084</td>
                    <td style="color: green;">369</td>
                    <td style="color: red;">$102,895</td>
                    <td style="color: red;">$531,111</td>
                    <td style="color: green;">642</td>
                </tr>
                <tr>
                    <td>Peers by region</td>
                    <td style="color: red;">$1,063,292</td>
                    <td style="color: green;">$334,235</td>
                    <td style="color: red;">385</td>
                    <td style="color: red;">$141,033</td>
                    <td style="color: green;">$687,310</td>
                    <td style="color: green;">611</td>
                </tr>
                <tr>
                    <td>Peers by size</td>
                    <td style="color: red;">$1,262,320</td>
                    <td style="color: green;">$350,907</td>
                    <td style="color: green;">312</td>
                    <td style="color: red;">$142,738</td>
                    <td style="color: red;">$596,375</td>
                    <td style="color: green;">437</td>
                </tr>
            </tbody>
        </table>
    </div>

    </body>

    </html>