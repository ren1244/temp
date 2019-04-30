<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class AddConstraintOnClassesAndDocs extends DBAccess implements Migration
{
    public function up()
    {
        $stat=$this->sql_execute(
            'ALTER TABLE classes
            ADD CONSTRAINT classes_fk FOREIGN KEY (uid)
            REFERENCES users(id)'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        $stat=$this->sql_execute(
            'ALTER TABLE docs
            ADD CONSTRAINT docs_fk FOREIGN KEY (cid)
            REFERENCES classes(id)'
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
            'ALTER TABLE classes
            DROP FOREIGN KEY classes_fk'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        $stat=$this->sql_execute(
            'ALTER TABLE docs
            DROP FOREIGN KEY docs_fk'
        );
        if($stat===false){
            echo $this->err;
            return false;
        }
        return true;
    }
}