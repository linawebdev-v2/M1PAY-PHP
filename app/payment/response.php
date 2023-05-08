<?php
function strToHex($string){ 
    $hex = ''; 
    for ($i=0; $i<strlen($string); $i++){ 
        $ord = ord($string[$i]); 
        $hexCode = dechex($ord); 
        $hex .= substr('0'.$hexCode, -2); 
    } 
    return strToUpper($hex);
}


//M1Pay Configuration before Payment Request

$env = "UAT";

//OAuth2 API
switch($env){
    case "UAT":
        $keycloak_url = "https://keycloak.m1pay.com.my/auth/realms/master/protocol/openid-connect/token";
        break;
    default:
        $keycloak_url = "https://keycloak.m1pay.com.my/auth/realms/m1pay-users/protocol/openid-connect/token";
        break;
}

//M1Pay Payment Request
switch($env){
    case "UAT":
        $monepay_url = "https://gateway.m1payall.com/m1paywall/api/m-1-pay-transactions"; //"https://gateway-uat.m1pay.com.my/m1paywall/api/transaction";
        break;
    default:
        $monepay_url = "https://gateway.m1pay.com.my/wall/api/api/m-1-pay-transactions";
        break;
}

//CURL - to obtain the access_token
//Parameter Value
$fields = array();
$fields['grant_type'] = 'client_credentials';
$fields['client_id'] = '92056548';
$fields['client_secret'] = '2b7345eb-8b36-4bf7-a15f-aab6e6c1fe31';

$fields_string = http_build_query($fields);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $keycloak_url);
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($curl);

//echo $data."<br><br>";

curl_close($curl);

$dataArray = json_decode($data,1);

//Get the access_token only
foreach($dataArray as $k => $v){
    if($k=='access_token'){
        $access_token = $v;
    }
}


//CURL
//add authorization in header later
$authorization = 'authorization: Bearer'.trim($access_token);
//opt in headers
$headers = array( 
    $authorization,
    "X-Content-Type-Options:nosniff",
    "Cache-Control:no-cache"
);

$txn_info_url = $monepay_url."/".$_GET['transactionId'];
//echo $txn_info_url; echo "<br>";
$curl = curl_init();
curl_setopt($curl,CURLOPT_URL, $txn_info_url);
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_ENCODING, "");
curl_setopt($curl,CURLOPT_MAXREDIRS, 10);
curl_setopt($curl,CURLOPT_TIMEOUT, 0);
curl_setopt($curl,CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);

$data = curl_exec($curl);

curl_close($curl);


echo "<pre>". print_r(json_decode($data),1)."</pre>";
