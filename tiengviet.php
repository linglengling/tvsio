<?php

function tiengvietIO($text) {
    $url = 'https://api.tiengviet.io/my/spin/';
    $data = array("token" => "56df1de9cd_dLYaNFAuROeDTENboFdIiiZ6967018","text" => $text);
    
    $postdata = json_encode($data);
    
    $ch = curl_init($url); 
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result = curl_exec($ch);
    curl_close($ch);
    print_r ($result);
    return $result;



}