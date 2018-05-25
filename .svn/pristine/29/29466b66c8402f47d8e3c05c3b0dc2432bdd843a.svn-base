<?php

class Model_Live extends Model_Common {
	/* 创建房间 */
	public function createRoom($uid,$data) {
		$isexist=DI()->notorm->users_live
					->select("uid")
					->where('uid=?',$uid)
					->fetchOne();
		if($isexist){
			/* 更新 */
			$rs=DI()->notorm->users_live->where('uid = ?', $uid)->update($data);
		}else{
			/* 加入 */
			$rs=DI()->notorm->users_live->insert($data);
		}
		if(!$rs){
			return $rs;
		}
		return 1;
	}
	
	/* 主播粉丝 */
    public function getFansIds($touid) {
		$fansids=DI()->notorm->users_attention
					->select("uid")
					->where('touid=?',$touid)
					->fetchAll();
        return $fansids;
    }	
	
	/* 修改直播状态 */
	public function changeLive($uid,$stream,$status){

		if($status==1){
			return DI()->notorm->users_live
					->where('uid=? and stream=?',$uid,$stream)
					->update(array("islive"=>1));
		}else{
			$this->stopRoom($uid,$stream);
			return 1;
		}
	}
	
	/* 关播 */
	public function stopRoom($uid,$stream) {

		$info=DI()->notorm->users_live
				->select("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val")
				->where('uid=? and stream=? and islive="1"',$uid,$stream)
				->fetchOne();
		if($info){
			DI()->notorm->users_live
				->where('uid=?',$uid)
				->delete();
			$nowtime=time();
			$info['endtime']=$nowtime;
			$stream2=explode('_',$stream);
			$starttime=$stream2[1];
			$votes=DI()->notorm->users_coinrecord
				->where('touid=? and showid=?',$uid,$starttime)
				->sum('totalcoin');
			$info['votes']=0;
			if($votes){
				$info['votes']=$votes;
			}	
			$nums=DI()->redis->hget("livenums",$stream);
			DI()->redis->hdel("livenums",$stream);
			DI()->redis->hDel("livelist",$uid);
			DI()->redis->delete($uid.'_zombie');
			DI()->redis->delete($uid.'_zombie_uid');
			DI()->redis->delete('attention_'.$uid);
			DI()->redis->delete('userlist_'.$stream);
			$info['nums']=$nums;			
			$result=DI()->notorm->users_liverecord->insert($info);	
		}					
		return 1;
	}
	/* 关播信息 */
	public function stopInfo($stream){
		
		$rs=array(
			'nums'=>0,
			'length'=>0,
			'votes'=>0,
		);
		
		$stream2=explode('_',$stream);
		$liveuid=$stream2[0];
		$starttime=$stream2[1];
		$liveinfo=DI()->notorm->users_liverecord
					->select("starttime,endtime,nums,votes")
					->where('uid=? and starttime=?',$liveuid,$starttime)
					->fetchOne();
		if($liveinfo){
			$rs['length']=$this->getSeconds($liveinfo['endtime'] - $liveinfo['starttime']);
			$rs['nums']=$liveinfo['nums'];
		}
		if($liveinfo['votes']){
			$rs['votes']=$liveinfo['votes'];
		}
		return $rs;
	}
	
