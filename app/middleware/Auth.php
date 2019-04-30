<?php
namespace app\middleware;

use ren1244\lib\Helper;
use ren1244\lib\Filter;

class Auth
{
    public static function isLogin()
    {
        if(isset($_SESSION['auth'])){
            return true;
        }
        return false;
    }
    
    public static function isAdmin()
    {
        if(isset($_SESSION['auth']) && $_SESSION['auth']['admin']===true){
            return true;
        }
        return false;
    }
    
    public static function login()
    {
        if(
            !isset($_POST['name']) ||
            !Filter::string($_POST['name'],64) ||
            !isset($_POST['time']) ||
            !Filter::timestamp($_POST['time']) ||
            !isset($_POST['hash']) ||
            !Filter::hex($_POST['hash'],64)
        ) {
            Helper::exitError(400);
        }
        $user=$_POST['name'];
        $time=(int)$_POST['time'];
        $hash=$_POST['hash'];
        $data=\env::$models->User->getByName($user);
        $hash2=hash_hmac('sha256',hex2bin($data['hash']),\env::$models->Sys->get('salt').$time);
        if($hash2!==$hash){
            return false;
        }
        $_SESSION['auth']=[
            'id'=>(int)$data['id'],
            'admin'=>(bool)$data['admin']
        ];
        return true;
    }
    
    public static function registry()
    {// token name hash [email]
        if(
            !isset($_POST['token']) ||
            !Filter::base64_url($_POST['token'],8) ||
            !isset($_POST['name']) ||
            !Filter::string($_POST['name'],64) ||
            !isset($_POST['hash']) ||
            !Filter::hex($_POST['hash'],64) ||
            (isset($_POST['email']) && !Filter::string($_POST['email'],256))
        ) {
            Helper::exitError(400);
        }
        //token 存在 且 name 不重複 則寫入
        $email=isset($_POST['email'])?$_POST['email']:null;
        return \env::$models->User->create($_POST['token'],$_POST['name'],$_POST['hash'],$email);
    }
    
    public static function logout()
    {
        if(isset($_SESSION['auth'])){
            unset($_SESSION['auth']);
        }
    }
    
    public static function getData($key)
    {
        if(!isset($_SESSION['auth']) || !isset($_SESSION['auth'][$key])){
            return null;
        }
        return $_SESSION['auth'][$key];
    }
}
