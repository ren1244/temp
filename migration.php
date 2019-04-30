<?php
require('vendor/autoload.php');

use ren1244\db\DBAccess;
use ren1244\db\MigrationApp;
use ren1244\lib\Helper;

$pdo=DBAccess::connect(env::db) OR Helper::exitError(500);
$app=new MigrationApp($pdo, env::db['dbname'], env::migration['dir'], env::migration['timezone']);

if($argc===1){ //一般 update
    if(!$app->init()){
        Helper::exitError(500,$app->err);
    }
    $arr=$app->update();
    foreach($arr as $row){
        echo '[up] '.$row.PHP_EOL;
    }
    if($app->breakOn){
        echo '[break] '.$app->breakOn .PHP_EOL;
    }
} elseif($argc===2 && is_numeric($argv[1])) {
    if(!$app->init()){
        Helper::exitError(500,$app->err);
    }
    $arr=$app->update(intval($argv[1]));
    foreach($arr as $row){
        echo '[up] '.$row.PHP_EOL;
    }
    if($app->breakOn){
        echo '[break] '.$app->breakOn .PHP_EOL;
    }
} elseif($argc===2 && $argv[1]==='r') {
    if(!$app->init()){
        Helper::exitError(500,$app->err);
    }
    $arr=$app->rollback();
    foreach($arr as $row){
        echo '[down] '.$row.PHP_EOL;
    }
    if($app->breakOn){
        echo '[break] '.$app->breakOn .PHP_EOL;
    }
} elseif($argc===3 && $argv[1]==='r' && is_numeric($argv[2])) {
    if(!$app->init()){
        Helper::exitError(500,$app->err);
    }
    $arr=$app->rollback(intval($argv[2]));
    foreach($arr as $row){
        echo '[down] '.$row.PHP_EOL;
    }
    if($app->breakOn){
        echo '[break] '.$app->breakOn .PHP_EOL;
    }
} elseif($argc===3 && $argv[1]==='c') {
    echo $app->create($argv[2]);
}
