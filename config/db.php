<?php
/*
	Конфигурация подключения к БД
*/
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'oci:dbname=//192.168.51.112:1521/xe;charset=UTF8;',
    'username' => 'JOURNAL',
    'password' => 'ah8dM5t1',
    'enableSchemaCache'=>true,
    // 'schemaMap'=>['oci'=>'app\components\ESchemaOci',],
    // 'attributes'=>[PDO::ATTR_CASE=> PDO::CASE_UPPER],
];
