<?php
function array_to_table($array) {
    $keys = array_keys($array);
    echo "<table> <tbody>";
    for($i=0; $i < count($keys); $i++) {
        echo "<tr> <td> " . $keys[$i] . "</td> <td>" . $array[$keys[$i]] . "</td> </tr>";
    }
    echo "</tbody> </table>";
}

if (empty($_GET["location"]) and empty($_GET["people"]) and empty($_GET["checkin"]) and empty($_GET["location"])){
    echo "<h2>Enter a Loction: </h2>";
    echo "<form action='tests.php' method='GET'>";
    echo "<textarea cols=25 rows = 1 name='location' placeholder='Location'></textarea>";
    echo "<textarea cols=25 rows = 1 name='adults' placeholder='Adults'></textarea>";
    echo "<textarea cols=25 rows = 1 name='adultswork' placeholder='Adults Working'></textarea>";
    echo "<textarea cols=25 rows = 1 name='kids' placeholder='Kids'></textarea>";
    echo "<textarea cols=25 rows = 1 name='position' placeholder='Position'></textarea>";
    echo "<textarea cols=25 rows = 1 name='yoe' placeholder='Years Of Experience'></textarea>";
    echo "<input type=submit>";
    echo "</form>";

}
else {
    $col_url = "https://livingwage.mit.edu/metros/";
    $location = strtolower($_GET["location"]);
    $locations = array(
        "boston" => "14460",
        "new york city" => "35620",
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
        "san diego" => "41740",
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

    exec('python parser.py "' . $position . '" "' . $location . '" "' . $yoe . '" ', $python_output, $ret_code);

    //echo $python_output[0] . "HELLO";
    parse_str($python_output[0], $decoded_output);

    $before_taxes = str_replace("$", "", $column_info["Required annual income before taxes"]);
    $before_taxes = str_replace(",", "", $before_taxes);

    #echo $before_taxes;
    if ($before_taxes < intval($decoded_output["zeroth"])) {
        echo "You need to make below the 0th percentile reported on levels.fyi for your information";

    } else if ($before_taxes < intval($decoded_output["twentieth"])) {
        echo "You need to make above the 0th percentile and less than the 25th percentile reported on levels.fyi for your information";

    } else if ($before_taxes < intval($decoded_output["fiftieth"])) {
        echo "You need to make above the 25th percentile and less than 50th percentile reported on levels.fyi for your information";

    } else if ($before_taxes < intval($decoded_output["seventy_fifth"])) {
        echo "You need to make above the 50th percentile and less than 75th percentile reported on levels.fyi for your information";

    } else if ($before_taxes < intval($decoded_output["one_hundreth"])) {
        echo "You need to make above the 75th percentile and less than the 100th percentile reported on levels.fyi for your information";

    } else {
        echo "You need to make above the 100th percentile reported on levels.fyi for your information";
        echo nl2br("\n");
        echo "This may not be liveable without considerable savings.";
    }
    echo nl2br("\n");
    echo "As a reminder, your info was: " . "<br>" . "Position: " . $position . "<br>" . "Location: " . $location . "<br>" . "Years of Experience: " . $yoe . "<br>" . "Adults: " . $adults . "<br>" . "Adults Working: " . $adults_working . "<br>" . "Kids: " . $kids . "<br>";
    # Output all column data
    array_to_table($decoded_output);
    array_to_table($column_info);
}
?>

