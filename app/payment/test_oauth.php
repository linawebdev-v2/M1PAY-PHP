<?php 

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

//CURL
//Parameter Value
$fields = array();
$fields['grant_type'] = 'client_credentials';
$fields['client_id'] = '68682293';
$fields['client_secret'] = '04f3cf6e-91f4-45ff-b703-e4c8666a083e';

$fields_string = http_build_query($fields);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $keycloak_url);
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($curl);

curl_close($curl);

echo "URL: $keycloak_url<br><br>";
echo "REQUEST<br>";
echo print_r($fields,1)."<br><br>";
echo "RESPONSE<br>";
echo $data."<br><br>"; exit;