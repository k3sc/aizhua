<?php
require_once "./gfun.php";
require_once "./request.php";
class Monitor
{
	static function index($data,$clientInfo,$server){
		if(strlen($data)>10&&ord($data[0])==58&&ord($data[1])==163&&ord($data[2])==0&&ord($data[3])==0&&ord($data[4])==0){//判断是否机器命令
			$addrlength=gfun::getaddrlength($data);
			$macno=gfun::getmacno($data);
			//保存连接信息到redis
			$redis_mac=array("ip"=>$clientInfo['address'],"port"=>$clientInfo['port'],"lastbeat"=>time());
	        gfun::set_redis($macno.":COM",$redis_mac);

			$cindex=$addrlength+8;
			$cindexA=$addrlength+9;
			if(ord($data[$cindex])==9){//机器主动指令
				if(ord($data[$cindexA])==1){//登录01
					$command=self::logon($data,$clientInfo);
					return $command;
				}
				if(ord($data[$cindexA])==2){//心跳02
					$command=self::jump($data,$clientInfo,$server);
					return $command;
				}
			}
			else{//机器应答指令
				$addrlength=gfun::getaddrlength($data);
				$macno=gfun::getmacno($data);
				$content='机器指令应答';
				$com=strtoupper(bin2hex(substr($data,$addrlength+8,2)));
				switch($com){
		case "0AD0"://娃娃机控制指示灯和激光动作0AD0
                        $save=ord($data[$addrlength+16]);
                        break;
                    case "0AD1"://娃娃机开始游戏命令0AD1
                        $save=ord($data[$addrlength+14]);
						gfun::set_redis($macno.":0AD1Start",time());
                        break;
                    case "0AD2"://娃娃机操作天车命令0AD2
                        $save=ord($data[$addrlength+14]);
                        break;
                    case "0AD3"://娃娃机甩抓命令0AD3
                        $save=ord($data[$addrlength+14]);
                        break;
				}
                gfun::set_redis($macno.":".$com,array("ctime"=>time(),"data"=>$save));
				if(!empty($command)) return $command.gfun::getcrc16($command);
			}
		}else{
			//initiative::saveothercommand($data,$clientInfo["address"],$clientInfo["port"],0,"其他指令");
			return gfun::strtohex("Received:".$data);
		}
	}
	
	static function logon($data,$clientInfo){//登录
		$command=gfun::getprecom($data)."0003090100";
		//保存记录格式、机器类型、通讯方式,协议控制字
		$addrlength=gfun::getaddrlength($data);
		$macno=gfun::getmacno($data);
		$rtype=substr(gfun::strtohex($data),($addrlength+34)*2,(strlen($data)-$addrlength-36)*2);
		$mtype=substr(gfun::strtohex($data),($addrlength+10)*2,8);
		return $command.gfun::getcrc16($command);
	}
	static function jump($data,$clientInfo,$server){//心跳
		self::updatebeat($data,$clientInfo,$server);
		$command=gfun::getprecom($data)."000B090201".gfun::gettime(time());
		return $command.gfun::getcrc16($command);
	}
	static function updatebeat($data,$clientInfo,$server){//更新保存温度湿度
		$addrlength=gfun::getaddrlength($data);
		$macno=gfun::getmacno($data);
		$post["macno"]=$macno;
		
    	$post['sysnum']=hexdec(bin2hex(substr($data,$addrlength+17,4)));//游戏流水:当前的游戏流水
        $post['status']=ord($data[$addrlength+21]);//机器运行状态0x00：空闲，0x01：正在使用0xF1：爪子上升超时。0xF2：天车回原位超时0xF3：前后电机电流异常0xF4：左右电机电流异常0xF5：上下电机电流异常0xF6：爪子线圈电流异常0xF7：光眼异常0xF8：保留，无意义0xF9：参数不合法
        $post['gift']=ord($data[$addrlength+22]);//机器出礼品数量:本次游戏流水对应的游戏的礼品数量
        if(ord($data[$addrlength+14])>=14) $post['error']=ord($data[$addrlength+23]);
        $post['api_name']="success";
        $post['token']='0df28be828330ee4d39460dc956d0e87';
        //通知状态信息
//        request::post2("http://localhost/api/room/api",http_build_query($post));//同步发POST，阴塞接收
		$server->task($post);
	}
}
