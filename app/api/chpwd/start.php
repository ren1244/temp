<?php
/** 
 * [POST]做更新密碼的準備
 * 
 * @return json 需要被更新的清單
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
$uid=Auth::getData('id');
//刪除所有doc資料表中 chpwd 被設為1的資料
env::$models->Doc->deleteChpwd($uid);
//取得新的 salt
$salt=env::$models->User->getNewSalt();
$_SESSION['newSalt']=$salt;
//回傳需要被更新的清單
$data=env::$models->Doc->getDocList($uid);
$n=count($data);
for($i=0;$i<$n;++$i){
    $data[$i]['title']=Helper::base64url_encode($data[$i]['title']);
}
Helper::returnJson(['newSalt'=>$salt,'data'=>$data]);
