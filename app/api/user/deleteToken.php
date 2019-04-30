<?php
/** 
 * [POST]刪除一個token
 *
 * @return jsonArray 刪除的個數，應該會是：{"rowCount":1}
 */
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;

if(!Auth::isLogin() || !Auth::isAdmin()){
    Helper::exitError(403);
}
if(!isset($_POST['token']) || preg_match('/^[0-9a-zA-Z\-_]{8}$/',$_POST['token'])!==1){
    Helper::exitError(400);
}
$r=env::$models->User->deleteToken($_POST['token']);
if($r===false){
    Helper::exitError(500);
}
Helper::returnJson(['rowCount'=>$r]);