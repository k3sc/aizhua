<?php
ini_set("display_errors","On");error_reporting(E_ALL);
require_once './redisreal.php';
require_once './gfun.php';
require_once './wawa_control.php';


$server = new swoole_server("0.0.0.0", 7518, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
$server->set(array('worker_num' => 32,"task_worker_num"=>1000));
$server->on('WorkerStart', function()use($server){
	$server->Task(1);
});
$server->on('Task', function($serv,$task_id,$work_id,$data)use($server){
	if($work_id==1&&$data===1){
		$server->tick(1000,function($work_id)use($server){
			//调用清队列
			request::post2("http://localhost/api/room/api",http_build_query(array('api_name'=>'roomwait_clean','token'=>'0df28be828330ee4d39460dc956d0e87')));
		});
	}
        if(!empty($data)&&$data!==1) request::post2("http://localhost/api/room/api",http_build_query($data));
});

$server->on('Finish', function($task_id,$work_id)use($server){
});
//UDP监听数据发送事件
$server->on('Packet', function ($serv, $data, $clientInfo){
	$msg=date("7518---Y-m-d H:i:s",time())."----".$clientInfo['address']."|".$clientInfo['port']."Received:".gfun::strtohex($data)."\n";
//	echo $msg;
	$send = Monitor::index($data,$clientInfo,$serv);
	
	if(!empty($send)){
		if(is_array($send)){
			$count=1;
			foreach($send as $da){
				// $msg=date("Y-m-d H:i:s")."----".$clientInfo['address']."|".$clientInfo['port']."Send".$count.":".$da."\n";
				// echo $msg;
				$serv->sendto($clientInfo['address'], $clientInfo['port'], gfun::hextostr($da),$clientInfo['server_socket']);
				$count++;
			}
		}
		else
		{
			// $msg=date("Y-m-d H:i:s")."----".$clientInfo['address']."|".$clientInfo['port']."Send:".$send."\n";
			// echo $msg;
			$serv->sendto($clientInfo['address'], $clientInfo['port'], gfun::hextostr($send),$clientInfo['server_socket']);
		}
	}
});



$port1=$server->listen("",5188,SWOOLE_SOCK_TCP);//娃娃机操作

$port1->set(array('ssl_cert_file' => 'ssl.cert','ssl_key_file' => 'ssl.key','heartbeat_check_interval' => 30,'heartbeat_idle_time' => 60));
//TCP监听连接进入事件
$port1->on('connect', function ($serv, $fd) {
	$fdinfo = $serv->connection_info($fd);
	gfun::set_redis('gamefd_'.$fd,time());
	echo date("Y-m-d H:i:s",time())."----".$fdinfo['remote_ip']."|5188:".$fdinfo['remote_port']."|".$fd.":Client: Connect.\n";
});

//TCP监听数据发送事件
$port1->on('receive', function ($serv, $fd, $from_id, $data) {
	$fdinfo = $serv->connection_info($fd);
	//echo date("Y-m-d H:i:s",time())."----".$fdinfo['remote_ip']."|5188:".$fdinfo['remote_port']."|".$fd."--Received:".gfun::strtohex($data)."\n";
	$ret=wawa_control::scratch_move($data,$serv,$fd);
	if(!empty($ret)){
		$serv->send($fd,$ret);
//		echo date("Y-m-d H:i:s",time())."----".$fdinfo['remote_ip']."|5188:".$fdinfo['remote_port']."|".$fd."--Send:".$ret;
	}
});
//TCP监听连接关闭事件
$port1->on('close', function ($serv, $fd) {
	$fdinfo = $serv->connection_info($fd);
	echo date("Y-m-d H:i:s",time())."----".$fdinfo['remote_ip']."|5188:".$fdinfo['remote_port']."|".$fd.":Client: Close.\n";
});

//启动服务器
$server->start();

