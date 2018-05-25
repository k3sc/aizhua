<?php

class Model_User extends Model_Common {
	/* 用户全部信息 */
	public function getBaseInfo($uid) {
		$info=DI()->notorm->users
//				->select("id,user_nicename,avatar,avatar_thumb,sex,signature,coin,votes,consumption,votestotal,province,city,birthday,gifttotal")
				->select('*')
                ->where('id=?  and user_type="2"',$uid)
				->fetchOne();	 				
		$info['avatar']=$this->get_upload_path($info['avatar']);
		$info['avatar_thumb']=$this->get_upload_path($info['avatar_thumb']);						
		$info['level']=$this->getLevel($info['consumption']);
		$info['authorlevel']=$this->getAuthorLevel($info['gifttotal']);	
		$info['isvip']=$this->getUserVip($uid)>time() ? 1 : 0;	
		$info['lives']=$this->getLives($uid);
		$info['follows']=$this->getFollows($uid);
		$info['fans']=$this->getFans($uid);
        $info['user_setting'] = json_decode($info['user_setting'],true);


        //统计娃娃币账单
        $coin_bill_count = DI()->notorm->users_coinrecord->where("uid = $uid")->fetchAll();
        //统计我的娃娃个数
        $wawa_count = DI()->notorm->user_wawas->where("user_id = $uid and is_del = 0")->fetchAll();
        //统计用户礼品数
        $gift_count = DI()->notorm->users_gift->where("user_id = $uid")->fetchAll();
        //统计用户的未读消息数量
        $noread = DI()->notorm->notice->where("user_id = $uid and status = 0")->fetchAll();
        //登录奖励
        $loginbonus = DI()->notorm->loginbonus->where("id = 1")->select('coin')->fetchOne();

        $info['coin_bill_count'] = count($coin_bill_count);
        $info['wawa_count'] = count($wawa_count);
        $info['gift_count'] = count($gift_count);
        $info['noread'] = count($noread);
        $info['loginbonus'] = $loginbonus['coin'];

		return $info;
	}

	/* 判断昵称是否重复 */
	public function checkName($uid,$name){
		$isexist=DI()->notorm->users
					->select('id')
					->where('id!=? and user_nicename=?',$uid,$name)
					->fetchOne();
		if($isexist){
			return 0;
		}else{
			return 1;
		}
	}
	
	/* 修改信息 */
	public function userUpdate($uid,$fields){
		/* 清除缓存 */
		$this->delCache("userinfo_".$uid);

		return DI()->notorm->users
					->where('id=?',$uid)
					->update($fields);
	}

	/* 修改密码 */
	public function updatePass($uid,$oldpass,$pass){
		$userinfo=DI()->notorm->users
					->select("user_pass")
					->where('id=?',$uid)
					->fetchOne();
		$oldpass=$this->setPass($oldpass);							
		if($userinfo['user_pass']!=$oldpass){
			return 1003;
		}							
		$newpass=$this->setPass($pass);
		return DI()->notorm->users
					->where('id=?',$uid)
					->update( array( "user_pass"=>$newpass ) );
	}
	
	/* 我的钻石 */
	public function getBalance($uid){
		return DI()->notorm->users
				->select("coin")
				->where('id=?',$uid)
				->fetchOne();
	}
	
