<?php
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;

if(!Auth::registry()){
    Helper::exitError(403);
}