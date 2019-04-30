<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class CreateTableClasses extends DBAccess implements Migration
{
    public function up()
    {
        $stat=$this->sql_execute(
            'CREATE TABLE classes(
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                uid INT UNSIGNED NOT NULL,
                name VARCHAR(64) NOT NULL,
                create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (uid)
            ) ENGINE=InnoDB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_bin'
        );
        if($stat===false){
            return false;
        }
        return true;
    }

    public function down()
    {
        $stat=$this->sql_execute(
            'DROP TABLE classes'
        );
        if($stat===false){
            return false;
        }
        return true;
    }
}