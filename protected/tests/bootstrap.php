<?php
$mockPath = dirname(__FILE__) . '/mock';
set_include_path(get_include_path() . PATH_SEPARATOR . $mockPath);

$yiit = dirname(__FILE__).'/../../vendor/yiisoft/yii/framework/yiit.php';
$config = dirname(__FILE__).'/../config/test.php';

require_once($yiit);

if (Yii::app() === null) {
    Yii::createWebApplication($config);
}