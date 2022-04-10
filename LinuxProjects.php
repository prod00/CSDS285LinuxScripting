<html>
<body>
<style>
    .link {
        background-color: red;
        box-shadow: 0 5px 0 darkred;
        color: white;
        padding: 1em 1.5em;
        position: relative;
        text-decoration: none;
        text-transform: uppercase;
    }

    .link:hover {
        background-color: #ce0606;
    }

    .link:active {
        box-shadow: none;
        top: 5px;
    }
    .submit-link{
        border: 2px solid black;
    }

    /* Non-Demo Styles */
    body {
        background-color: #a6feff;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
</style>

<?php

if (empty($_GET["location"]) and empty($_GET["people"]) and empty($_GET["checkin"]) and empty($_GET["location"])){

    echo "<h2>Enter a Loction: </h2>";
    echo "<form action='project2.php' method='GET'>";
    echo "<textarea cols=25 rows = 1 name='location'>Tampa</textarea>";
    echo "<textarea cols=25 rows = 1 name='people'>3</textarea>";
    echo "<textarea cols=25 rows = 1 name='checkin'>2022-03-23</textarea>";
    echo "<textarea cols=25 rows = 1 name='checkout'>2022-03-24</textarea>";
    echo "<input type=submit>";
    echo "</form>";
}
else{

    $airbnb_url = "https://www.airbnb.com/s/LOCATION/homes?tab_id=home_tab&refinement_paths%5B%5D=%2Fhomes&flexible_trip_dates%5B%5D=april&flexible_trip_dates%5B%5D=march&flexible_trip_lengths%5B%5D=weekend_trip&date_picker_type=calendar&checkin=CHECKIN&checkout=CHECKOUT&adults=PEOPLE&source=structured_search_input_header&search_type=filter_change";
    $vrbo_url = "https://www.vrbo.com/search/keywords:LOCATION/arrival:CHECKIN/departure:CHECKOUT/minNightlyPrice/0?filterByTotalPrice=true&petIncluded=false&ssr=true&adultsCount=PEOPLE&childrenCount=0";
    $expedia_url = "https://www.expedia.com/Hotel-Search?adults=2&d1=CHECKIN&d2=CHECKOUT&destination=LOCATION&endDate=CHECKOUT&rooms=1&semdtl=&sort=RECOMMENDED&startDate=CHECKOUT&theme=&useRewards=true&userIntent=";

    $location = $_GET["location"];
    $people = $_GET["people"];
    $checkin = $_GET["checkin"];
    $checkout = $_GET["checkout"];

    $airbnb_url = str_replace("LOCATION", $location, $airbnb_url);
    $vrbo_url = str_replace("LOCATION", $location, $vrbo_url);
    $expedia_url = str_replace("LOCATION", $location, $expedia_url);

    $airbnb_url = str_replace("CHECKIN", $checkin, $airbnb_url);
    $vrbo_url = str_replace("CHECKIN", $checkin, $vrbo_url);
    $expedia_url = str_replace("CHECKIN", $checkin, $expedia_url);

    $airbnb_url = str_replace("CHECKOUT", $checkout, $airbnb_url);
    $vrbo_url = str_replace("CHECKOUT", $checkout, $vrbo_url);
    $expedia_url = str_replace("CHECKOUT", $checkout, $expedia_url);

    $airbnb_url = str_replace("PEOPLE", $people, $airbnb_url);
    $vrbo_url = str_replace("PEOPLE", $people, $vrbo_url);
    $expedia_url = str_replace("PEOPLE", $people, $expedia_url);

    echo $airbnb_url;
    echo "<br>";
    echo $vrbo_url;
    echo "<br>";
    echo $expedia_url;

    function getBetween($string, $start = "", $end = ""){
        if (strpos($string, $start)) { // required if $start not exist in $string
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return substr($firstSubStr, 0, $endCharCount);
        } else {
            return '';
        }
    }
    echo "<h2>Enter You Top Choice URL: </h2>";
    $message = "";
    if(isset($_POST['SubmitButton'])){ //check if form was submitted
        $input = $_POST['inputText']; //get input text
        $message = "Success! You have your top choice! <br>";
        if(strpos($input,"airbnb") != false){
            $id = getBetween($input, "rooms/","?adults");
            echo "<div class='airbnb-embed-frame' data-id='{$id}' data-view='home' style='width:450px;height:300px;margin:auto'><a href='https://www.airbnb.com/rooms/{$id}?check_in=2022-03-23&amp;check_out=202><p;adults=3&amp;s=66&amp;source=embed_widget' rel='nofollow'>paul<a><script async='' src='https://www.airbnb.com/embeddable/airbnb_jssdk'></script></div>";
    }
    else if(strpos($input, "vrbo")){
      echo "<a href='{$input}' target='_blank'>Top Choice VRBO Link</a>";
      echo "<br>";
      //echo "<iframe src='{$input}'></iframe>"; 
//      echo file_get_contents($input);
    }
    else if(strpos($input, "expedia")){
      echo "Expedia Link: <br>";
      echo "<a href='{$input}' target='_blank'>Top Choice Expedia Link</a";
    }
    else{
      echo "Couldnt Process The URL!";
      echo $input;
    }
  }
  echo "<form action='' method='post'>";
  echo "<input type='text' name='inputText'/>";
  echo "<input type='submit' name='SubmitButton'/>";
  echo "</form>";
  echo htmlentities(file_get_contents($expedia_url));
}
?>