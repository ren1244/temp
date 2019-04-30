<?php
namespace ren1244\lib;
class Helper
{
    const codeStr=[
        '500'=>'Internal Server Error',
        '400'=>'Bad Request',
        '403'=>'Forbidden',
        '404'=>'Not Found',
        '202'=>'Accepted'
    ];
    
    /** 
     * 回傳錯誤碼並結束，如果錯誤碼在 codeStr 存在，也會印出預設字串
     *
     * @param int $code 錯誤碼
     * @return void
     */
    public static function exitError($code, $info=false)
    {
        http_response_code($code);
        if($info) {
            exit($info);
        } elseif(isset(Helper::codeStr[$code])){
            exit(Helper::codeStr[$code]);
        }
        exit();
    }
    
    /** 
     * 讀取視圖
     *
     * @param string target 
     * @param array paramAssocArr 參數的關聯陣列
     * @return void
     */
    public static function view($target_loadView, $param_loadView=[])
    {
        foreach($param_loadView as $key_loadView=>$val_loadView){
            $$key_loadView=$val_loadView;
        }
        include($target_loadView.'.php');
    }
    
    /** 
     * 回傳 json
     *
     * @param array obj 能轉換為json的物件
     * @return void
     */
    public static function returnJson($obj)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo (json_encode($obj,JSON_UNESCAPED_SLASHES));
    }
    
    /** 
     * 敘述
     *
     * @param 型別 變數 敘述
     * @return 型別 敘述
     */
    public static function base64url_encode($str)
    {
        return str_replace('=','',strtr(base64_encode($str),'+/','-_'));
    }
    
    public static function base64url_decode($str)
    {
        return base64_decode(strtr($str,'-_','+/').str_repeat('=',(4-strlen($str)%4)%4));
    }
}