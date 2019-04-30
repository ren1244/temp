<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class CreateTableDocs extends DBAccess implements Migration
{
    public function up()
    {
        $stat=$this->sql_execute(
            'CREATE TABLE docs(
                id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                cid INT UNSIGNED NOT NULL,
                title TINYBLOB NOT NULL,
                content MEDIUMBLOB NOT NULL,
                create_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX (cid)
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
            'DROP TABLE docs'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        return true;
    }
}