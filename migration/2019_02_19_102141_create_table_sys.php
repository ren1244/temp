<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class CreateTableSys extends DBAccess implements Migration
{
    public function up()
    {
        $stat=$this->sql_execute(
            'CREATE TABLE sys(
                `key` VARCHAR(32) NOT NULL PRIMARY KEY,
                `val` VARCHAR(1024) NOT NULL
            ) ENGINE=InnoDB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        return true;
    }

    public function down()
    {
        $stat=$this->sql_execute(
            'DROP TABLE `sys`'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        return true;
    }
}