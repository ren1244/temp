<?php
namespace app\model;

use ren1244\lib\Helper;
use ren1244\db\DBAccess;
use ren1244\db\traits\KeyValue;

class Sys extends DBAccess
{
    private $table='sys';
    use KeyValue;
    
    public function getNext($key)
    {
        $stat=$this->sql_transaction([[
            'SELECT `val` FROM '.$this->table.' WHERE `key`=:? FOR UPDATE',
            [$key]
        ]],DBAccess::TS_START);
        if($stat===false){
            Helper::exitError(500);
        }
        $r=$stat->fetchAll(\PDO::FETCH_ASSOC);
        if(count($r)<1){
            $stat=$this->sql_transaction([[
                'INSERT INTO '.$this->table.'(`key`,`val`) VALUES(:?,:?)',
                [$key,1],
                1
            ]],DBAccess::TS_END) OR Helper::exitError(500);
            return 1;
        }
        $stat=$this->sql_transaction([[
            'UPDATE '.$this->table.' SET `val`=`val`+1 WHERE `key`=:?',
            [$key],
            1
        ]],DBAccess::TS_END) OR Helper::exitError(500);
        return intval($r[0]['val'],10)+1;
    }
}
