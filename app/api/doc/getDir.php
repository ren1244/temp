<?php
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;

if(!Auth::isLogin()){
    Helper::exitError(403);
}
$id=Auth::getData('id');
$data=env::$models->Doc->getDocList($id);
$n=count($data);
for($i=0;$i<$n;++$i){
    $data[$i]['title']=Helper::base64url_encode($data[$i]['title']);
}
Helper::returnJson($data);