	/* 直播状态 */
	public function checkLive($uid,$liveuid,$stream){
		$islive=DI()->notorm->users_live
					->select("islive,type,type_val,starttime")
					->where('uid=? and stream=?',$liveuid,$stream)
					->fetchOne();
					
		if(!$islive || $islive['islive']==0){
			return 1005;
		}
		$rs['type']=$islive['type'];
		$rs['type_msg']='';
		
		if($islive['type']==1){
			$rs['type_msg']=md5($islive['type_val']);
		}else if($islive['type']==2){
			$rs['type_msg']='本房间为收费房间，需支付'.$islive['type_val'].'钻石';
			$isexist=DI()->notorm->users_coinrecord
						->select('id')
						->where('uid=? and touid=? and showid=? and action="roomcharge" and type="expend"',$uid,$liveuid,$islive['starttime'])
						->fetchOne();
			if($isexist){
				$rs['type']='0';
				$rs['type_msg']='';
			}
		}else if($islive['type']==3){
			$rs['type_msg']='本房间为计时房间，每分钟支付需支付'.$islive['type_val'].'钻石';
		}
		
		return $rs;
		
	}
	/* 房间扣费 */
	public function roomCharge($uid,$token,$liveuid,$stream){
		$islive=DI()->notorm->users_live
					->select("islive,type,type_val,starttime")
					->where('uid=? and stream=?',$liveuid,$stream)
					->fetchOne();
		if(!$islive || $islive['islive']==0){
			return 1005;
		}
		
		if($islive['type']==0 || $islive['type']==1 ){
			return 1006;
		}
		
		$userinfo=DI()->notorm->users
					->select("token,expiretime,coin")
					->where('id=?',$uid)
					->fetchOne();
		if($userinfo['token']!=$token || $userinfo['expiretime']<time()){
			return 101;				
		}
		
		$total=$islive['type_val'];
		if($total<=0){
			return 1007;
		}
		if($userinfo['coin'] < $total){
			return 1008;
		}
		$action='roomcharge';
		if($islive['type']==2){
			$action='roomcharge';
		}
		
		$giftid=0;
		$giftcount=0;
		$showid=$islive['starttime'];
		$addtime=time();
		/* 更新用户余额 消费 */
		DI()->notorm->users
				->where('id = ?', $uid)
				->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}")) );

