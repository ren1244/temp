<?php
namespace app\model;

use ren1244\db\DBAccess;
use ren1244\lib\Helper;

class Doc extends DBAccess
{
    /*
    public function createClass()
    {
    }
    
    public function readClass($uid)
    {
        
    }
    
    public function updateClass($id)
    {
    }
    
    public function deleteClass($id)
    {
    }
    */
    private function createRootIfNotExists($uid)
    {
        $stat=$this->sql_execute(
            'INSERT INTO classes(uid,name)
            SELECT :?,\'/\' FROM DUAL
            WHERE NOT EXISTS (
                SELECT 1 FROM classes
                WHERE uid=:? AND name=\'/\'
            )',
            [$uid,$uid]
        ) OR Helper::exitError(500,$this->err);
    }
    
    private function getUserCid($uid, $cid)
    {
        $stat=$this->sql_execute(
            '(
                SELECT C.id id,1 od
                FROM classes C
                JOIN users U
                ON C.uid=U.id AND U.id=:? AND C.id=:?
            ) UNION (
                SELECT id,2 od
                FROM classes
                WHERE uid=:? AND name=\'/\'
            ) ORDER BY od',
            [$uid,$cid,$uid]
        ) OR Helper::exitError(500,$this->err);
        $r=$stat->fetchAll(\PDO::FETCH_COLUMN, 0);
        if(count($r)===0){
            return null;
        }
        return (int)$r[0];
    }
    
    public function createDoc($uid, $cid, $title, $content, $chpwd=false)
    {
        //確認有根目錄
        $this->createRootIfNotExists($uid);
        //如果cid不合法則指向根目錄
        $cid=$this->getUserCid($uid, $cid);
        if(is_null($cid)){
            Helper::exitError(500);
        }
        //寫入資料
        $stat=$this->sql_execute(
            'INSERT INTO docs(cid,title,content,chpwd) VALUES(:?,:?,:?,:?)',
            [$cid, $title, $content, $chpwd]
        ) OR Helper::exitError(500,$this->err);
        return intval(\env::$models->getPdo()->lastInsertId());
    }
    
    public function readDoc($uid, $docId)
    {
        $stat=$this->sql_execute(
            'SELECT D.*, C.uid
            FROM docs D
            JOIN classes C ON C.id=D.cid AND D.id=:? AND C.uid=:?',
            [$docId,$uid]
        ) OR Helper::exitError(500,$this->err);
        $r=$stat->fetchAll(\PDO::FETCH_ASSOC);
        if(count($r)===0){
            return null;
        }
        return $r[0];
    }
    
    public function updateDoc($uid, $docId, $title, $content, $chpwd=false)
    {
        $stat=$this->sql_execute(
            'UPDATE docs SET title=:?, content=:?, chpwd=:?
            WHERE id=:? AND (
                SELECT 1 FROM (
                    SELECT 1 FROM docs D
                    JOIN classes C
                    ON C.id=D.cid AND D.id=:? AND C.uid=:?
                ) AS tmp
            )',
            [$title, $content, $chpwd, $docId, $docId, $uid]
        ) OR Helper::exitError(500,$this->err);
        return $stat->rowCount();
    }
    
    public function deleteDoc($uid ,$docId)
    {
        $stat=$this->sql_execute(
            'DELETE FROM docs WHERE id=:? AND (
                SELECT 1 FROM (
                    SELECT 1 FROM docs D
                    JOIN classes C
                    ON C.id=D.cid AND D.id=:? AND C.uid=:?
                ) AS tmp
            )',
            [$docId, $docId, $uid]
        ) OR Helper::exitError(500,$this->err);
        return $stat->rowCount();
    }
    
    public function getDocList($uid)
    {
        $stat=$this->sql_execute(
            'SELECT D.id,D.cid,D.title,C.name class,D.title,D.create_time,D.update_time
            FROM users U
            JOIN classes C ON U.id=C.uid AND U.id=:?
            JOIN docs D ON C.id=D.cid AND D.chpwd=:?',
            [$uid, false]
        ) OR Helper::exitError(500,$this->err);
        return $stat->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function deleteChpwd($uid)
    {
        $stat=$this->sql_execute(
            'DELETE FROM docs WHERE EXISTS (
                SELECT * FROM classes
                WHERE classes.id=docs.cid
                    AND classes.uid=:?
                    AND docs.chpwd=:?
            )',
            [$uid, true]
        ) OR Helper::exitError(500,$this->err);
        return $stat->rowCount();
    }
    
    public function swapChpwd($uid)
    {
        $stat=$this->sql_transaction([[
            'DELETE FROM docs WHERE EXISTS (
                SELECT * FROM classes
                WHERE classes.id=docs.cid
                    AND classes.uid=:?
                    AND docs.chpwd=:?
            )',
            [$uid, false]
        ],[
            'UPDATE docs SET chpwd=:? WHERE EXISTS (
                SELECT * FROM classes
                WHERE classes.id=docs.cid
                    AND classes.uid=:?
            )',
            [false, $uid]
        ]]) OR Helper::exitError(500,$this->err);
        return true;
    }
}
    