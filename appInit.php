<?php
env::$models=new ren1244\db\ModelContainer(function(){
    try{
        $pdo=new PDO(env::db['dsn'], env::db['username'], env::db['password']);
    } catch (PDOException $e) {
        ren1244\lib\Helper::exitError(500);
    }
    return $pdo;
},env::model['namespace']);
foreach(env::model['list'] as $model)
{
    env::$models->addModel($model);
}