<?php
include_once "../tools/config.php";

$GLOBALS["dbconnec"]=connectDB();
$sqlUpdate="";
$resultats=$GLOBALS["dbconnec"]->query("SELECT id,value,name,comment FROM config WHERE name = 'meteo'");
$resultats->setFetchMode(PDO::FETCH_OBJ);
while( $resultat = $resultats->fetch() )
{
    $BASE_URL = "http://query.yahooapis.com/v1/public/yql";

    $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="'.$resultat->value.'") AND u="c"';
    $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";
echo $yql_query_url;
    // Make call with cURL
    $session = curl_init($yql_query_url);
    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
    $json = curl_exec($session);
    // Convert JSON to PHP object
    $phpObj =  json_decode($json, TRUE);
    print_r($phpObj);
    //var_dump($phpObj);
    $result = array();
    if(isset($phpObj->query->results->channel->item->condition->temp)){
        $result["actual_temp"]=$phpObj->query->results->channel->item->condition->temp;
    }
    if(isset($phpObj->query->results->channel->item->condition->code)){
        $result["actual_code"]=$phpObj->query->results->channel->item->condition->code;
    }
    if(isset($phpObj->query->results->channel->item->condition->text)){
        $result["actual_text"]=$phpObj->query->results->channel->item->condition->text;
    }
    if(isset($phpObj->query->results->channel->item->forecast[0]->code)){
        $result["today_code"]=$phpObj->query->results->channel->item->forecast[0]->code;
    }
    if(isset($phpObj->query->results->channel->item->forecast[0]->high)){
        $result["today_high"]=$phpObj->query->results->channel->item->forecast[0]->high;
    }
    if(isset($phpObj->query->results->channel->item->forecast[0]->low)){
        $result["today_low"]=$phpObj->query->results->channel->item->forecast[0]->low;
    }
    if(isset($phpObj->query->results->channel->item->forecast[1]->code)){
        $result["day1_code"]=$phpObj->query->results->channel->item->forecast[1]->code;
    }
    if(isset($phpObj->query->results->channel->item->forecast[1]->high)){
        $result["day1_high"]=$phpObj->query->results->channel->item->forecast[1]->high;
    }
    if(isset($phpObj->query->results->channel->item->forecast[1]->low)){
        $result["day1_low"]=$phpObj->query->results->channel->item->forecast[1]->low;
    }
    /*if(isset($phpObj->query->results->channel->item->forecast[1]->text)){
        $result["day1_text"]=$phpObj->query->results->channel->item->forecast[1]->text;
    }*/
    if(isset($phpObj->query->results->channel->item->forecast[1]->day)){
        $result["day1_day"]=$phpObj->query->results->channel->item->forecast[1]->day;
    }
    if(isset($phpObj->query->results->channel->item->forecast[2]->code)){
        $result["day2_code"]=$phpObj->query->results->channel->item->forecast[2]->code;
    }
    if(isset($phpObj->query->results->channel->item->forecast[2]->high)){
        $result["day2_high"]=$phpObj->query->results->channel->item->forecast[2]->high;
    }
    if(isset($phpObj->query->results->channel->item->forecast[2]->low)){
        $result["day2_low"]=$phpObj->query->results->channel->item->forecast[2]->low;
    }
    /*if(isset($phpObj->query->results->channel->item->forecast[2]->text)){
        $result["day2_text"]=$phpObj->query->results->channel->item->forecast[2]->text;
    }*/
    if(isset($phpObj->query->results->channel->item->forecast[2]->day)){
        $result["day2_day"]=$phpObj->query->results->channel->item->forecast[2]->day;
    }
    if(isset($phpObj->query->results->channel->item->forecast[3]->code)){
        $result["day3_code"]=$phpObj->query->results->channel->item->forecast[3]->code;
    }
    if(isset($phpObj->query->results->channel->item->forecast[3]->high)){
        $result["day3_high"]=$phpObj->query->results->channel->item->forecast[3]->high;
    }
    if(isset($phpObj->query->results->channel->item->forecast[3]->low)){
        $result["day3_low"]=$phpObj->query->results->channel->item->forecast[3]->low;
    }
    if(isset($phpObj->query->results->channel->item->forecast[3]->text)){
        $result["day3_text"]=$phpObj->query->results->channel->item->forecast[3]->text;
    }
    if(isset($phpObj->query->results->channel->item->forecast[3]->day)){
        $result["day3_day"]=$phpObj->query->results->channel->item->forecast[3]->day;
    }

    $sqlUpdate .= "UPDATE config ";
    $sqlUpdate .= " SET comment='".json_encode($result)."' ";
    $sqlUpdate .= "WHERE id=".$resultat->id.";";
}

if($sqlUpdate != ""){
    $stmt = $GLOBALS["dbconnec"]->query($sqlUpdate);
}

?>