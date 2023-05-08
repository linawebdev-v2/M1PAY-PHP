<?php
include "inc.php";

if($_POST['proceedpay'] && $_POST['proceedpay'] == 'submit'){
    foreach($_POST as $k => $v){
        echo "$k = $v <br/>";
    }
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
        $monepay_url = "https://gateway.m1payall.com/m1paywall/api/transaction"; //"https://gateway-uat.m1pay.com.my/m1paywall/api/transaction";
        break;
    default:
        $monepay_url = "https://gateway.m1pay.com.my/wall/api/transaction";
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

//echo $data."<br><br>"; exit;

curl_close($curl);

$dataArray = json_decode($data,1);

//Get the access_token only
foreach($dataArray as $k => $v){
    if($k=='access_token'){
        $access_token = $v;
    }
}


//Payment Request
$params = array(
    'merchantId' => $fields['client_id'],
    'transactionAmount' => number_format($_POST['amount'], 2,'.',''),
    'transactionCurrency' => $_POST['currency'],
    'merchantOrderNo' => $_POST['orderid'],
    'emailAddress' => $_POST['cust_email'],
    'phoneNumber' => $_POST['cust_mobile'],
    'productDescription' => $_POST['description'],
    'fpxBank' => '', //TEST0021
    'exchangeOrderNo' => '',
    'skipConfirmation' => "false"
);

if(!empty($_POST['bankid'])){
    $params['fpxBank'] =  $_POST['bankid'];
}

if(!empty($_POST['channel'])){
    $params['channel'] =  $_POST['channel'];
}

$raw_data = $params['productDescription'].'|'
    .$params['transactionAmount'].'|'
    .$params['exchangeOrderNo'].'|'
    .$params['merchantOrderNo'].'|'
    .$params['transactionCurrency'].'|'
    .$params['emailAddress'].'|'
    .$params['merchantId'];

echo "<br><br>".$raw_data."<br><br>"; 

$data = $raw_data; 
$file_name = '/etc/pki/tls/certs/92056548.key'; //Path of private key 
$signature = ''; //Signed data will be store in this parameter 
try { 
    $myfile = fopen($file_name, "r") or die("Unable to open file!"); 
    $priv_key = fread($myfile,filesize($file_name)); 
    fclose($myfile); 
    $pkeyid = openssl_get_privatekey($priv_key); 
    openssl_sign($data, $signature, $pkeyid, "sha1WithRSAEncryption"); 
    $signature = strToHex($signature); 
} catch (Exception $e) { 
    echo 'Caught exception: ', $e->getMessage(), "\n"; 
} 

$params['signedData'] = $signature;

//CURL - to get the payment link and redirect 
//the parameter-values that need to send
$params_string = json_encode($params);

$myfile = fopen("payment_request_m1pay.txt", "a") or die("Unable to open file!");
$txt = date('Ymd H:i:s')."\n\n".print_r($_POST,1)."\n\n\n";
fwrite($myfile, $txt);
fclose($myfile);

//add authorization in header later
$authorization = 'authorization: Bearer'.trim($access_token);
//opt in headers
$headers = array( 
    $authorization,
    "Content-Type: application/json",
    "X-Content-Type-Options:nosniff",
    "Accept:application/json",
    "Cache-Control:no-cache"
);

$curl = curl_init();
curl_setopt($curl,CURLOPT_URL, $monepay_url);
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_ENCODING, "");
curl_setopt($curl,CURLOPT_MAXREDIRS, 10);
curl_setopt($curl,CURLOPT_TIMEOUT, 0);
curl_setopt($curl,CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl,CURLOPT_POSTFIELDS, $params_string); 
curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);

$data = curl_exec($curl);
var_dump($data);

curl_close($curl);

echo "<script type='text/javascript'>window.location.replace('".$data."');</script>";
