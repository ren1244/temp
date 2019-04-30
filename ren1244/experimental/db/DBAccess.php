<?php
namespace ren1244\db;
use \PDO;
use \PDOException;
 /**
  * 作為基本的 class，提供一些簡單的方法
  * 
  * @version 1.0.0
  * @author ren1244
  * @since 0.2.0 2019/02/14 ren1244: 增加 connect 靜態函數處理 pdo 連線; 並分離 orm 部分
  * @since 0.1.1 2019/01/30 ren1244: 增加 sql_execute 可以為 null
  * @since 0.1.0 2019/01/08 ren1244: 修正多個欄位排序SQL語法錯誤
  */
class DBAccess
{
    public $pdo;
    public $err;
    
    public function __construct($pdo)
    {
        $this->pdo=$pdo;
        $this->err="";
    }
    
    /** 
     * 連接資料庫
     *
     * @param arrau $cfg 含有 dsn,username,password的關聯陣列
     * @return pdo|false pdo資源或是錯誤
     */
    public static function connect($cfg)
    {
        try {
            $pdo = new PDO($cfg['dsn'], $cfg['username'], $cfg['password']);
        } catch (PDOException $e) {
            return false;
        }
        return $pdo;
    }
    
    /** 
     * 執行 sql 語法
     *
     * @param string sql sql敘述句
     * @param array param 參數陣列
     * @param bool dbg 發生錯誤時是否直接列印錯誤
     * @return resource|false PDOStatement物件或錯誤
     */
    public function sql_execute($sql, $param=false, $dbg=false)
    {
        if(!$param)
            $param=[];
        $arr=explode(':?',$sql);
        $n=count($param);
        if($n+1 != count($arr)){
            $this->err="sql_execute:參數數量不合";
            return false;
        }
        foreach($arr as $k=>$v){
            if($k==0){
                $sql=$v;
            } else {
                $sql.=':param'.$k.$v;
            }
        }
        if($dbg)
            echo $sql;
        $stat=$this->pdo->prepare($sql);
        if($stat===false)
            return false;
        for($i=0;$i<$n;++$i){
            $type=gettype($param[$i]);
            if($type=='integer') {
                $type=PDO::PARAM_INT;
            } elseif($type=='boolean') {
                $type=PDO::PARAM_BOOL;
            } elseif($type=='NULL') {
                $type=PDO::PARAM_NULL;
            } else {
                $type=PDO::PARAM_STR;
            }
            if($dbg)
                echo " bindValue(".$param[$i].")";
            $stat->bindValue(":param".($i+1), $param[$i], $type);
        }
        if(!$stat->execute()){
            $this->err="sql_execute:執行錯誤:".implode("###",$stat->errorInfo());
            return false;
        }
        return $stat;
    }
    
    public function sql_execute_all($sqlList, &$n)
    {
        foreach($sqlList as $idx=>$sqlcfg){
            $n=count($sqlcfg);
            if($n===1){
                $stat=$this->sql_execute($sqlcfg[0]);
            } elseif($n===2) {
                $stat=$this->sql_execute($sqlcfg[0],$sqlcfg[1]);
            } elseif($n===3) {
                $stat=$this->sql_execute($sqlcfg[0],$sqlcfg[1]);
                if($sqlcfg[2] && $stat->rowCount()<$sqlcfg[2]){
                    $stat=false;
                }
            } else {
                $stat=false;
            }
            if($stat===false){
                $n=$idx;
                return false;
            }
        }
        $n=$idx+1;
        return $stat;
    }
    
    const TS_START=1;
    const TS_END=2;
    const TS_SINGLE=3;
    const TS_MID=0;
    
    /** 
     * 以 transaction 執行一系列的 sql 語法
     *
     * @param array sqlList 此陣列的每個元素也是一個陣列，即 [sql, optParamArray, optRowCountCondition]
     * @param array opt transaction 模式，如果 transaction 中途需要 php 處理一些數據，TS_START ->處理->TS_END 
     * @return 型別 敘述
     */
    public function sql_transaction($sqlList, $opt=DBAccess::TS_SINGLE)
    {
        if($opt&DBAccess::TS_START){
            $stat=$this->sql_execute('START TRANSACTION');
            if($stat===false){
                return false;
            }
        }
        
        $stat=$this->sql_execute_all($sqlList,$n);
        if($stat===false){
            $stat=$this->sql_execute('ROLLBACK');
            return false;
        }
        
        if($opt&DBAccess::TS_END){
            $stat=$this->sql_execute('COMMIT');
            if($stat===false){
                return false;
            }
            return true;
        }
        return $stat;
    }
}
