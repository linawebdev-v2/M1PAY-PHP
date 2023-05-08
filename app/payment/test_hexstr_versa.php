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
function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}


// Tests
header('Content-Type: text/plain');
function test($expected, $actual, $success) {
    if($expected !== $actual) {
        echo "Expected: '$expected'\n";
        echo "Actual:   '$actual'\n";
        echo "\n";
        $success = false;
    }
    return $success;
}

$success = true;
$success = test('00', strToHex(hexToStr('00')), $success);
$success = test('FF', strToHex(hexToStr('FF')), $success);
$success = test('000102FF', strToHex(hexToStr('000102FF')), $success);
$success = test('↕↑↔§P↔§P ♫§T↕§↕', hexToStr(strToHex('↕↑↔§P↔§P ♫§T↕§↕')), $success);

echo $success ? "Success" : "\nFailed";