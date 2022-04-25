<!DOCTYPE html>
<html>
<body>
<style>
    body{
        background: palegoldenrod;
        font-family: Helvetica, sans-serif;
        align-content: center;

    }
    footer{
        text-align: center;

    }
    .column {
        float: left;
        width: 50%;
        padding: 10px;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }
    main {
        display: flex;
        justify-content: center;
    }

    table {
        max-width: 100%;
    }

    tr:nth-child(odd) {
        background-color: #eee;
    }

    th {
        background-color: #555;
        color: #fff;
    }

    th,
    td {
        text-align: left;
        padding: 0.5em 1em;
</style>

<h1 style="text-align: center"><strong>City-Search</strong></h1>

<?php
function array_to_table($array) {
    $keys = array_keys($array);
    echo "<table style='margin-left: auto; margin-right: auto;'> <tbody>";
    for($i=0; $i < count($keys); $i++) {
        echo "<tr> <td> " . $keys[$i] . "</td> <td>" . $array[$keys[$i]] . "</td> </tr>";
    }
    echo "</tbody> </table>";

}

if (empty($_GET["location"]) and empty($_GET["adults"]) and empty($_GET["adultswork"]) and empty($_GET["kids"]) and empty($_GET["position"])){
    echo "<form action='tests.php' method='GET'>";
    echo "<br>";
    echo "<textarea cols=25 rows = 1 name='location' placeholder='City'></textarea>";
    echo "<br>";
    echo "<textarea cols=25 rows = 1 name='adults' placeholder='Adults'></textarea>";
    echo "<br>";
    echo "<textarea cols=25 rows = 1 name='adultswork' placeholder='Adults Working'></textarea>";
    echo "<br>";
    echo "<textarea cols=25 rows = 1 name='kids' placeholder='Kids'></textarea>";
    echo "<br>";
    echo "<textarea cols=25 rows = 1 name='position' placeholder='Position'></textarea>";
    echo "<br>";
    echo "<textarea cols=25 rows = 1 name='yoe' placeholder='Years Of Experience'></textarea>";
    echo "<br>";
    echo "<input type=submit>";
    echo "</form>";

}
else {
    $col_url = "https://livingwage.mit.edu/metros/";
    $location = strtolower($_GET["location"]);
    $locations = array(
        "boston" => "14460",
        "new york" => "35620",
        "chicago" => "16980",
        "los angeles" => "31080",
        "houston" => "26420",
        "phoenix" => "38060",
        "philadelphia" => "37980",
        "san antonio" => "41700",
        "san diego" => "41740",
        "dallas" => "19100",
        "san jose" => "41940",
        "fort worth" => "19100",
        "jacksonville" => "27260",
        "san francisco" => "41860",
        "seattle" => "42660",
        "miami" => "33100",
        "nashville" => "34980",
        "el paso" => "21340",
        "cleveland" => "17460",
        "baltimore" => "12580"
    );

    if ($locations[$location]) {
        $user_url = $col_url . $locations[$location];
        $contents = file_get_contents($user_url);
        $entities = htmlentities($contents, ENT_QUOTES, "UTF-8");


        $adults = strtolower($_GET["adults"]);
        $adults_working = strtolower($_GET["adultswork"]);
        $kids = strtolower($_GET["kids"]);


        if ($adults == "1" and $kids == 0) {
            $column = 1;
        } elseif ($adults == "1" and $kids == 1) {
            $column = 2;
        } elseif ($adults == "1" and $kids == 2) {
            $column = 3;
        } elseif ($adults == "1" and $kids > 2) {
            $column = 4;
        } elseif ($adults == "2" and $kids == 0 and $adults_working == 1) {
            $column = 5;
        } elseif ($adults == "2" and $kids == 1 and $adults_working == 1) {
            $column = 6;
        } elseif ($adults == "2" and $kids == 2 and $adults_working == 1) {
            $column = 7;
        } elseif ($adults == "2" and $kids > 2 and $adults_working == 1) {
            $column = 8;
        } elseif ($adults == "2" and $kids == 0 and $adults_working == 2) {
            $column = 9;
        } elseif ($adults == "2" and $kids == 1 and $adults_working == 2) {
            $column = 10;
        } elseif ($adults == "2" and $kids == 2 and $adults_working == 2) {
            $column = 11;
        } elseif ($adults == "2" and $kids > 2 and $adults_working == 2) {
            $column = 12;
        }

        $column_info = array();
        $dom = new DOMDocument;
        $dom->loadHTML($contents);
        $rows = $dom->getElementsByTagName('td');
        $counter = 0;
        $search = false;
        foreach ($rows as $row) {
            if ($row->nodeValue == "Food" or $row->nodeValue == "Child Care" or $row->nodeValue == "Medical" or $row->nodeValue == "Housing" or $row->nodeValue == "Transportation" or $row->nodeValue == "Civic" or $row->nodeValue == "Other" or $row->nodeValue == "Required annual income after taxes" or $row->nodeValue == "Annual taxes" or $row->nodeValue == "Required annual income before taxes") {
                $key = $row->nodeValue;
                $search = true;
                $counter = 0;
            }

            if ($search) {
                if ($counter == $column) {
                    $column_info[$key] = $row->nodeValue;
                    $search = false;
                }
                $counter += 1;
            }
        }
    }

    $position = strtolower($_GET["position"]);
    $yoe = strtolower($_GET["yoe"]);

    echo "<p style='text-align: center'> Position: " . $position . ", " . "Location: " . $location . ", " . "Years of Experience: " . $yoe . ", " . "Adults: " . $adults . ", " . "Adults Working: " . $adults_working . ", " . "Kids: " . $kids . "</p> <br>";
    echo "<hr>";
    exec('python parser.py "' . $position . '" "' . $location . '" "' . $yoe . '" ', $python_output, $ret_code);

    //echo $python_output[0] . "HELLO";
    parse_str($python_output[0], $decoded_output);

    $before_taxes = str_replace("$", "", $column_info["Required annual income before taxes"]);
    $before_taxes = str_replace(",", "", $before_taxes);

    #echo $before_taxes;
    if ($before_taxes < intval($decoded_output["zeroth"])) {
        $message = "we have found 0 salaries of your position in the area lower than your cost of living";

    } else if ($before_taxes < intval($decoded_output["twenty_fifth"])) {
        $message = "you need to make above the 0th percentile: $" . intval($decoded_output["zeroth"]);

    } else if ($before_taxes < intval($decoded_output["fiftieth"])) {
        $message = "you need to make above the 25th percentile: $" . intval($decoded_output["twenty_fifth"]);

    } else if ($before_taxes < intval($decoded_output["seventy_fifth"])) {
        $message = "you need to make above the 50th percentile: $" . intval($decoded_output["fiftieth"]);

    } else if ($before_taxes < intval($decoded_output["one_hundreth"])) {
        $message = "you need to make above the 75th percentile: $" . intval($decoded_output["seventy_fifth"]);

    } else {
        $message = "we have found 0 salaries of your position in the area higher than your cost of living";

    }

    # Output all column data
    echo "<div style='display: flex'>";
    echo "<div style='flex: 50%; padding: 10px; text-align: center; background-color: lightgray'>";
    echo "<h2>Custom Salary Information:</h2>";
    array_to_table($decoded_output);
    echo "</div>";

    echo "<div style='flex: 50%; padding: 10px; text-align: center; background-color: lightgray'>";
    echo "<h2Custom >Cost of Living:</h2Custom>";
    array_to_table($column_info);
    echo "</div>";

    echo "</div>";

    echo "<hr>";


    echo "<h2 style='text-align: center'>Conclusion:</h2>";
    echo "<h4 style='text-align: center;'>Based on all of the given information $message </h4>";
    echo "<br>";

}
?>

</body>
<footer>
    <hr>
    This information was taken from <a href="https://www.levels.fyi/">levels.fyi</a> and <a href="https://livingwage.mit.edu">livingwage.mit.edu</a>
</footer>
</html>