		/* 更新直播 映票 累计映票 */
		DI()->notorm->users
				->where('id = ?', $liveuid)
				->update( array('votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));

		/* 更新直播 映票 累计映票 */
		DI()->notorm->users_coinrecord
				->insert(array("type"=>'expend',"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));	
				
		$userinfo2=DI()->notorm->users
					->select('coin')
					->where('id = ?', $uid)
					->fetchOne();	
		$rs['coin']=$userinfo2['coin'];
		return $rs;
		
	}
	
	/* 判断是否僵尸粉 */
	public function isZombie($uid) {
        $userinfo=DI()->notorm->users
					->select("iszombie")
					->where("id='{$uid}'")
					->fetchOne();
		
		return $userinfo['iszombie'];				
    }
	
	/* 僵尸粉 */
    public function getZombie($stream,$where) {
		$ids= DI()->notorm->users_zombie
            ->select('uid')
            ->where("uid not in ({$where})")
			->limit(0,10)
            ->fetchAll();	

		$info=array();

		if($ids){
			$ids2=$this->array_column2($ids,'uid');
			$ids=implode(",",$ids2);
			
			$stream2=explode('_',$stream);
			$showid=$stream2[1];

			$info= DI()->notorm->users
				->select('id,user_nicename,avatar,sex,consumption,city')
				->where("id in ({$ids}) ")
				->fetchAll();	
			foreach( $info as $k=>$v){
				$level=$this->getLevel($v['consumption']);						
				$v['level']=$level;						
				$info[$k]['level']=$level;					
				$v['isguard']=0;						
				$info[$k]['isguard']=0;				
				$v['isvip']=0;						
				$info[$k]['isvip']=0;				
				$sign = md5($showid.'_'.$v['id']);		
				DI()->redis -> hSet('userlist_'.$stream,$sign,json_encode($v));					
			}		
				 
			$num=count($info);
			
			

			DI()->redis->hIncrBy("livenums",$stream,$num);
	
		}
						
		return 	$info;		
    }		

	
	/* 弹窗 */
	public function getPop($touid){
		$info=$this->getUserInfo($touid);
		if(!$info){
			return $info;
		}
		$info['follows']=$this->getFollows($touid);
		$info['fans']=$this->getFans($touid);
		
		$info['consumption']=$this->NumberFormat($info['consumption']);
		$info['votestotal']=$this->NumberFormat($info['votestotal']);
		$info['follows']=$this->NumberFormat($info['follows']);
		$info['fans']=$this->NumberFormat($info['fans']);
		unset($info['province']);
		unset($info['birthday']);
		unset($info['issuper']);
		return $info;
	}
	
	/* 礼物列表 */
	public function getGiftList(){

		$rs=DI()->notorm->gift
			->select("id,type,giftname,needcoin,gifticon")
			->order("orderno asc")
			->fetchAll();
		foreach($rs as $k=>$v){
			$rs[$k]['gifticon']=$this->get_upload_path($v['gifticon']);
		}	

		return $rs;
	}
	
	/* 赠送礼物 */
	public function sendGift($uid,$liveuid,$stream,$giftid,$giftcount) {

		$userinfo=DI()->notorm->users
					->select('coin')
					->where('id = ?', $uid)
					->fetchOne();	

			/* 礼物信息 */
		$giftinfo=DI()->notorm->gift
					->select("type,giftname,gifticon,needcoin")
					->where('id=?',$giftid)
					->fetchOne();
		if(!$giftinfo){
			/* 礼物信息不存在 */
			return 1002;
		}
		
		//守护礼物
		if($giftinfo['type'] == 2){
			$guard_uid = $this->getUserGuard($uid,$liveuid);
			if($guard_uid<time()){
				return 1003;
			}
		}
				
		$total= $giftinfo['needcoin']*$giftcount;
		 
		$addtime=time();
		$type='expend';
		$action='sendgift';
		if($userinfo['coin'] < $total){
			/* 余额不足 */
			return 1001;
		}		

		/* 更新用户余额 消费 */
		$isuid =DI()->notorm->users
				->where('id = ?', $uid)
				->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );

		/* 更新直播 魅力值 累计魅力值 */
		$istouid =DI()->notorm->users
					->where('id = ?', $liveuid)
					->update( array('giftcount' => new NotORM_Literal("giftcount + 1"),'gifttotal' => new NotORM_Literal("gifttotal + {$giftcount}"),'votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
		
		$stream2=explode('_',$stream);
		$showid=$stream2[1];

		/* 写入记录 或更新 */
		/* $unique=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"showid"=>$showid);
		$insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
		$update= array('giftcount' => new NotORM_Literal("giftcount + {$giftcount}"),'totalcoin' => new NotORM_Literal("totalcoin + {$total}"));

		$isexit=DI()->notorm->users_coinrecord
				->select("id")
				->where($unique)
				->fetchOne();
		if($isexit){
			$isup=DI()->notorm->users_coinrecord->where('id=?',$isexit['id'])->update($update);
		}else{
			$isup=DI()->notorm->users_coinrecord->insert($insert);
		}	 */		
		$insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
		$isup=DI()->notorm->users_coinrecord->insert($insert);

		$userinfo2 =DI()->notorm->users
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();	
			 
		$level=$this->getLevel($userinfo2['consumption']);			
		
		/* 清除缓存 */
		$this->delCache("userinfo_".$uid); 
		$this->delCache("userinfo_".$liveuid); 
	
		$votestotal=$this->getVotes($liveuid);
		
		$gifttoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));
		
		$result=array("uid"=>$uid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"giftname"=>$giftinfo['giftname'],"gifticon"=>$giftinfo['gifticon'],"level"=>$level,"coin"=>$userinfo2['coin'],"votestotal"=>$votestotal,"gifttoken"=>$gifttoken);
					
		return $result;
	}		
	
	/* 发送弹幕 */
	public function sendBarrage($uid,$liveuid,$stream,$giftid,$giftcount,$content) {

		$userinfo=DI()->notorm->users
					->select('coin')
					->where('id = ?', $uid)
					->fetchOne();	
		$configpri=$this->getConfigPri();
					 
		$giftinfo=array(
			"giftname"=>'弹幕',
			"gifticon"=>'',
			"needcoin"=>$configpri['barrage_fee'],
		);		
		
		$total= $giftinfo['needcoin']*$giftcount;
		 
		$addtime=time();
		$type='expend';
		$action='sendbarrage';
		if($userinfo['coin'] < $total){
			/* 余额不足 */
			return 1001;
		}		

		/* 更新用户余额 消费 */
		$isuid =DI()->notorm->users
				->where('id = ?', $uid)
				->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );

		/* 更新直播 魅力值 累计魅力值 */
		$istouid =DI()->notorm->users
				->where('id = ?', $liveuid)
				->update( array('votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
				
		$stream2=explode('_',$stream);
		$showid=$stream2[1];

		/* 写入记录 或更新 */
		/* $unique=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"showid"=>$showid);
		$insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
		$update= array('giftcount' => new NotORM_Literal("giftcount + {$giftcount}"),'totalcoin' => new NotORM_Literal("totalcoin + {$total}"));

		
		$isexit=DI()->notorm->users_coinrecord
					->select("id")
					->where($unique)
					->fetchOne();
		if($isexit){
			$isup=DI()->notorm->users_coinrecord->where('id=?',$isexit['id'])->update($update);
		}else{
			$isup=DI()->notorm->users_coinrecord->insert($insert);
		} */
		$insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
		$isup=DI()->notorm->users_coinrecord->insert($insert);
					 
		$userinfo2 =DI()->notorm->users
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();	
			 
		$level=$this->getLevel($userinfo2['consumption']);			
		
		/* 清除缓存 */
		$this->delCache("userinfo_".$uid); 
		$this->delCache("userinfo_".$liveuid); 
		
		$votestotal=$this->getVotes($liveuid);
		
		$barragetoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));
		 
		$result=array("uid"=>$uid,"content"=>$content,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"giftname"=>$giftinfo['giftname'],"gifticon"=>$giftinfo['gifticon'],"level"=>$level,"coin"=>$userinfo2['coin'],"votestotal"=>$votestotal,"barragetoken"=>$barragetoken);
		
		return $result;
	}			

	/* 购买守护 */
	public function sendGuard($uid,$liveuid,$stream,$giftid,$giftcount,$content) {

		$userinfo=DI()->notorm->users
					->select('coin')
					->where('id = ?', $uid)
					->fetchOne();	
					 
		$giftinfo=array(
			"giftname"=>'守护',
			"gifticon"=>'',
			"needcoin"=>$giftcount == 12 ? 520000 : 52000 * $giftcount,
		);		
		
		$total= $giftinfo['needcoin'];
		 
		$addtime=time();
		$type='expend';
		$action='sendguard';
		if($userinfo['coin'] < $total){
			/* 余额不足 */
			return 1001;
		}		

		/* 更新用户余额 消费 */
		$isuid =DI()->notorm->users
				->where('id = ?', $uid)
				->update(array('coin' => new NotORM_Literal("coin - {$total}"),'consumption' => new NotORM_Literal("consumption + {$total}") ) );

		/* 更新直播 魅力值 累计魅力值 */
		$istouid =DI()->notorm->users
				->where('id = ?', $liveuid)
				->update( array('votes' => new NotORM_Literal("votes + {$total}"),'votestotal' => new NotORM_Literal("votestotal + {$total}") ));
				
		$stream2=explode('_',$stream);
		$showid=$stream2[1];

		$insert=array("type"=>$type,"action"=>$action,"uid"=>$uid,"touid"=>$liveuid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime );
		$isup=DI()->notorm->users_coinrecord->insert($insert);
					 
		$userinfo2 =DI()->notorm->users
				->select('consumption,coin')
				->where('id = ?', $uid)
				->fetchOne();	
			 
		$level=$this->getLevel($userinfo2['consumption']);			
		
		/* 清除缓存 */
		$this->delCache("userinfo_".$uid); 
		$this->delCache("userinfo_".$liveuid); 
		$this->delCache("userguard_".$uid."_".$liveuid);
		
		$votestotal=$this->getVotes($liveuid);
		
		$barragetoken=md5(md5($action.$uid.$liveuid.$giftid.$giftcount.$total.$showid.$addtime.rand(100,999)));
		 
		$result=array("uid"=>$uid,"content"=>$content,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"giftname"=>$giftinfo['giftname'],"gifticon"=>$giftinfo['gifticon'],"level"=>$level,"coin"=>$userinfo2['coin'],"votestotal"=>$votestotal,"barragetoken"=>$barragetoken);
		
		return $result;
	}			
	
	/* 设置/取消 管理员 */
	public function setAdmin($liveuid,$touid){
					
		$isexist=DI()->notorm->users_livemanager
					->select("*")
					->where('uid=? and  liveuid=?',$touid,$liveuid)
					->fetchOne();			
		if(!$isexist){
			$count =DI()->notorm->users_livemanager
						->where('liveuid=?',$liveuid)
						->count();	
			if($count>=5){
				return 1004;
			}		
			$rs=DI()->notorm->users_livemanager
					->insert(array("uid"=>$touid,"liveuid"=>$liveuid) );	
			if($rs!==false){
				return 1;
			}else{
				return 1003;
			}				
			
		}else{
			$rs=DI()->notorm->users_livemanager
				->where('uid=? and  liveuid=?',$touid,$liveuid)
				->delete();		
			if($rs!==false){
				return 0;
			}else{
				return 1003;
			}						
		}
	}
	
	/* 管理员列表 */
	public function getAdminList($liveuid){
		$rs=DI()->notorm->users_livemanager
						->select("uid")
						->where('liveuid=?',$liveuid)
						->fetchAll();	
		foreach($rs as $k=>$v){
			$rs[$k]=$this->getUserInfo($v['uid']);
		}				
		return $rs;
	}
	
	/* 举报 */
	public function setReport($uid,$touid,$content){
		return  DI()->notorm->users_report
				->insert(array("uid"=>$uid,"touid"=>$touid,'content'=>$content,'addtime'=>time() ) );	
	}
	
	/* 主播总映票 */
	public function getVotes($liveuid){
		$userinfo=DI()->notorm->users
					->select("votestotal")
					->where('id=?',$liveuid)
					->fetchOne();	
		return $userinfo['votestotal'];					
	}
	
	/* 超管关闭直播间 */
	public function superStopRoom($uid,$token,$liveuid,$type){
		
		$userinfo=DI()->notorm->users
					->select("token,expiretime,issuper")
					->where('id=? ',$uid)
					->fetchOne();
		if($userinfo['token']!=$token || $userinfo['expiretime']<time()){
			return 101;				
		} 	
		
		if($userinfo['issuper']==0){
			return 1001;
		}
		
		if($type==1){
			/* 关闭并禁用 */
			DI()->notorm->users->where('id=? ',$liveuid)->update(array('user_status'=>0));
		}
		
	
		$info=DI()->notorm->users_live
				->select("uid,showid,starttime,title,province,city,stream,lng,lat,type,type_val")
				->where('uid=? and islive="1"',$liveuid)
				->fetchOne();
		if($info){
			$nowtime=time();
			$stream=$info['stream'];
			$info['endtime']=$nowtime;
			

			$nums=DI()->redis->hget("livenums",$stream);
			DI()->redis->hdel("livenums",$stream);
			DI()->redis->hDel("livelist",$liveuid);
			DI()->redis->delete($liveuid.'_zombie');
			DI()->redis->delete($liveuid.'_zombie_uid');
			DI()->redis->delete('attention_'.$liveuid);
			DI()->redis->delete('userlist_'.$stream);

			$info['nums']=$nums;			
			$result=DI()->notorm->users_liverecord->insert($info);	
		}
		DI()->notorm->users_live->where('uid=?',$liveuid)->delete();
		
		return 0;
		
	}
}
