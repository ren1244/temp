<?php
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;
use ren1244\lib\Filter;

//未登入拒絕
if(!Auth::isLogin()){
    Helper::exitError(403);
}
//管理員重設密碼
if(Auth::isAdmin() && isset($_POST['name'])){
    resetUserPwd();
} else {
    changeSelfPwd();
}

function resetUserPwd()
{
    if(!Filter::string($_POST['name'],64)){
        Helper::exitError(400);
    }
    $data=env::$models->User->getByName($_POST['name']);
    if($data===false){
        Helper::exitError(404);
    }
    $id=$data['id'];
    $pwd=strtr(base64_encode(random_bytes(3)),'+/','-_');
    $hash=hash_hmac('sha256',$pwd,env::$models->Sys->get('salt'));
    $r=env::$models->User->changePwd($id, $hash);
    if($r===false){
        Helper::exitError(500);
    } elseif($r===0) {
        Helper::exitError(202);
    }
    Helper::returnJson(['pwd'=>$pwd]);
}

function changeSelfPwd()
{
    if(
        !isset($_POST['hash']) ||
        !Filter::hex($_POST['hash'],64)
    ){
        Helper::exitError(400);
    }
    $pwd=$_POST['hash'];
    $id=Auth::getData('id');
    if(is_null($id)){
        Helper::exitError(404);
    }
    $r=env::$models->User->changePwd($id, $pwd);
    if($r===false){
        Helper::exitError(500);
    } elseif($r===0) {
        Helper::exitError(202);
    }
}
