<?php
/** 
 * [POST]
 * 
 * @param int uid 使用者id
 * @return status
 */
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;
//檢查登入且有權限
if(!Auth::isAdmin()){
    Helper::exitError(403);
}
//檢查並取得 uid
if(!isset($_POST['uid'])) {
    Helper::exitError(404);
}
$uid=intval($_POST['uid']);
if(strval($uid)!==$_POST['uid']) {
    var_dump($uid);
    Helper::exitError(404);
}
//刪除使用者
$r=env::$models->User->deleteUser($uid);
//回傳資料
if(!$r) {
    Helper::exitError(500);
}