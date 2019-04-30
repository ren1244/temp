<?php
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;

//google recaptcha 檢查
if(!isset($_POST['gToken'])){
    Helper::exitError(400,'403:gToken');
}
$ch=curl_init();
curl_setopt_array($ch,[
    CURLOPT_URL=>'https://www.google.com/recaptcha/api/siteverify',
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>[
        'secret'=>env::googleRecaptcha['private'],
        'response'=>$_POST['gToken']
    ],
    CURLOPT_RETURNTRANSFER=>true
]);
$gResponse=json_decode(curl_exec($ch),true);
if($gResponse['success']===false){
    Helper::exitError(403,'403:google');
}

//身分驗證
if(!Auth::isLogin()){
    if(!Auth::login()){
        Helper::exitError(403);
    }
}