	/* 充值规则 */
	public function getChargeRules(){

		$rules= DI()->notorm->charge_rules
				->select('id,coin,coin_ios,money,money_ios,product_id,give,firstgive')
				->order('orderno asc')
				->fetchAll();

		return 	$rules;
	}
	/* 我的收益 */
	public function getProfit($uid){
		$info= DI()->notorm->users
				->select("votes,consumption")
				->where('id=?',$uid)
				->fetchOne();
		$level=$this->getLevel($info['consumption']);		
		//等级限制金额
		$limitcash=$this->getLevelSection($level);	
		
		$config=$this->getConfigPri();
		
		//提现比例
		$cash_rate=$config['cash_rate'];
		//剩余票数
		$votes=$info['votes'];
		//总可提现数
		$total=floor($votes/$cash_rate);
		
		$nowtime=time();
		//当天0点
		$today=date("Ymd",$nowtime);
		$today_start=strtotime($today)-1;
		//当天 23:59:59
		$today_end=strtotime("{$today} + 1 day");
		
		//已提现
		$hascash=DI()->notorm->users_cashrecord
					->where('uid=? and addtime>? and addtime<? and status!=2',$uid,$today_start,$today_end)
					->sum("money");
		if(!$hascash){
			$hascash=0;
		}		
		//今天可体现
		$todaycancash=$limitcash - $hascash;
		
		//今天能提
		if($todaycancash<$total){
			$todaycash=$todaycancash;
		}else{
			$todaycash=$total;
		}
		
		$rs=array(
			"votes"=>$votes,
			"todaycash"=>$todaycash,
			"total"=>$total,
		);
		return $rs;
	}	
	/* 提现  */
	public function setCash($uid){
		$isrz=DI()->notorm->users_auth
				->select("status")
				->where('uid=?',$uid)
				->fetchOne();
		if(!$isrz || $isrz['status']!=1){
			return 1003;
		}					
		$info= DI()->notorm->users
				->select("votes,consumption")
				->where('id=?',$uid)
				->fetchOne();
		$level=$this->getLevel($info['consumption']);		
		//等级限制金额
		$limitcash=$this->getLevelSection($level);	
		
		$config=$this->getConfigPri();
		
		//提现比例
		$cash_rate=$config['cash_rate'];
		/* 最低额度 */
		$cash_min=$config['cash_min'];
		//剩余票数
		$votes=$info['votes'];
		//总可提现数
		$total=floor($votes/$cash_rate);
		
		//已提现
		$nowtime=time();
		//当天0点
		$today=date("Ymd",$nowtime);
		$today_start=strtotime($today)-1;
		//当天 23:59:59
		$today_end=strtotime("{$today} + 1 day");
		
		$hascash =DI()->notorm->users_cashrecord
					->where('uid=? and addtime>? and addtime<? and status!=2',$uid,$today_start,$today_end)
					->sum("money");
		if(!$hascash){
			$hascash=0;
		}		
		//今天可体现
		$todaycancash=$limitcash - $hascash;
		
		//今天能提
		if($todaycancash<$total){
			$todaycash=$todaycancash;
		}else{
			$todaycash=$total;
		}
		
		if($todaycash==0){
			return 1001;
		}
		
		if($todaycash < $cash_min){
			return 1004;
		}
		
		$cashvotes=$todaycash*$cash_rate;
		
		$nowtime=time();
		
		$data=array(
			"uid"=>$uid,
			"money"=>$todaycash,
			"votes"=>$cashvotes,
			"orderno"=>$uid.'_'.$nowtime.rand(100,999),
			"status"=>0,
			"addtime"=>$nowtime,
			"uptime"=>$nowtime,
		);
		
		$rs=DI()->notorm->users_cashrecord->insert($data);
		if($rs){
			DI()->notorm->users
				->where('id = ?', $uid)
				->update(array('votes' => new NotORM_Literal("votes - {$cashvotes}")) );
		}else{
			return 1002;
		}				
		
		return $rs;
	}
	
	/* 关注 */
	public function setAttent($uid,$touid){
		$isexist=DI()->notorm->users_attention
					->select("*")
					->where('uid=? and touid=?',$uid,$touid)
					->fetchOne();
		if($isexist){
			DI()->notorm->users_attention
				->where('uid=? and touid=?',$uid,$touid)
				->delete();
			return 0;
		}else{
			DI()->notorm->users_black
				->where('uid=? and touid=?',$uid,$touid)
				->delete();
			DI()->notorm->users_attention
				->insert(array("uid"=>$uid,"touid"=>$touid));
			return 1;
		}			 
	}	
	
	/* 拉黑 */
	public function setBlack($uid,$touid){
		$isexist=DI()->notorm->users_black
					->select("*")
					->where('uid=? and touid=?',$uid,$touid)
					->fetchOne();
		if($isexist){
			DI()->notorm->users_black
				->where('uid=? and touid=?',$uid,$touid)
				->delete();
			return 0;
		}else{
			DI()->notorm->users_attention
				->where('uid=? and touid=?',$uid,$touid)
				->delete();
			DI()->notorm->users_black
				->insert(array("uid"=>$uid,"touid"=>$touid));

			return 1;
		}			 
	}
	
	/* 关注列表 */
	public function getFollowsList($uid,$touid,$p){
		$pnum=50;
		$start=($p-1)*$pnum;
		$touids=DI()->notorm->users_attention
					->select("touid")
					->where('uid=?',$touid)
					->limit($start,$pnum)
					->fetchAll();
		foreach($touids as $k=>$v){
			$touids[$k]=$this->getUserInfo($v['touid']);
			if($uid==$touid){
				$isattent=1;
			}else{
				$isattent=$this->isAttention($uid,$v['touid']);
			}
			$touids[$k]['isattention']=$isattent;
		}						
		return $touids;
	}
	
	/* 粉丝列表 */
	public function getFansList($uid,$touid,$p){
		$pnum=50;
		$start=($p-1)*$pnum;
		$touids=DI()->notorm->users_attention
					->select("uid")
					->where('touid=?',$touid)
					->limit($start,$pnum)
					->fetchAll();
		foreach($touids as $k=>$v){
			$touids[$k]=$this->getUserInfo($v['uid']);
			$touids[$k]['isattention']=$this->isAttention($uid,$v['uid']);
		}						
		return $touids;
	}	

