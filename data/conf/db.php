<?php
/**
 * 配置文件
 */
return array(
    'DB_TYPE' => 'mysqli',
    'DB_HOST'      => '127.0.0.1',             //数据库域名
    'DB_NAME'      => 'aizhuawawajidb',               //数据库名字
    'DB_USER'      => 'root',                  //数据库用户名
    'DB_PWD'  => 'aizhua123!@#Q',                         //数据库密码

    'DB_PORT' => '3306',
    'DB_PREFIX' => 'cmf_',
						
	/* redis */
	'REDIS_HOST' => "localhost",
	'REDIS_AUTH' => "",
	'REDIS_PREFIX' => "zzwawaji_",
    //密钥
    "AUTHCODE" => 'rCt52pF2cnnKNB3Hkp',
    //cookies
    "COOKIE_PREFIX" => 'AJ1sOD_',
);
