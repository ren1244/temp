<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class CreateTableUsers extends DBAccess implements Migration
{
    public function up()
    {
        $stat=$this->sql_execute(
            /*
                name:明文
                hash:hmac(pwd,user salt); in hex
                salt:random_bytes(24) in base64_url
            */
            'CREATE TABLE users(
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(64) NOT NULL UNIQUE KEY,
                hash CHAR(64) NOT NULL,
                salt CHAR(32) NOT NULL,
                email VARCHAR(256),
                admin BOOL NOT NULL DEFAULT FALSE,
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
            'DROP TABLE `users`'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        return true;
    }
}