	/* 黑名单列表 */
	public function getBlackList($uid,$touid,$p){
		$pnum=50;
		$start=($p-1)*$pnum;
		$touids=DI()->notorm->users_black
					->select("touid")
					->where('uid=?',$touid)
					->limit($start,$pnum)
					->fetchAll();
		foreach($touids as $k=>$v){
			$touids[$k]=$this->getUserInfo($v['touid']);
		}						
		return $touids;
	}
	
	/* 直播记录 */
	public function getLiverecord($touid,$p){
		$pnum=50;
		$start=($p-1)*$pnum;
		$record=DI()->notorm->users_liverecord
					->select("id,uid,nums,starttime,endtime,title,city")
					->where('uid=?',$touid)
					->order("id desc")
					->limit($start,$pnum)
					->fetchAll();
		foreach($record as $k=>$v){
			$record[$k]['datestarttime']=date("Y年m月d日 H:i",$v['starttime']);
			$record[$k]['dateendtime']=date("Y年m月d日 H:i",$v['endtime']);
		}						
		return $record;						
	}	
	
		/* 个人主页 */
	public function getUserHome($uid,$touid){
		$info=$this->getUserInfo($touid);				

		$info['follows']=$this->NumberFormat($this->getFollows($touid));
		$info['fans']=$this->NumberFormat($this->getFans($touid));
		$info['isattention']=(string)$this->isAttention($uid,$touid);
		$info['isblack']=(string)$this->isBlack($uid,$touid);
		$info['isblack2']=(string)$this->isBlack($touid,$uid);
		
		/* 贡献榜前三 */
		$rs=array();
		$rs=DI()->notorm->users_coinrecord
				->select("uid,sum(totalcoin) as total")
				->where('touid=?',$touid)
				->group("uid")
				->order("total desc")
				->limit(0,3)
				->fetchAll();
		foreach($rs as $k=>$v){
			$userinfo=$this->getUserInfo($v['uid']);
			$rs[$k]['avatar']=$userinfo['avatar'];
		}		
		$info['contribute']=$rs;	
		
		/* 是否直播 */
			$info['islive']='1';
		if($uid==$touid){
			$live['uid']='';
			$live['avatar']='';
			$live['avatar_thumb']='';
			$live['user_nicename']='';
			$live['title']='';
			$live['city']='';
			$live['stream']='';
			$live['pull']='';
			$info['islive']='0';
		}else{
			$live=DI()->notorm->users_live
					->select("uid,avatar,avatar_thumb,user_nicename,title,city,stream,pull")
					->where('uid=? and islive="1"',$touid)
					->fetchOne();
			if(!$live){
				$live['uid']='';
				$live['avatar']='';
				$live['avatar_thumb']='';
				$live['user_nicename']='';
				$live['title']='';
				$live['city']='';
				$live['stream']='';
				$live['pull']='';
				$info['islive']='0';
			}			
		}

		$info['liveinfo']=$live;	

		/* 直播记录 */
		$record=array();
		$record=DI()->notorm->users_liverecord
					->select("id,uid,nums,starttime,endtime,title,city")
					->where('uid=?',$touid)
					->order("id desc")
					->limit(0,20)
					->fetchAll();
		foreach($record as $k=>$v){
			$record[$k]['datestarttime']=date("Y年m月d日 H:i",$v['starttime']);
			$record[$k]['dateendtime']=date("Y年m月d日 H:i",$v['endtime']);
		}		
		$info['liverecord']=$record;	
		return $info;
	}
	
	/* 贡献榜 */
	public function getContributeList($touid,$p){
		
		$pnum=50;
		$start=($p-1)*$pnum;

		$rs=array();
		$rs=DI()->notorm->users_coinrecord
				->select("uid,sum(totalcoin) as total")
				->where('touid=?',$touid)
				->group("uid")
				->order("total desc")
				->limit($start,$pnum)
				->fetchAll();
				
		foreach($rs as $k=>$v){
			$rs[$k]['userinfo']=$this->getUserInfo($v['uid']);
		}		
		
		return $rs;
	}

	/* 购买VIP */
	public function buyVip($uid,$giftcount) {

		$userinfo=DI()->notorm->users
					->select('coin')
					->where('id = ?', $uid)
					->fetchOne();	

		$total= $giftcount == 12 ? 188800 : 18800 * $giftcount;
		 
		$addtime=time();
		$type='expend';
		$action='vip';
		if($userinfo['coin'] < $total){
			/* 余额不足 */
			return 1001;
		}		

		/* 更新用户余额 消费 */
		$isuid =DI()->notorm->users
				->where('id = ?', $uid)
				->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );


		$insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>0,"giftid"=>0,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>0,"addtime"=>$addtime );
		$isup=DI()->notorm->users_coinrecord->insert($insert);
					 
		$userinfo2 =DI()->notorm->users
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();	
			 
		$this->getLevel($userinfo2['consumption']);			
		
		/* 清除缓存 */
		$this->delCache("userinfo_".$uid); 
		
		return true;
	}			
}
