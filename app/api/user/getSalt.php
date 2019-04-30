<?php
ini_set("session.cookie_httponly",1);
session_start();
require(__dir__ .'/../../../vendor/autoload.php');
use app\middleware\Auth;
use ren1244\lib\Helper;
use ren1244\lib\Filter;

if(!isset($_GET['name']) || !Filter::string($_GET['name'],64)){
    Helper::exitError(400);
}
$data=env::$models->User->getByName($_GET['name']);
if($data===false){
    Helper::exitError(404);
}
Helper::returnJson(['salt'=>$data['salt']]);