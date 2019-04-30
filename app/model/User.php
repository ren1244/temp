<?php
namespace app\model;

use ren1244\db\DBAccess;

class User extends DBAccess
{
    public function getByName($name)
    {
        $stat=$this->sql_execute(
            'SELECT * FROM `users` WHERE `name`=:?',
            [$name]
        );
        if($stat===false){
            return false;
        }
        $r=$stat->fetchAll(\PDO::FETCH_ASSOC);
        if(count($r)===0){
            return false;
        }
        return $r[0];
    }
    
    public function createTokens($num, &$result)
    {
        for($i=0;$i<$num;++$i){
            for($j=0;$j<10;++$j){
                $token=strtr(base64_encode(random_bytes(6)),'+/','-_');
                $stat=$this->sql_execute(
                    'INSERT INTO `regtokens` (`token`)
                    SELECT :? FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM `regtokens`
                        WHERE `token`=:?
                    )',
                    [$token, $token]
                );
                if($stat===false){
                    return false;
                }
                if($stat->rowCount()>0){
                    $stat=$this->sql_execute(
                        'SELECT `token`,`status`,UNIX_TIMESTAMP(`create_time`) AS `create_time` FROM `regtokens` WHERE `token`=:? AND `status` >= 1',
                        [$token]
                    );
                    $result[]=$stat->fetchAll(\PDO::FETCH_ASSOC)[0];
                    break;
                }
            }
            if($j===10){
                return $i;
            }
        }
        return $i;
    }
    
    public function getTokenList()
    {
        $stat=$this->sql_execute(
            'SELECT `token`,`status`,UNIX_TIMESTAMP(`create_time`) AS `create_time` FROM regtokens WHERE `status` >= 1 ORDER BY `create_time`'
        );
        if($stat===false){
            return false;
        }
        return $stat->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function deleteToken($token)
    {
        $stat=$this->sql_execute(
            'DELETE FROM regtokens WHERE `token`=:? OR `status` <= 0',
            [$token]
        );
        if($stat===false){
            return false;
        }
        return $stat->rowCount();
    }
    
    public function getNewSalt()
    {
        return base64_encode(random_bytes(24));
    }
    
    private function setNewSaltAndHash(&$pwd, &$salt)
    {
        $salt=$this->getNewSalt();
        $pwd=hash_hmac('sha256',hex2bin($pwd),$salt);
    }
    
    public function create($token, $name, $hash, $email=null)
    {
        $this->setNewSaltAndHash($hash,$salt);
        return $this->sql_transaction([[
            'INSERT INTO `users` (`name`,`hash`,`salt`,`email`)
            SELECT :?,:?,:?,:? FROM regtokens
            WHERE token=:? AND NOT EXISTS (
                SELECT 1 FROM `users` WHERE `name`=:?
            )',
            [$name, $hash, $salt, $email, $token, $name],1
        ],[
            'DELETE FROM regtokens WHERE `token`=:?',
            [$token],1
        ]]);
    }
    
    public function changePwd($id, $newPwd ,$newSalt)
    {
        $newPwd=hash_hmac('sha256',hex2bin($newPwd),$newSalt);
        $stat=$this->sql_execute(
            'UPDATE `users`
            SET `hash`=:?, `salt`=:?
            WHERE `id`=:?',
            [$newPwd, $newSalt, $id]
        );
        if($stat===false){
            return false;
        }
        return $stat->rowCount();
    }
    
    public function update($id ,$hash=null ,$email=null)
    {
        $qryArr=[];
        $paramArr=[];
        $params=['hash','email'];
        foreach($params as $p){
            if(!is_null($$p)){
                $qryArr[]="`$p`=:?";
                $paramArr[]=$$p;
            }
        }
        if(count($qryArr)===0){
            return false;
        }
        $paramArr[]=$id;
        $stat=$this->sql_execute(
            'UPDATE `users` SET '
            .implode(',', $qryArr)
            .' WHERE `id`=:?',
            $paramArr
        );
        if($stat===false){
            return false;
        }
        return $stat->rowCount();
    }
    
    /** 
     * 取得使用者列表
     *
     * @return array|false; 
     */
    public function getUserList()
    {
        $stat=$this->sql_execute('SELECT
            `id`,`name`,`email`,`admin`,`create_time`,`update_time`
            FROM `users`');
        if($stat===false) {
            return false;
        }
        $r=$stat->fetchAll(\PDO::FETCH_ASSOC);
        return $r;
    }
    
    /** 
     * 刪除使用者
     *
     * @param int uid 使用者id
     * @return bool 成功或是失敗
     */
    public function deleteUser($uid)
    {
        if($uid<=1) {
            return false;
        }
        $stat=$this->sql_transaction([[
            'DELETE `docs`
            FROM `users`
            INNER JOIN `classes` ON `users`.id=`classes`.uid
            INNER JOIN `docs` ON `docs`.cid=`classes`.id
            WHERE `users`.id=:?',
            [$uid]
        ],[
            'DELETE `classes`
            FROM `users`
            INNER JOIN `classes` ON `users`.id=`classes`.uid
            WHERE `users`.id=:?',
            [$uid]
        ],[
            'DELETE FROM `users` WHERE `users`.id=:?',
            [$uid]
        ]]);
        if($stat===false) {
            var_dump($this->err);
            return false;
        }
        return true;
    }
    
    /** 
     * 變更使用者權限
     *
     * @param int uid 使用者id
     * @param bool admin 是否有權限
     * @return bool 成功或是失敗
     */
    public function setAdmin($uid, $admin)
    {
        if($uid<=1) {
            return false;
        }
        $stat=$this->sql_execute(
            'UPDATE users SET admin=:? WHERE id=:?',
            [$admin, $uid]
        );
        if($stat===false || $stat->rowCount()<=0){
            return false;
        }
        return true;
    }
}