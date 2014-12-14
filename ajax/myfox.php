<?php
function getToken(){
    $response=exec("curl -u e64fd7351eccb2dcceb5c34eafd09be0:grr6pmctbOxI0Z16SXXCEr0iT6chgF8T https://api.myfox.me/oauth2/token -d 'grant_type=password&username=nico.gyss@gmail.com&password=triplm4949'");
    //print_r($response);
    $json=json_decode($response);
    if(!isset($json->access_token)){
        return false;
    }
    $token = $json->access_token;
    return $token;
}

//echo "<br/>".$token."<br/>";

//98033
//GET /site/{siteId}/device/{deviceId}/data/temperature/get
//GET /site/{siteId}/device/gate/list
//print_r(file_get_contents("https://api.myfox.me:443/v2/site/10562/device/socket/list?access_token=".$token));
//print_r(file_get_contents("https://api.myfox.me:443/v2/site/10562/device/98033/data/temperature/get?dateFrom=2014-05-01T00:00:00002:00&dateTo=2014-05-02T00:00:00002:00&access_token=".$token));
//print_r(file_get_contents("https://api.myfox.me:443/v2/client/site/list?access_token=".$token));

?>
