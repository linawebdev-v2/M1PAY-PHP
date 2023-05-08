<?php 
    $gen_orderid = date("YmdHis").rand(1,9999);
    $def_currency = "MYR";

    $headers = array( 
        "Content-Type: application/json",
        "X-Content-Type-Options:nosniff",
        "Accept:application/json",
        "Cache-Control:no-cache"
    );

    $env = "UAT";
    switch($env){
        case "UAT":
            $banklist_url = "https://gateway.m1payall.com/m1payfpx/api/bank-list/B2C";
            break;
        default:
            $banklist_url = "https://gateway.m1pay.com.my/fpx/api/bank-list/B2C";
            break;
    }

    $curl = curl_init();
    curl_setopt($curl,CURLOPT_URL, $banklist_url);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_ENCODING, "");
    curl_setopt($curl,CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl,CURLOPT_TIMEOUT, 0);
    curl_setopt($curl,CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);

    $bank_list = curl_exec($curl);
    

    $banks_arr = json_decode($bank_list);


    curl_close($curl);
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>M1Pay Demo - Payment Form</title>
    </head>
    <body>
        <h1>DEMO PAYMENT M1PAY</h1>
        <h3>Payment Info</h3>
        <form action="request.php" method="POST">
            <label>Order ID:</label><input name="orderid" id="orderid" value="<?php echo $gen_orderid?>" /><br/><br/>
            <label>Amount (MYR):</label><input name="amount" id="amount" value="" /><br/><br/>
            <label>Name:</label><input name="cust_name" id="cust_name" value="" /><br/><br/>
            <label>Email:</label><input name="cust_email" id="cust_email" value="" /><br/><br/>
            <label>Contact Number:</label><input name="cust_mobile" id="cust_mobile" value="" /><br/><br/>
            <label>Payment Description:</label><input name="description" id="description" value="" /><br/><br/>
            <label>Payment Channel:</label>
            <select name="channel" id="channel">
                <option value="">Select One</option>
                <option value="ONLINE_BANKING">Online Banking</option>
                <option value="CARD_PAYMENT">Debit/Credit Card</option>
                <option value="UMOBILE">Umobile</option>
                <option value="EMONEI">E-Monei</option>
                <option value="ALIPAY">AliPay</option>
                <option value=""></option>
            </select><br/><br/>
            <label>Payment Channel:</label>
            <select name="bankid" id="bankid">
            <?php 
                foreach ($banks_arr as $k => $kv) { 
                    echo '<option value="'.$kv->bankId.'">'.$kv->title.' ('.($kv->fpxOnline == 1 ? "On" : "Offline").')</option>';
                } 
            ?>
            </select><br/><br/>
            <input type="hidden" name="currency" id="currency" value="<?php echo $def_currency ?>" />
            <button type="submit" name="proceedpay" value="submit">Proceed to Pay</button>
        </form>
    </body>
</html>