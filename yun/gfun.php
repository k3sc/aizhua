<?php
class gfun{
	public static function my_redis(){
		$redis_conf = include '../data/conf/db.php';
        include_once '../simplewind/Lib/Extend/TPRedis.php';
        return new \TPRedis(array('host' => $redis_conf['REDIS_HOST'],'auth' => $redis_conf['REDIS_AUTH'], 'prefix' => $redis_conf['REDIS_PREFIX'],'format'=>'json'));
	}
	public static function set_redis($key,$value){
		$redis=self::my_redis();
		$result = $redis->set($key,$value);
		return $result;
	}
	public static function get_redis($key){
		$redis=self::my_redis();
		$result = $redis->get($key);
		return $result;
	}
	public static function delete_redis($key){
		$redis=self::my_redis();
		$result = $redis->delete($key);
		return $result;
	}
	public static function push_redis($key,$value){
		$redis=self::my_redis();
		$result = $redis->rpush($key,$value);
		return $result;
	}
	public static function pop_redis($key){
		$redis=self::my_redis();
		$result = $redis->lpop($key);
		return $result;
	}
	public static function range_redis($key){
		$redis=self::my_redis();
		$result = $redis->lrange($key);
		return $result;
	}
	public static function get_total_millisecond(){//返回字符串的毫秒数时间戳 
    	$time = explode (" ", microtime () );
    	$time = $time [1] . ($time [0] * 1000);
    	$time2 = explode ( ".", $time );
    	$time = $time2 [0];
    	return $time;
    }
    public static function microtime_float(){//返回当前 Unix 时间戳和微秒数(用秒的小数表示)浮点数表示，常用来计算代码段执行时间
    	list($usec, $sec) = explode(" ", microtime());
    	return ((float)$usec + (float)$sec);
    }
	
	public static function getmacno($data){//取机号
		$addrlength=gfun::getaddrlength($data);
		$macno=substr(gfun::strtohex($data),12,$addrlength*2);
		if($addrlength<5) $macno=hexdec($macno);
		return $macno;
	}
	public static function getprecom($data){//取命令前置固定字节
		$addrlength=gfun::getaddrlength($data);
		$command=substr(gfun::strtohex($data),0,($addrlength+6)*2);
		return $command;
	}
	public static function getchildchar($data,$start,$length){//取命令数据中子数组
		$addrlength=gfun::getaddrlength($data);
		return substr(gfun::strtohex($data),($addrlength+10+$start)*2,$length*2);
	}
	public static function getaddrlength($data){
		if(ord($data[5])==1||ord($data[5])==4||ord($data[5])==5||ord($data[5])==6||ord($data[5])==255)
			return 8;
		if(ord($data[5])==3)
			return 4;
		if(ord($data[5])==254)
			return 5;
		return 2;
	}
	public static function strtohex($string){//字符串转十六进制
		$hex=bin2hex($string);
		$hex=strtoupper($hex);
		return $hex;
	}
	public static function hextostr($hex){//十六进制转字符串
		$string=hex2bin($hex);
		return $string;
	}
	public static function getcrc16($Source){
		$crc = 0xA1EC;          // initial value
		$polynomial = 0x1021;   // 0001 0000 0010 0001  (0, 5, 12) 
		$tmp = "";
		$bytes = array();
		for ($i = 0; $i < strlen($Source) - 1; $i++)
		{
			if ($i % 2 == 0)
			{
				$tmp = substr($Source,$i, 2);
				$bytes[$i / 2] = hexdec($tmp);
			}
		}
		foreach ($bytes as $b)
		{
			for ($i = 0; $i < 8; $i++)
			{
				$bit = (($b >> (7 - $i) & 1) == 1);
				$c15 = (($crc >> 15 & 1) == 1);
				$crc <<= 1;
				if ($c15 ^ $bit) $crc ^= $polynomial;
			}
		}
		$crc &= 0xffff;
		$strDest = $crc;
		return strtoupper(substr("000".dechex($strDest),-4));
	}
	public static function gettime($itime=0){
		if(empty($itme)) $itme=time();
		$dt = date("Y-m-d H:i:s",$itime);
		$time = substr("0".dechex(substr($dt,0,4)),-4);
		$time.= substr("0".dechex(substr($dt,5,2)),-2);
		$time.= substr("0".dechex(substr($dt,8,2)),-2);
		$time.= substr("0".dechex(substr($dt,11,2)),-2);
		$time.= substr("0".dechex(substr($dt,14,2)),-2);
		$time.= substr("0".dechex(substr($dt,17,2)),-2);
		$time.=substr("0".dechex(date("N")),-2);
		return strtoupper($time);
	}
	public static function toasciistr($data){
		$ret="";
		for($i=0;$i<strlen($data);$i++){
			$ret.=substr("0".dechex(ord($data[$i])),-2);
		}
		return $ret;
	}
	public static function getweather($ip){//根据IP返回天气
		$url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
		$ip=json_decode(file_get_contents($url));	
		if((string)$ip->code=='1'){
		   return false;
	 	}
	 	$city = $ip->data->city;
		$city = str_split($city,strlen($city)-3);
		$city =$city[0];
		$cityUrl = "http://evenle.com/wei/20130921/city.php";
		$web=json_decode(file_get_contents($cityUrl));
		$arr=array();
		foreach($web as $k=>$w){
			if(is_object($w)) $arr[$k]=json_to_array($w); //判断类型是不是object
			else $arr[$k]=$w;
		}
		$url="http://www.weather.com.cn/data/cityinfo/".$arr[$city].".html";
		$weather=json_decode(file_get_contents($url));
		return $weather;
	}
	public static function safeEncoding($string, $outEncoding = 'UTF-8') {
		$encoding = "UTF-8";
		for ($i = 0; $i < strlen($string); $i++) {
			if (ord($string{$i}) < 128)
				continue;


			if ((ord($string{$i}) & 224) == 224) {
				$char = $string{++$i};
				if ((ord($char) & 128) == 128) {
					$char = $string{++$i};
					if ((ord($char) & 128) == 128) {
						 $encoding = "UTF-8";
						break;
					}
				}
			}

			if ((ord($string{$i}) & 192) == 192) {
				$char = $string{++$i};
				if ((ord($char) & 128) == 128) {
					$encoding = "GB2312";
					break;
				}
			}
		}

		if (strtoupper($encoding) == strtoupper($outEncoding)){
			return $string;
		} else {
			return iconv($encoding, $outEncoding, $string);
		}
	}
}
