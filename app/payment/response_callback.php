<?php
include "inc.php";

if(isset($_POST) && !empty($_POST)){
    //Open this comment to log into your server
    /*$myfile = fopen("post_callback_data.txt", "a") or die("Unable to open file!");
    $txt = date('Ymd H:i:s')."\nM1PAY RESPONSE:\n".print_r($_POST,1)."\n";*/
    
    //Verify the data from M1Pay
    $raw_data = $_POST['transactionAmount']."|".$_POST['fpxTxnId']."|".$_POST['sellerOrderNo']."|".$_POST['status']."|".$_POST['merchantOrderNo'];

    $file_name = '/etc/pki/tls/certs/public.crt'; //Path of public key
    $response_signature = $_POST['signedData'];
    $signature = hexToStr($response_signature);
    
    try { 
        $myfile_pub = fopen($file_name, "r") or die("Unable to open file!"); 
        $pub_key = fread($myfile_pub,filesize($file_name));
        fclose($myfile_pub);

        $pubkeyid = openssl_pkey_get_public($pub_key);
        
        $r = openssl_verify($raw_data, $signature, $pubkeyid, "sha1WithRSAEncryption");
    } catch (Exception $e) { 
        echo 'Caught exception: ', $e->getMessage(), "\n"; 
    } 

    //Open this comment to log into your server
    /*$txt .= "Verified Data: ".( $r ? "Success" : "Failed" )."\n\n\n";
    fwrite($myfile, $txt);
    fclose($myfile);*/

    //If use Postman to see the result
    echo "M1Pay Reponse Data Logged and Verified as ". ( $r ? "Success" : "Failed" ); 
}