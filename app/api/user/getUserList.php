<?php
/** 
 * [GET]
 * 
 * @return json 使用者列表[{id, name, email, admin, create, update}]
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
//取得資料
$r=env::$models->User->getUserList();
//回傳資料
Helper::returnJson($r);