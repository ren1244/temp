<?php
namespace ren1244\db\traits;
trait KeyValue
{
    public function set($key, $val)
    {
        $stat=$this->sql_execute(
            'REPLACE INTO `'.$this->table.'`(`key`,`val`) VALUES(:?,:?)',
            [$key,$val]
        );
        if($stat===false){
            return false;
        }
        return true;
    }
    
    public function get($key)
    {
        $stat=$this->sql_execute(
            'SELECT `val` FROM `'.$this->table.'` WHERE `key` = :?',
            [$key]
        );
        if($stat===false){
            return false;
        }
        $result=$stat->fetchAll(\PDO::FETCH_ASSOC);
        if(count($result)===0){
            return NULL;
        }
        return $result[0]['val'];
    }
    
    public function unset($key)
    {
        $stat=$this->sql_execute(
            'DELETE FROM `'.$this->table.'` WHERE `key` = :?',
            [$key]
        );
        if($stat===false){
            return false;
        }
        return $stat->rowCount();
    }
}
