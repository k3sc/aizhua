<?php

class Model_Common extends PhalApi_Model_NotORM {
	/* Redis链接 */
	public function connectionRedis(){
		$REDIS_HOST= DI()->config->get('app.REDIS_HOST');
		$REDIS_AUTH= DI()->config->get('app.REDIS_AUTH');
		$redis = new Redis();
		$redis -> pconnect($REDIS_HOST,6379);
		$redis -> auth($REDIS_AUTH);

		return $redis;
	}
	/* 设置缓存 */
	public function setcache($key,$info){
		$config=$this->getConfigPri();
		if($config['cache_switch']!=1){
			return 1;
		}

		DI()->redis->set($key,json_encode($info));
		DI()->redis->setTimeout($key, $config['cache_time']); 

		return 1;
	}	
	/* 设置缓存 可自定义时间*/
	public function setcaches($key,$info,$time){
		DI()->redis->set($key,json_encode($info));
		DI()->redis->setTimeout($key, $time); 
		return 1;
	}
	/* 获取缓存 */
	public function getcache($key){
		$config=$this->getConfigPri();

		if($config['cache_switch']!=1){
			$isexist=false;
		}else{
			$isexist=DI()->redis->Get($key);
		}

		return json_decode($isexist,true);
	}		
	/* 获取缓存 不判断后台设置 */
	public function getcaches($key){

		$isexist=DI()->redis->Get($key);
		
		return json_decode($isexist,true);
	}
	/* 删除缓存 */
	public function delcache($key){
		$isexist=DI()->redis->delete($key);
		return 1;
	}	
	/* 同系统函数 array_column   php版本低于5.5.0 时用  */
	public function array_column2($input, $columnKey, $indexKey = NULL){
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
	
	/* 密码检查 */
	public function passcheck($user_pass) {
		$num = preg_match("/^[a-zA-Z]+$/",$user_pass);
		$word = preg_match("/^[0-9]+$/",$user_pass);
		$check = preg_match("/^[a-zA-Z0-9]{6,12}$/",$user_pass);
		if($num || $word ){
			return 2;
		}else if(!$check){
			return 0;
		}		
		return 1;
	}
	
	/* 密码加密 */
	public function setPass($pass){
		$authcode='rCt52pF2cnnKNB3Hkp';
		$pass="###".md5(md5($authcode.$pass));
		return $pass;
	}	
	
	/* 公共配置 */
	public function getConfigPub() {
		$key='getConfigPub';
		$config=$this->getcaches($key);
		$config=false;
		if(!$config){
			$config= DI()->notorm->config
					->select('*')
					->where(" id ='1'")
					->fetchOne();
			$this->setcaches($key,$config,60);
		}
        
		return 	$config;
	}		
	
	/* 私密配置 */
	public function getConfigPri() {
		$key='getConfigPri';
		$config=$this->getcaches($key);
		$config=false;
		if(!$config){
			$config= DI()->notorm->config_private
					->select('*')
					->where(" id ='1'")
					->fetchOne();
			$this->setcaches($key,$config,60);
		}
		return 	$config;
	}		
	
	/**
	 * 返回带协议的域名
	 */
	public function get_host(){
		//$host=$_SERVER["HTTP_HOST"];
	//	$protocol=$this->is_ssl()?"https://":"http://";
		//return $protocol.$host;
		$config=$this->getConfigPub();
		return $config['site'];
	}	
	
	/**
	 * 转化数据库保存的文件路径，为可以访问的url
	 */
	public function get_upload_path($file){
		if(strpos($file,"http")===0){
			return $file;
		}else if(strpos($file,"/")===0){
			$filepath= $this->get_host().$file;
			return $filepath;
		}else{
			$space_host= DI()->config->get('app.Qiniu.space_host');
			$filepath=$space_host."/".$file;
			return $filepath;
		}
	}
	
	/* 判断是否关注 */
	public function isAttention($uid,$touid) {
		$isexist=DI()->notorm->users_attention
					->select("*")
					->where('uid=? and touid=?',$uid,$touid)
					->fetchOne();
		if($isexist){
			return  1;
		}else{
			return  0;
		}			 
	}
	/* 是否黑名单 */
	public function isBlack($uid,$touid) {	
		$isexist=DI()->notorm->users_black
				->select("*")
				->where('uid=? and touid=?',$uid,$touid)
				->fetchOne();
		if($isexist){
			return 1;
		}else{
			return 0;					
		}
	}	
	
	/* 判断权限 */
	public function isAdmin($uid,$liveuid) {
		if($uid==$liveuid){
			return 50;
		}
		$isuper=$this->isSuper($uid);
		if($isuper){
			return 60;
		}
		$isexist=DI()->notorm->users_livemanager
					->select("*")
					->where('uid=? and liveuid=?',$uid,$liveuid)
					->fetchOne();
		if($isexist){
			return  40;
		}
		
		return  30;
			
	}	
	/* 判断账号是否超管 */
	public function isSuper($uid){
		$isexist=DI()->notorm->users_super
					->select("*")
					->where('uid=?',$uid)
					->fetchOne();
		if($isexist){
			return 1;
		}			
		return 0;
	}	
	/* 判断token */
	public function checkToken($uid,$token) {
		$userinfo=$this->getCache("token_".$uid);
		if(!$userinfo){
			$userinfo=DI()->notorm->users
						->select('token,expiretime')
						//->where('id = ? and user_type="2"', $uid)
						->where('id = ?', $uid)
						->fetchOne();	
			$this->setCache("token_".$uid,$userinfo);								
		}

		if($userinfo['token']!=$token){//} || $userinfo['expiretime']<time()){
			return 101;				
		}else{
			return 	0;				
		} 		
	}	
	
	/* 用户基本信息 */
	public function getUserInfo($uid) {
		$info=$this->getCache("userinfo_".$uid);
		if(!$info){
			$info=DI()->notorm->users
					->select('id,user_nicename,avatar,coin,avatar_thumb,sex,signature,consumption,votestotal,province,city,birthday,issuper,gifttotal')
					->where('id=? and user_type="2"',$uid)
					->fetchOne();	
			if($info){
				$info['level']=$this->getLevel($info['consumption']);
				$info['authorlevel']=$this->getAuthorLevel($info['gifttotal']);
				$info['isvip']=$this->getUserVip($uid)>time() ? 1 : 0;
				$info['avatar']=$this->get_upload_path($info['avatar']);
				$info['avatar_thumb']=$this->get_upload_path($info['avatar_thumb']);
				$this->setCache("userinfo_".$uid,$info);						
			}					
		}
		return 	$info;		
	}
	
	/* 计算守护 */
	public function getUserGuard($uid, $liveuid) {
		$info=$this->getCache("userguard_".$uid."_".$liveuid);
		if(!$info){
			$info=DI()->notorm->users_coinrecord
					->select('*')
					->where("action='sendguard' and uid= ? and touid= ?",$uid, $liveuid)
					->fetchOne();	
			if($info){
				$info = $info['addtime'] + $info['giftcount'] * 30 * 86400;		
				$this->setCache("userguard_".$uid."_".$liveuid,$info);						
			}else{
				$info = 0;
			}
		}
		return 	$info;		
	}
	
	/* 计算守护 */
	public function getUserVip($uid) {
		$info=$this->getCache("uservip_".$uid);
		if(!$info){
			$info=DI()->notorm->users_coinrecord
					->select('*')
					->where("action='vip' and uid= ? and touid=0",$uid)
					->fetchOne();	
			if($info){
				$info = $info['addtime'] + $info['giftcount'] * 30 * 86400;		
				$this->setCache("uservip_".$uid,$info);						
			}else{
				$info = 0;
			}
		}
		return 	$info;		
	}
	
	/* 会员等级 */
	public function getLevel($experience){
		$levelid=1;
		$key='level';
		$level=$this->getCache($key);
		if(!$level){
			$level=DI()->notorm->experlevel
					->select("levelid,level_up")
					->order("level_up asc")
					->fetchAll();
			$this->setCache($key,$level);			 
		}

		foreach($level as $k=>$v){
			if( $v['level_up']>=$experience){
				$levelid=$v['levelid'];
				break;
			}
		}
		return $levelid;
	}
	
	/* 主播等级 */
	public function getAuthorLevel($experience = false){
		$levelid=1;
		$key='authorlevel';
		$level=$this->getCache($key);
		if(!$level){
			$level=DI()->notorm->authorlevel
					->select("levelid,level_up")
					->order("level_up asc")
					->fetchAll();
			$this->setCache($key,$level);			 
		}
		if($experience === false)return $level;
		foreach($level as $k=>$v){
			if( $v['level_up']>=$experience){
				$levelid=$v['levelid'];
				break;
			}
		}
		return $levelid;
	}
	
	/* 等级区间限额 */
	public function getLevelSection($level){
		$key='experlevel_limit';
		$experlevel_limit=$this->getCache($key);
		if(!$experlevel_limit){
			$experlevel_limit=DI()->notorm->experlevel_limit
						 ->select("withdraw,level_up")
						 ->order("level_up asc")
						 ->fetchAll();
			$this->setCache($key,$experlevel_limit);			 
		}
		
		foreach($experlevel_limit as $k=>$v){
			if($v['level_up']>=$level){
				$withdraw=$v['withdraw'];
				break;
			}
			
		}
		return $withdraw;		 
	}	
	/* 统计 直播 */
	public function getLives($uid) {
		/* 直播中 */
		$count1=DI()->notorm->users_live
				->where('uid=? and islive="1"',$uid)
				->count();
		/* 回放 */
		$count2=DI()->notorm->users_liverecord
					->where('uid=? ',$uid)
					->count();
		return 	$count1+$count2;
	}		
	
	/* 统计 关注 */
	public function getFollows($uid) {
		$count=DI()->notorm->users_attention
				->where('uid=? ',$uid)
				->count();
		return 	$count;
	}			
	
	/* 统计 粉丝 */
	public function getFans($uid) {
		$count=DI()->notorm->users_attention
				->where('touid=? ',$uid)
				->count();
		return 	$count;
	}		
	/**
	*  @desc 根据两点间的经纬度计算距离
	*  @param float $lat 纬度值
	*  @param float $lng 经度值
	*/
	public function getDistance($lat1, $lng1, $lat2, $lng2){
		$earthRadius = 6371000; //近似地球半径 单位 米
		 /*
		   Convert these degrees to radians
		   to work with the formula
		 */

		$lat1 = ($lat1 * pi() ) / 180;
		$lng1 = ($lng1 * pi() ) / 180;

		$lat2 = ($lat2 * pi() ) / 180;
		$lng2 = ($lng2 * pi() ) / 180;


		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculatedDistance = $earthRadius * $stepTwo;
		
		$distance=round($calculatedDistance)/1000;

		return $distance.'km';
	}
	/* 判断账号是否禁用 */
	public function isBan($uid){
		$status=DI()->notorm->users
					->select("user_status")
					->where('id=?',$uid)
					->fetchOne();
		if(!$status || $status['user_status']==0){
			return 0;
		}
		return 1;
	}
	/* 是否认证 */
	public function isAuth($uid){
		$status=DI()->notorm->users_auth
					->select("status")
					->where('uid=?',$uid)
					->fetchOne();
					//print_r($status);die;
		if(!$status || $status['status']!=1){
			return 0;
		}
		return 1;
	}
	/* 过滤字符 */
	public function filterField($field){
		$configpri=$this->getConfigPri();
		
		$sensitive_field=$configpri['sensitive_field'];
		
		$sensitive=explode(",",$sensitive_field);
		$replace=array();
		$preg=array();
		foreach($sensitive as $k=>$v){
			if($v){
				$re='';
				$num=mb_strlen($v);
				for($i=0;$i<$num;$i++){
					$re.='*';
				}
				$replace[$k]=$re;
				$preg[$k]='/'.$v.'/';
			}else{
				unset($sensitive[$k]);
			}
		}
		
		return preg_replace($preg,$replace,$field);
	}
	/* 时间差计算 */
	public function datetime($time){
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
	/* 时长格式化 */
	public function getSeconds($cha){		 
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
			return $cha.'秒';
		}else if($iz<60){
			return $iz.'分'.$s.'秒';
		}else if($hz<24){
			return $hz.'小时'.$i.'分'.$s.'秒';
		}else if($dz<30){
			return $dz.'天'.$h.'小时'.$i.'分'.$s.'秒';
		}
	}	
	
	/* 数字格式化 */
	public function NumberFormat($num){
		if($num<10000){

		}else if($num<1000000){
			$num=round($num/10000,2).'万';
		}else if($num<100000000){
			$num=round($num/10000,1).'万';
		}else if($num<10000000000){
			$num=round($num/100000000,2).'亿';
		}else{
			$num=round($num/100000000,1).'亿';
		}
		return $num;
	}

	/**
	*  @desc 登录奖励
	*/
	public function LoginBonus($uid,$token){
		$rs=array(
			'bonus_switch'=>'0',
			'bonus_day'=>'0',
			'bonus_list'=>array(),
		);
		$configpri=$this->getConfigPri();
		if(!$configpri['bonus_switch']){
			return $rs;
		}
		$rs['bonus_switch']=$configpri['bonus_switch'];
		$iftoken=$this->checkToken($uid,$token);
		if($iftoken){
			return $iftoken;
		}
		
		/* 获取登录设置 */
		$list=DI()->notorm->loginbonus
					->select("day,coin")
					->fetchAll();
		$rs['bonus_list']=$list;
		$bonus_coin=array();
		foreach($list as $k=>$v){
			$bonus_coin[$v['day']]=$v['coin'];
		}
		
		/* 登录奖励 */
		$userinfo=DI()->notorm->users
					->select("bonus_day,bonus_time")
					->where('id=?',$uid)
					->fetchOne();
		$nowtime=time();
		if($nowtime>$userinfo['bonus_time']){
			//更新
			$bonus_time=strtotime(date("Ymd",$nowtime))+60*60*24;
			$bonus_day=$userinfo['bonus_day'];
			if($bonus_day>6){
				$bonus_day=0;
			}
			$bonus_day++;
			
			$rs['bonus_day']=$bonus_day;
			$coin=$bonus_coin[$bonus_day];
			DI()->notorm->users
				->where('id=?',$uid)
				->update(array("bonus_time"=>$bonus_time,"bonus_day"=>$bonus_day,"coin"=>new NotORM_Literal("coin + {$coin}") ));
			/* 记录 */
			$insert=array("type"=>'income',"action"=>'loginbonus',"uid"=>$uid,"touid"=>$uid,"giftid"=>$bonus_day,"giftcount"=>'0',"totalcoin"=>$coin,"showid"=>'0',"addtime"=>$nowtime );
			$isup=DI()->notorm->users_coinrecord->insert($insert);
		}
		
		return $rs;
		
	}


    /**
     * 生成邀请码
     * @return int 邀请码
     */
    public function get_code(){
        $str  = '0123456789';
        $rand = mt_rand(10000000,99999999);
        $len  = 6;
        $code = '';
        for ($i = 0;$i < $len;$i++){
            $code .= $str{mt_rand(0,strlen($str)-1)};
        }
        $code   .= $rand;
//        $md5code = md5($code);
        $final_code = '';
        for ($i = 0;$i < $len;$i++){
            $start = mt_rand(0,strlen($code)-1);
            $final_code .= substr($code,$start,1);
        }
        $code_true = DI()->notorm->users->where('user_activation_key=?',$final_code)->fetchOne();
        if( $code_true['user_activation_key'] ){
            $this->get_code();
        }else{
            return $final_code;
        }
    }

    public function get_code1(){
        $str  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = mt_rand(10000000,99999999);
        $len  = 6;
        $code = '';
        for ($i = 0;$i < $len;$i++){
            $code .= $str{mt_rand(0,strlen($str)-1)};
        }
        $code   .= $rand;
        $md5code = md5($code);
        $final_code = '';
        for ($i = 0;$i < $len;$i++){
            $start = mt_rand(0,strlen($md5code)-1);
            $final_code .= substr($md5code,$start,1);
        }
        $code_true = DI()->notorm->users->where('user_activation_key=?',$final_code)->fetchOne();
        if( $code_true['user_activation_key'] ){
            $this->get_code();
        }else{
            return $final_code;
        }
    }

}
