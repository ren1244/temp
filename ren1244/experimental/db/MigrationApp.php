<?php
namespace ren1244\db;

use ren1244\lib\Helper;

class MigrationApp extends DBAccess
{
    private $dbName;
    private $dirName;
    private $timeZone;
    private $fileList;
    public $breakOn=false;
    
    public function __construct($pdo, $dbName, $dirName, $timeZone)
    {
        //儲存環境變數
        parent::__construct($pdo);
        $this->dbName=$dbName;
        $this->dirName=$dirName;
        $this->timeZone=$timeZone;
        date_default_timezone_set($this->timeZone);
    }
    
    //初始化
    public function init()
    {
        //取得檔案列表給 $this->fileList
        $this->fileList=$this->getFileList();
        if($this->fileList===false){
            return false;
        }
        //檢查資料表 migration 是否存在，沒有的話初始化
        if(!$this->prepareDB()){
            return false;
        }
        return true;
    }
    
    //建立一個新的 Migration
    public function create($name)
    {
        $pathName=$this->dirName
                 .date('Y_m_d_His_',time())
                 .$name.'.php';
        $className=$this->getClassName($name);
        file_put_contents($pathName, $this->getTemplate($className));
        return $pathName;
    }
    
    /** 
     * 執行 update，需要先執行過 $this->init
     *
     * @param int $step 要往後執行幾步，如果<0則跑到最後
     * @return array 執行成功的 file，確認 $this->breakOn 可能有最後發生錯誤的檔名
     */
    public function update($step=-1)
    {
        $n=$this->getLastIndex();
        if($n===false){
            return false;
        }
        $arr=[];
        $m=count($this->fileList);
        if($step>=0){
            $m=min($n+$step,$m);
        }
        for($i=$n;$i<$m;++$i){
            $obj=$this->getMigration($i);
            if($obj->up()){
                $stat=$this->sql_execute(
                    'INSERT INTO `migration`(`fname`) VALUES(:?)',
                    [$this->fileList[$i]['fname']]
                );
                $arr[]=$this->fileList[$i]['fname'];
            } else {
                $this->breakOn=$this->fileList[$i]['fname'];
                break;
            }
        }
        return $arr;
    }
    
    /** 
     * 執行 rollback，需要先執行過 $this->init
     *
     * @param int $step 要往前回滾幾步，如果<0則復原到最初
     * @return array|false 執行成功的 file，確認 $this->breakOn 可能有最後發生錯誤的檔名
     */
    public function rollback($step=-1)
    {
        $n=$this->getLastIndex();
        if($n===false){
            return false;
        }
        $arr=[];
        $m=0;
        if($step>=0){
            $m=max($n-$step,$m);
        }
        for($i=$n-1;$i>=$m;--$i){
            $obj=$this->getMigration($i);
            if($obj->down()){
                $stat=$this->sql_execute(
                    'DELETE FROM `migration` WHERE `fname` = :?',
                    [$this->fileList[$i]['fname']]
                );
                $arr[]=$this->fileList[$i]['fname'];
            } else {
                $this->breakOn=$this->fileList[$i]['fname'];
                break;
            }
        }
        return $arr;
    }
    
    /** 
     * 取得檔案列表
     *
     * @return array|false 檔案列表
     */
    private function getFileList()
    {
        $arr=[];
        $list=scandir($this->dirName,SCANDIR_SORT_ASCENDING);
        foreach($list as $fInDir){
            if(is_dir($this->dirName .$fInDir)){
                continue;
            }
            if(preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_(\w+)\.php$/',$fInDir,$mch)!==1){
                $this->err=$fInDir.' does not match Migration format.';
                return false;
            }
            $arr[]=[
                'fname'=>substr($fInDir,0,-4),
                'cname'=>$this->getClassName($mch[1])
            ];
        }
        return $arr;
    }
    
    /** 
     * 比對資料庫與 FileList 資料
     *
     * @return int|false 符合的筆數(等於資料庫的筆數)
     */
    private function getLastIndex()
    {
        $stat=$this->sql_execute(
            'SELECT `fname` FROM `migration`'
        );
        if($stat===false){
            return false;
        }
        $result=$stat->fetchAll(\PDO::FETCH_ASSOC);
        $n=count($result);
        for($i=0;$i<$n;++$i){
            if(!isset($this->fileList[$i]) || $result[$i]['fname']!==$this->fileList[$i]['fname']){
                $this->err='there is no file match '.$result[$i]['fname'];
                return false;
            }
        }
        return $n;
    }
    
    /** 
     * 檢查表 migration 存在或建立
     *
     * @return bool 執行成功或失敗
     */
    private function prepareDB()
    {
        //檢查資料表存在
        $stat=$this->sql_execute(
            'SELECT 1 FROM information_schema.TABLES WHERE (TABLE_SCHEMA = :?) AND (TABLE_NAME = :?)',
            [$this->dbName,'migration']
        );
        if($stat===false){
            return false;
        }
        $r=$stat->fetch(\PDO::FETCH_ASSOC);
        //如果資料表不存在，則建立資料表
        if($r===false){
            $stat=$this->sql_execute(
                'CREATE TABLE `migration` (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    fname VARCHAR(64) NOT NULL
                )'
            );
            if($stat===false){
                return false;
            }
        }
        return true;
    }
    
    /** 
     * 把名稱轉換為 className
     *
     * @param string $name 扣除日期部分的檔名
     * @return string className
     */
    private function getClassName($name)
    {
        $arr=explode('_',$name);
        $n=count($arr);
        for($i=0;$i<$n;++$i){
            if(strlen($arr[$i])>0){
                $str=strtoupper(substr($arr[$i],0,1))
                    .strtolower(substr($arr[$i],1));
                $arr[$i]=$str;
            }
        }
        return implode('',$arr);
    }
    
    /** 
     * 取得樣板
     *
     * @param string $className className
     * @return string 文件內容
     */
    private function getTemplate($className)
    {
        return '<?php

use ren1244\db\DBAccess;
use ren1244\db\Migration;

class '.$className.' extends DBAccess implements Migration
{
    public function up()
    {
        return true;
    }

    public function down()
    {
        return true;
    }
}';
    }
    
    /** 
     * 取得 Migration 物件
     *
     * @param int $idx $this->fileList 的索引
     * @return object Migration 物件
     */
    private function getMigration($idx)
    {
        require($this->dirName .$this->fileList[$idx]['fname'] .'.php');
        $className=$this->fileList[$idx]['cname'];
        return new $className($this->pdo);
    }
}