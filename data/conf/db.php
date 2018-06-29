<?php
/**
 * 配置文件
 */
return array(
    'DB_TYPE' => 'mysqli',
    'DB_HOST'      => 'rm-wz99ouwa974707w85.mysql.rds.aliyuncs.com',             //数据库域名
    'DB_NAME'      => 'aizhuawawajidb',               //数据库名字
    'DB_USER'      => 'aizhuawawajicn',                  //数据库用户名
    'DB_PWD'  => 'wawaji123!',                         //数据库密码

    'DB_PORT' => '3306',
    'DB_PREFIX' => 'cmf_',
						
	/* redis */
	'REDIS_HOST' => "localhost",
	'REDIS_AUTH' => "rediswawaji123",
	'REDIS_PREFIX' => "zzwawaji_",
    //密钥
    "AUTHCODE" => 'rCt52pF2cnnKNB3Hkp',
    //cookies
    "COOKIE_PREFIX" => 'AJ1sOD_',
);
