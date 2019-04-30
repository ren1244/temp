<?php
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;
use ren1244\lib\Filter;

if(!Auth::isLogin()){
    Helper::exitError(403);
}

if(!isset($_GET['id']) || intval($_GET['id'])===0){
    Helper::exitError(400,'22');
}
$docId=intval($_GET['id']);
$uid=Auth::getData('id');
$data=env::$models->Doc->readDoc($uid, $docId);
if(is_null($data)){
    Helper::exitError(400);
}
Helper::returnJson([
    'title'=>Helper::base64url_encode($data['title']),
    'content'=>Helper::base64url_encode($data['content'])
]);
