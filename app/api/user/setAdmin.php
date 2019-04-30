<?php
/** 
 * [POST]
 * 
 * @param int uid 使用者id
 * @param int val 開啟關閉權限(1為開啟，其他都是關閉)
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
if(!isset($_POST['uid']) || !isset($_POST['val'])) {
    Helper::exitError(404);
}
$uid=intval($_POST['uid']);
$val=$_POST['val']==='1'?true:false;
if(strval($uid)!==$_POST['uid']) {
    var_dump($uid);
    Helper::exitError(404);
}
//刪除使用者
$r=env::$models->User->setAdmin($uid, $val);
//回傳資料
if(!$r) {
    Helper::exitError(500);
}