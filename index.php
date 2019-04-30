<?php
ini_set("session.cookie_httponly",1);
session_start();
require('vendor/autoload.php');

use app\middleware\Auth;
use ren1244\lib\Helper;

if(preg_match('/^'.addcslashes(env::baseUrl,'/').'(.*)\/?$/U', $_SERVER['REQUEST_URI'], $mch)!==1){
    Helper::returnError(403);
}
$path=$mch[1];

if(Auth::isLogin()){
    switch($path){
    case '':
        Helper::view(__dir__ .'/app/view/home',[
            'salt'=>env::$models->Sys->get('salt'),
            'encSalt'=>env::$models->Sys->get('encSalt'),
            'admin'=>Auth::isAdmin()
        ]);
        break;
    case 'setting':
        Helper::view(__dir__ .'/app/view/setting',[
            'salt'=>env::$models->Sys->get('salt'),
            'encSalt'=>env::$models->Sys->get('encSalt'),
            'admin'=>Auth::isAdmin(),
            'resourceDir'=>env::baseUrl.'public'
        ]);
        break;
    case 'admin':
        Helper::view(__dir__ .'/app/view/admin',[
            'admin'=>Auth::isAdmin(),
            'resourceDir'=>env::baseUrl.'public'
        ]);
        break;
    default:
        header('Location: '.env::baseUrl);
    }
} else {
    if($path==='') {
        Helper::view(__dir__ .'/app/view/login',[
            'salt'=>env::$models->Sys->get('salt')
        ]);
    } else {
        header('Location: '.env::baseUrl);
    }
}
