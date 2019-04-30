<?php
/** 
 * [POST]儲存檔案
 *
 * @param
 * [title]標題，base64url
 * [content]內容，base64url
 * [id]文章id，int，可選，有這個參數會更新文章，否則為新增文章
 * [chpwd]設定 chpwd flag，可選，int=0or1
 * 
 * @return
 * [200]成功
 * [400]參數錯誤
 * [403]未登入
 */
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');

use app\middleware\Auth;
use ren1244\lib\Helper;
//檢查登入
if(!Auth::isLogin()){
    Helper::exitError(403);
}
//檢查參數並取得 $title $content
if(
    !isset($_POST['title']) ||
    strlen($_POST['title'])>320 ||
    preg_match('/^[0-9a-zA-Z\-_]*$/',$_POST['title'])!==1 ||
    !isset($_POST['content']) ||
    strlen($_POST['content'])>2800000 || //還原後最多是2.1M
    preg_match('/^[0-9a-zA-Z\-_]*$/',$_POST['content'])!==1 
    
){
    Helper::exitError(400);
}
$title=Helper::base64url_decode($_POST['title']);
$content=Helper::base64url_decode($_POST['content']);
//
if(isset($_POST['id'])){
    $uid=Auth::getData('id');
    $docId=(int)$_POST['id'];
    //更新文章
    $rowCount=env::$models->Doc->updateDoc($uid, $docId, $title, $content);
    if($rowCount===0){
        Helper::exitError(404);
    }
} else {
    //新增文章
    $uid=Auth::getData('id');
    if(!isset($_POST['chpwd'])) {
        $docId=env::$models->Doc->createDoc($uid, 0, $title, $content);
        Helper::returnJson(['docId'=>$docId]);
    } else {
        $docId=env::$models->Doc->createDoc($uid, 0, $title, $content, true);
        Helper::returnJson(['docId'=>$docId]);
    }
}

