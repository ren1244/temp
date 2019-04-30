<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class CreateTableRegTokens extends DBAccess implements Migration
{
    public function up()
    {
        $stat=$this->sql_execute(
            'CREATE TABLE regtokens(
                token CHAR(8) NOT NULL PRIMARY KEY,
                status int NOT NULL DEFAULT 1,
                create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
            'DROP TABLE `regtokens`'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        return true;
    }
}