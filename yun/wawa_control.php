<?php
require_once "./gfun.php";
class wawa_control{
    public static function scratch_move($data,$serv,$fd){
        echo $data.PHP_EOL;
        $rev=json_decode($data,1);
        if(!self::check_sign($rev)){
            return json_encode(array("code"=>-5,"msg"=>"签名错误"));
        }
        if(empty($rev['macno'])||empty($rev['type'])||empty($rev['sysnum'])){
            return json_encode(array("code"=>-1,"msg"=>"缺少参数"));
        }
        $redis_beat=gfun::get_redis($rev['macno'].":COM");
        $macinfo=$redis_beat;
        if($macinfo['lastbeat']+30<time()){
            return json_encode(array("code"=>-2,"msg"=>"设备不在线"));
        }

//        if($rev['type']=="grab"){//如果是下爪命令就停止接收数据，不支持多进程模式
//            $serv->pause($fd);
//        }

//        $grab_stat=gfun::get_redis($rev['sysnum'].$rev['macno'].":grab");
//        $gamestart=gfun::get_redis($rev['macno'].":0AD1Start");
//        $gamefd_=gfun::get_redis(":gamefd_".$fd);
//        if($rev['type']=="grab"){//如果是下爪命令就断开链接
//            if(time()-$gamestart<7  || time()-$gamefd_ < 5){
//                $rev['type']='right';
//                $grab_stat=0;
//                echo '提前下爪'.time()-$gamestart;
//                gfun::set_redis($rev['macno']."sysnum",$rev['sysnum']);//保存当前流水下爪状态
//            }else{
//                $serv->close($fd);
//                gfun::set_redis($rev['sysnum'].$rev['macno'].":grab",1);//保存当前流水下爪状态
//            }
//        }
        $command='';
//        if($grab_stat!=1){
        $command=self::wawa_move($rev['macno'],$rev['type'],$rev['sysnum'],$rev['move_time'],$rev['top_time']);
//        }
        if($command){
            self::send($macinfo['ip'],$macinfo['port'],$command);
            return json_encode(array("code"=>1,"msg"=>"发送成功"));
        }else{
            return json_encode(array("code"=>0,"msg"=>"指令错误"));
        }
    }

    public static function wawa_move($macno,$type,$sysnum,$time=2,$top_time=80){
        $params=array(intval(($type=="up")?$time:($type=="down"?-1*$time:0))
            , intval(($type=="left")?-1*$time:($type=="right"?$time:0)), intval(($type=="grab")?1:0), 0, 0, $top_time, 0, 0);

//        echo $type.json_encode($params).PHP_EOL;
        $command="3AA300000001".$macno."00120AD2".substr("0000000".dechex($sysnum),-8).self::_scratch_param($params);
        return gfun::hextostr($command.gfun::getcrc16($command));
    }
    private static function _scratch_param($param,$first="FF55C2"){
        $str=$first;
        foreach ($param as $v)
        {
            $key = substr('0'.dechex($v),-2);
            $str .= substr($key,- 2);
        }
        $str .= self::_get_addstr(hex2bin($str));
        return $str;
    }
    private static function _get_addstr($data){
        if (strlen($data) < 2) return "00";
        $lastkey = hexdec(substr('0'.dechex(ord($data[0]) + ord($data[1])),-2));
        for ($i = 2; $i < strlen($data); $i++){
            $lastkey = hexdec(substr('0'.dechex($lastkey + ord($data[$i])),-2));
        }
        return strtoupper(substr('0'.dechex($lastkey),-2));
    }
    private static function check_sign($arr){
        if(empty($arr['sign'])||$arr['sign']!=md5($arr['macno']."DLCwawa".$arr['type'])) return false;
        return true;
    }
    private static function send($ip,$port,$command){
        $client = new Swoole\Client(SWOOLE_SOCK_UDP);
        $client->set(array(
                'bind_address'  =>  '0.0.0.0',
                'bind_port'     =>  7518,
        ));
        if (!$client->connect($ip, $port, 0.5))
        {
            return false;
        }else{
            if (!$client->send($command))
            {
                return false;
            }else{
                $client->close();
                return true;
            }
        }
    }
}