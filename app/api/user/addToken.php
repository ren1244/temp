<?php
/** 
 * [POST]產生一個token
 *
 * @return jsonArray 例如：{"token":"_TK6RCoj","status":"1","create_time":"1555046623"}
 */
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;

if(!Auth::isLogin() || !Auth::isAdmin()){
    Helper::exitError(403);
}
$arr=[];
if(env::$models->User->createTokens(1,$arr)!==1){
    Helper::exitError(500);
}
Helper::returnJson($arr);
