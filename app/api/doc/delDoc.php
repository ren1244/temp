<?php
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;

//驗證取得 uid
if(!Auth::isLogin()){
    Helper::exitError(403);
}
$uid=intval(Auth::getData('id'));
//驗證並取得 doc_Id
if(!isset($_POST['id'])){
    Helper::exitError(403);
}
$docId=intval($_POST['id']);
if(($docId).'' !== $_POST['id']){
    Helper::exitError(403);
}
//刪除文件
$rowCount=env::$models->Doc->deleteDoc($uid, $docId);
if($rowCount===0){
    Helper::exitError(404);
}
