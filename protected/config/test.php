<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array(
        'import' => array(
            'application.models.*',
            'application.components.*',
        ),
		'components'=>array(
			'fixture'=>array(
				'class'=>'system.test.CDbFixtureManager',
			),
            'db'=>array(
                'connectionString' => 'mysql:host=db;dbname=yii_db_test',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => 'rootsecret',
                'charset' => 'utf8',
            ),
		),
	)
);
