<?php
require('../../vendor/autoload.php');

use ren1244\db\DBAccess;
use ren1244\db\MigrationApp;
use ren1244\lib\Helper;

$pdo=DBAccess::connect(env::db) OR Helper::exitError(500);
$app=new MigrationApp(
    $pdo,
    env::db['dbname'],
    env::migration['dir'],
    env::migration['timezone']
);

echo '<pre>';
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
echo '</pre>';