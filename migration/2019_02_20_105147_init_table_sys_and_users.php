<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class InitTableSysAndUsers extends DBAccess implements Migration
{
    public function up()
    {
        //鹽
        $salt=base64_encode(random_bytes(24));
        $salt2=base64_encode(random_bytes(24));
        $encSalt=base64_encode(random_bytes(24));
        $hash=hash_hmac('sha256','',$salt,true);
        $hash=hash_hmac('sha256',$hash,$salt2);
        $sqlList=[[
            'START TRANSACTION'
        ],[
            'INSERT INTO sys (`key`,`val`) VALUES (:?,:?),(:?,:?)',
            ['salt',$salt,'encSalt',$encSalt]
        ],[
            'INSERT INTO users (`name`,`hash`,`salt`,`admin`) VALUES (\'admin\',:?,:?,true)',
            [$hash,$salt2]
        ],[
            'COMMIT'
        ]];
        $stat=$this->sql_execute_all($sqlList,$n);
        if($n!==4){
            echo $n.' '.$this->err;
            return false;
        }
        return true;
    }

    public function down()
    {
        $sqlList=[[
            'START TRANSACTION'
        ],[
            'DELETE FROM `users` WHERE `name`=\'admin\''
        ],[
            'DELETE FROM `sys` WHERE `key`=\'salt\''
        ],[
            'COMMIT'
        ]];
        $stat=$this->sql_execute_all($sqlList,$n);
        if($n!==4){
            echo $this->err;
            return false;
        }
        return true;
    }
}