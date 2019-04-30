<?php

class env{
    const baseUrl='/';
    const db=[
        'dsn'=>'mysql:host=localhost;dbname=example',
        'dbname'=>'example',
        'username'=>'example',
        'password'=>'example'
    ];
    
    //可以使用 command line 建立資料庫
    const migration=[
        'dir'=>__dir__ .'/migration/', //migration檔案應該存放的路徑
        'timezone'=>'Asia/Taipei' //建立檔案時，日期使用的時區
    ];
    
    //google recapcha
    const googleRecaptcha=[
        'public'=>'填上 public key(javascript使用)',
        'private'=>'填上 private key(後端使用)'
    ];
    
    //讓 env::$model->模型名稱 可以自動實例化模型
    const model=[
        'namespace'=>'app\model',
        'list'=>[
            'User', //模型名稱，例如 'User'
            'Sys',
            'Doc'
        ]
    ];
    
    public static $models;
}
