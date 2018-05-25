<?php
/**
 * $APP_NAME 统一入口
 */

//define("LAN",1);//1简体 2繁体

 $ipjson = file_get_contents("../Config/iplimit.php");

 $iplimitarray = json_decode($ipjson,true);
 if(in_array($_SERVER["REMOTE_ADDR"],$iplimitarray)){
	 header('HTTP/1.1 403 Forbidden');
	echo "Access forbidden";
	exit;
 }
require_once dirname(__FILE__) . '/init.php';

//装载你的接口
DI()->loader->addDirs('Appapi');

//file_put_contents('./../../data/runtime/log.txt', var_export($_POST, true).var_export($_GET, true));

/** ---------------- 响应接口请求 ---------------- **/

$api = new PhalApi();
$rs = $api->response();
$rs->output();

