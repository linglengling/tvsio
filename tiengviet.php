<?php

function tiengvietIO($text) {
    $url = 'https://api.tiengviet.io/my/spin/';
    $data = array("token" => "56df1de9cd_dLYaNFAuROeDTENboFdIiiZ6967018","text" => $text);
    
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
    print_r ($result);
    return $result;



}