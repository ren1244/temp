<?php
/** 
 * [POST]用新資料取代舊資料
 * 
 * @return json 需要被更新的清單
 */
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Filter;
//檢查登入
if(!Auth::isLogin()){
    Helper::exitError(403);
}
$uid=Auth::getData('id');
//參數
if(!isset($_POST['hash']) ||
    !Filter::hex($_POST['hash'],64)){
    Helper::exitError(400);
}
//用新資料取代舊資料
if(env::$models->User->changePwd($uid,$_POST['hash'],$_SESSION['newSalt'])!==false){
    env::$models->Doc->swapChpwd($uid);
} else {
    Helper::exitError(500);
}

