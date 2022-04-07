<?php

function postAPISEO($data, $APIKey) {
    $url = "http://internationalsamuel.com/APISEOTOOL/public/index.php/api/".$APIKey;
    // $url = "http://demo.test/public/index.php/api/".$APIKey;

    // $data = array("text" => $text);
    
    $postdata = json_encode($data);
    
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result = curl_exec($ch);
    
    // Check the return value of curl_exec(), too
    if ($result === false) {
        throw new Exception(curl_error($ch), curl_errno($ch));
    }
    curl_close($ch);
    // print_r ($result);
    $result = json_decode($result, true);
    return $result;



}