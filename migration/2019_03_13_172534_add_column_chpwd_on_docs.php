<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class AddColumnChpwdOnDocs extends DBAccess implements Migration
{
    public function up()
    {
        $stat=$this->sql_execute(
            'ALTER TABLE docs ADD COLUMN (`chpwd` BOOLEAN NOT NULL DEFAULT false)'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        return true;
    }

    public function down()
    {
        $stat=$this->sql_transaction([[
            'DELETE FROM docs WHERE `chpwd`=true'
        ],[
            'ALTER TABLE docs DROP COLUMN `chpwd`'
        ]]);
        if($stat===false){
            return false;
        }
        return true;
    }
}