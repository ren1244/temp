<?php
namespace ren1244\lib;

class Filter
{
    public static function string($str, $len)
    {
        return strlen($str)>$len?false:true;
    }
    
    public static function timestamp($str)
    {
        return $str===(string)(int)$str?true:false;
    }
    
    public static function hex($str, $len){
        if(strlen($str)!==$len){
            return false;
        }
        return preg_match('/^[0-9a-fA-F]*$/',$str)===1?true:false;
    }
    
    public static function base64($str, $len){
        if(strlen($str)!==$len){
            return false;
        }
        return preg_match('/^[0-9a-zA-Z\+\/]*$/',$str)===1?true:false;
    }
    
    public static function base64_url($str, $len){
        if(strlen($str)!==$len){
            return false;
        }
        return preg_match('/^[0-9a-zA-Z\-_]*$/',$str)===1?true:false;
    }
}