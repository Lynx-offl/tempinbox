<?php
require_once './functions.php';
require_once './config.php';
require_once './options.php';
session_start();
error_reporting(E_ALL);
if(isset($_GET['user'])) {
    $address = $_GET['user'];
    $address = strtolower($address);
    $emailParts = explode("@",$address);
    if(isset($emailParts[1])) {
        $domain = $emailParts[1];
    } else {
        $domain = null;
    }
    $address = preg_replace('/@.*$/', "", $address);  
    $address = preg_replace('/[^A-Za-z0-9_.+-]/', "", $address); 
    $domain = preg_replace('/[^A-Za-z0-9_.+-]/', "", $domain);
    if(in_array(strtolower($address), $config['forbidemail'])) {
        $address = "";
    }
    if($address == null || $address == "") {   
        $address = generateRandomWord()."@".generateRandomDomain($config['domains']);
    } else {
        if($domain == null || $domain == "") {
            $address = $address."@".generateRandomDomain($config['domains']);
        } else {
            if(in_array($domain, $config['domains'])) {
                $address = $address."@".$domain;
            } else {
                $address = $address."@".generateRandomDomain($config['domains']);
            }
        }
    }
} else {
    $address = generateRandomWord()."@".generateRandomDomain($config['domains']);
}
$_SESSION['address'] = $address;
$allEmails = array();
if(isset($_SESSION["emails"])) {
    $allEmails = $_SESSION["emails"];
} 
if(!(in_array(strtolower($address), $allEmails))) {
    array_push($allEmails,$address);
}
$_SESSION["emails"] = $allEmails;
if($option['logs'] == "yes") {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    $finalData = "//EMail ID - ".$address.", Date - ".date('m/d/Y h:i:s a', time()).", IP - ".$ip;
    $myfile = file_put_contents('logs.php', $finalData.PHP_EOL , FILE_APPEND | LOCK_EX);
}
echo $address; 
?>