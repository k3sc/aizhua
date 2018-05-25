<?php

/* 前台 */
	/* redis链接 */
	function connectionRedis(){
		$REDIS_HOST= C('REDIS_HOST');
		$REDIS_AUTH= C('REDIS_AUTH');
		$redis = new \Redis();
		$redis -> pconnect($REDIS_HOST,6379);
		$redis -> auth($REDIS_AUTH);

		return $redis;
	}
	
	/* 设置缓存 */
	function setcache($key,$info){
		$config=getConfigPri();
		if($config['cache_switch']!=1){
			return 1;
		}
		$redis=connectionRedis();
		$redis->set($key,json_encode($info));
		$redis->setTimeout($key, $config['cache_time']); 
		$redis->close();
		
		return 1;
	}	
	/* 设置缓存 可自定义时间*/
	function setcaches($key,$info,$time){

		$redis=connectionRedis();
		$redis->set($key,json_encode($info));
		$redis->setTimeout($key, $time); 
		$redis->close();
		
		return 1;
	}
	/* 获取缓存 */
	function getcache($key){
		$config=getConfigPri();

		$redis=connectionRedis();
		$isexist=$redis->Get($key);
		if($config['cache_switch']!=1){
			$isexist=false;
		}
		$redis->close();
		
		return json_decode($isexist,true);
	}		
	/* 获取缓存 不判断后台设置 */
	function getcaches($key){
		$redis=connectionRedis();
		$isexist=$redis->Get($key);
		$redis->close();
		
		return json_decode($isexist,true);
	}
	/* 删除缓存 */
	function delcache($key){
		$redis=connectionRedis();
		$isexist=$redis->delete($key);
		$redis->close();
		
		return 1;
	}	

	/* 获取公共配置 */
	function getConfigPub() {
		$key='getConfigPub';
		$config=getcaches($key);
		$config=false;
		if(!$config){
			$config= M("config")->where("id='1'")->find();
			setcaches($key,$config,60);
		}
		return 	$config;
	}	
	
	/* 获取私密配置 */
	function getConfigPri() {
		$key='getConfigPri';
		$config=getcaches($key);
		$config=false;
		if(!$config){
			$config= M("config_private")->where("id='1'")->find();
			setcaches($key,$config,60);
		}
		return 	$config;
	}
	
	/* 获取等级 */
	
	function getLevel($experience){
		
		$levelid=1;
		$key='level';
		$level=getCache($key);
		if(!$level){
			$level= M("experlevel")->field("levelid,level_up")->order("level_up asc")->select();
			setCache($key,$level);			 
		}

		foreach($level as $k=>$v){
			if( $v['level_up']>=$experience){
				$levelid=$v['levelid'];
				break;
			}
		}
		return $levelid;
	}
	/*等级区间限额*/
	function getLevelSection($level)
	{
	 	$key='experlevel_limit';
		$experlevel_limit=getcaches($key);
		if(!$experlevel_limit)
		{
			$experlevel_limit=M("experlevel_limit")->field("withdraw,level_up")->order("level_up asc")->select();
			setcaches($key,$experlevel_limit,60);
		} 
		foreach($experlevel_limit as $k=>$v)
		{
			if($v['level_up']>=$level){
				$withdraw=$v['withdraw'];
				break;
			}
		}	
		return $withdraw;		 
	}
	/* 判断是否关注 */
	function isAttention($uid,$touid) {
     $id=M("users_attention")->where("uid='$uid' and touid='$touid'")->find();
		if($id){
	     return  1;
		}else{
			return  0;
		}			 
				
   }
	/*判断是否拉黑*/ 
	function isBlack($uid,$touid)
	{
		$isexist=M("users_black")->where("uid=".$uid." and touid=".$touid)->find();
		if($isexist){
			return 1;
		}else{
			return 0;					
		}
	}
	/* 关注人数 */
	function getFollownums($uid) 
	{
    return M("users_attention")->where("uid='{$uid}' ")->count();
   }
	/* 粉丝人数 */
	function getFansnums($uid) 
	{
    return M("users_attention")->where(" touid='{$uid}'")->count();
  } 
		/* 用户基本信息 */
    function getUserInfo($uid) {
        $info= M("users")->field("id,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,birthday,issuper")->where("id='{$uid}'")->find();
		if($info){
			$info['level']=getLevel($info['consumption']);
		}
				
		return 	$info;		
    }		 
	/*获取收到礼物数量(tsd) 以及送出的礼物数量（tsc） */
	function getgif($uid)
	{
		
    $live=M("users_coinrecord");
		$count=$live->query('select sum(case when touid='.$uid.' then 1 else 0 end) as tsd,sum(case when uid='.$uid.' then 1 else 0 end) as tsc from cmf_users_coinrecord');
		return 	$count;		
	}
	/* 用户信息 含有私密信息 */
   function getUserPrivateInfo($uid) {
        $info= M("users")->field('id,user_login,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,coin,votes,token,birthday,issuper')->where("id='{$uid}'")->find();
		if($info){
			$info['level']=getLevel($info['consumption']);
		}
		return 	$info;		
    }			
		
		/* 用户信息 含有私密信息 */
    function getUserToken($uid) {
        $info= M("users")->field('token')->where("id='{$uid}'")->find();

		return 	$info['token'];		
    }				
	/* 房间管理员 */
	function getIsAdmin($uid,$showid){
		if($uid==$showid){		
			return 50;
		}
		$isuper=isSuper($uid);
		if($isuper){
			return 60;
		}
		$id=M("users_livemanager")->where("uid = '$uid' and touid = '$showid'")->getField("id");

		if($id)	{
			return 40;					
		}
		return 30;		
	}
	/*前台个人中心判断是否登录*/
	function LogIn()
	{
		$uid=session("uid");
		if($uid<=0)
		{
			$url=$_SERVER['HTTP_HOST'];
			header("Location:http://".$url); 
			exit;
		}
	}
	/* 判断账号是否超管 */
	function isSuper($uid){
		$isexist=M("users_super")->where("uid='{$uid}'")->find();
		if($isexist){
			return 1;
		}			
		return 0;
	}	
		/* 多维数组排序 */
 	function array_column2($input, $columnKey, $indexKey = NULL){
		$columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
		$indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
		$indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
		$result = array();
 
		foreach ((array)$input AS $key => $row){ 
			if ($columnKeyIsNumber){
				$tmp = array_slice($row, $columnKey, 1);
				$tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
			}else{
				$tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
			}
			if (!$indexKeyIsNull){
				if ($indexKeyIsNumber){
					$key = array_slice($row, $indexKey, 1);
					$key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
					$key = is_null($key) ? 0 : $key;
				}else{
					$key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
				}
			}
			$result[$key] = $tmp;
		}
		return $result;
	}

	/* 时间差计算 */
	function datetime($time){
		$cha=time()-$time;
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		
		if($cha<60){
			 return $cha.'秒前';
		}else if($iz<60){
			return $iz.'分钟前';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟前';
		}else if($dz<30){
			return $dz.'天前';
		}else{
			return date("Y-m-d",$time);
		}
	}
	/*判断该用户是否已经认证*/
	function auth($uid)
	{
		$users_auth=M("users_auth")->field('uid,status')->where("uid=".$uid)->find();
		if($users_auth)
		{
			return $users_auth["status"];
		}
		else
		{
			return 3;
		}
	}
	/* 时长 */
	function datelong($cha){
		$iz=floor($cha/60);
		$hz=floor($iz/60);
		$dz=floor($hz/24);
		/* 秒 */
		$s=$cha%60;
		/* 分 */
		$i=floor($iz%60);
		/* 时 */
		$h=floor($hz/24);
		/* 天 */
		if($s<10){
			$s='0'.$s;
		}
		if($i<10){
			$i='0'.$i;
		}

		if($h<10){
			$h='0'.$h;
		}
		
		if($hz<10){
			$hz='0'.$hz;
		}
		
		return $hz.':'.$i.':'.$s;
		if($cha<60){
			 return '00:00:'.$s;
		}else if($iz<60){
			return $iz.'分钟'.$s.'秒';
		}else if($hz<24){
			return $hz.'小时'.$i.'分钟';
		}else if($dz<30){
			return $dz.'天';
		}else{
			return date("Y-m-d",$time);
		}
	}
	/* 获取指定长度的随机字符串 */
	function random($length = 6 , $numeric = 0) {
		PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
		if($numeric) {
			$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
		} else {
			$hash = '';
			$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
			$max = strlen($chars) - 1;
			for($i = 0; $i < $length; $i++) {
				$hash .= $chars[mt_rand(0, $max)];
			}
		}
		return $hash;
	}

	/* 获取扩展配置 */
	function getConfig(){	
		return M("config")->where("id='1'")->find();
	}		
	/* 去除emoji表情 */
	function filterEmoji($str){
		$str = preg_replace_callback(
			'/./u',
			function (array $match) {
				return strlen($match[0]) >= 4 ? '' : $match[0];
			},
			$str);
		return $str;
	}
