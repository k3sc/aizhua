<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\HomebaseController; 
/**
 * 直播页面
 */
class ShowController extends HomebaseController {
  //首页
	public function index() 
	{
		$uid=session("uid");
		$token=session("token");
		$config=$this->config;
		$getConfigPri=getConfigPri();
		$getConfigPub=getConfigPub();
		$this->assign("configj",json_encode($config));
		$this->assign("ConfigPub",json_encode($getConfigPub));
		$this->assign("getConfigPri",$getConfigPri);
		$this->assign("getConfigPub",$getConfigPub);
		$User=M('users');
		$Gift=M('gift');
		$Live=M('users_live');
		$Car=M('car');
		$Coinrecord=M('users_coinrecord');
		$nowtime=time();		
		/* 主播信息 */
		$anchorid=(int)I("roomnum");
		$anchorinfo=getUserInfo($anchorid);
		$anchorinfo['level']=getLevel($anchorinfo['consumption']);
		$anchorinfo['follows']=getFollownums($anchorinfo['id']);
		$anchorinfo['fans']=getFansnums($anchorinfo['id']);
		/*设置stream*/
		if($uid==$anchorinfo['id'])
		{
			$stream=$anchorid."_".time();
		}
		else
		{
			$stream=$anchorid."_".$anchorid;
		}
		$anchorinfo['stream']=$stream;
		$this->assign("anchorinfo",$anchorinfo);
		$this->assign("anchorinfoj",json_encode($anchorinfo) );	
		$liveinfo=$Live->where("uid='{$anchorinfo['id']}' and islive=1")->order("islive desc")->limit(1)->find();
		$this->assign("liveinfo",$liveinfo);
		$this->assign("liveinfoj",json_encode($liveinfo));
		if($uid>0)
		{
			/*是否踢出房间*/
			$redis = connectionRedis();
			$iskick=$redis  -> hGet($anchorinfo['id'].'kick',$uid);
			$nowtime=time();
			if($iskick>$nowtime)
			{
				$surplus=$iskick-$nowtime;
				$this->assign('jumpUrl',__APP__);
				$this->error('您已被踢出房间，剩余'.$surplus.'秒');
			}else
			{
				$redis  -> hdel($anchorinfo['id'].'kick',$uid);
			}
			/*身份判断*/
			$getisadmin=getIsAdmin($uid,$anchorinfo['id']);
			/*该主播是否被禁用*/
			$isBan=isBan($anchorinfo['id']);
			if($isBan==0)
			{
				$this->assign('jumpUrl',__APP__);
				$this->error('该主播已经被禁止直播');
			}
			$isBan=isBan($uid);
			if($isBan==0)
			{
				$this->assign('jumpUrl',__APP__);
				$this->error('你的账号已经被禁用');
			}
			/*进入房间设置redis*/
			$userinfo=$User->where("id=".$uid)->field("id,issuper")->find();
			if($userinfo['issuper']==1){
				$redis  -> hset('super',$userinfo['id'],'1');
			}else{
				$redis  -> hDel('super',$userinfo['id']);
			}
			$redis -> close();
		}
		else
		{
			$getisadmin=10;
		}
		$this->assign('identity',$getisadmin);
		/* 是否关注 */
		$isattention=isAttention($uid,$anchorinfo['id']);
		$this->assign("isattention",$isattention);
		$attention_type = $isattention ? "已关注" : "+关注" ;
		$this->assign("attention_type",$attention_type);
		$this->assign("anchorid",$anchorid);
		/* 礼物信息 */
		$giftinfo=$Gift->field("*")->order("orderno asc")->select();
		$this->assign("giftinfo",$giftinfo);
		$giftinfoj=array();
		foreach($giftinfo as $k=>$v)
		{
			$giftinfoj[$v['id']]=$v;
		}
		$this->assign("giftinfoj",json_encode($giftinfoj));
		$this->a='aaaaa';
		$configpri=M("config_private")->where("id=1")->find();
		/* 判断 播流还是推流 */
		if($uid==$anchorinfo['id'])
		{ 
			$checkToken=checkToken($uid,$token);
			if($checkToken==700){
				$this->assign('jumpUrl',__APP__);
				$this->error('登陆过期，请重新登陆');
			} 
			if($configpri['auth_islimit']==1)
			{
				$auth=M("users_auth")->field("status")->where("uid='{$uid}'")->find();
				if(!$auth || $auth['status']!=1)
				{
					$this->assign('jumpUrl',__APP__);
					$this->error("请先进行身份认证");
				}
			}	
			if($configpri['level_islimit']==1)
			{
				if($anchorinfo['level']<$configpri['level_limit'])
				{
					$this->assign('jumpUrl',__APP__);
					$this->error('等级小于'.$configpri['level_limit'].'级，不能直播');
				}						
			}
			$token=getUserToken($uid);
			$this->assign('token',$token);
			/* 流地址 */	
			$this->assign('stream',$stream);
			$this->display('player');
		}
		else
		{ 
			$this->display();
		}	
  }
	/*
	二维码=====
	value 二维码连接地址
	*/
	function qrcode(){
		$roomid=$_GET["roomid"];
		include 'simplewind/Lib/Util/phpqrcode.php'; 
		$a= new \QRcode();
		$value = "http://".$_SERVER['SERVER_NAME'].'/wxshare/index.php/Share/show?roomnum='.$roomid;
		$errorCorrectionLevel = 'L';//容错级别 
		$matrixPointSize = 6;//生成图片大小 
		//生成二维码图片 
		$a->png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2); 
		$logo = 'jb51.png';//准备好的logo图片 
		$QR = 'qrcode.png';//已经生成的原始二维码图 
		if ($logo !== FALSE) { 
			$QR = imagecreatefromstring(file_get_contents($QR)); 
			$logo = imagecreatefromstring(file_get_contents($logo)); 
			$QR_width = imagesx($QR);//二维码图片宽度 
			$QR_height = imagesy($QR);//二维码图片高度 
			$logo_width = imagesx($logo);//logo图片宽度 
			$logo_height = imagesy($logo);//logo图片高度 
			$logo_qr_width = $QR_width / 5; 
			$scale = $logo_width/$logo_qr_width; 
			$logo_qr_height = $logo_height/$scale; 
			$from_width = ($QR_width - $logo_qr_width) / 2; 
			//重新组合图片并调整大小 
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, 
			$logo_qr_height, $logo_width, $logo_height); 
		} 
		//输出图片 
		Header("Content-type: image/png");
		ImagePng($QR);
	}	
		/* 用户进入 写缓存 
		50本房间主播 60超管 40管理员 30观众 10为游客(判断当前用户身份) 
		*/
		public function setNodeInfo() {
		/* 当前用户信息 */
			$uid=session("uid");
			$showid=I('showid');
			$token=session("token");
			$stream=I('stream');
			if($uid>0){
				$info=getUserInfo($uid);	
				$info['liveuid']=$showid;
				$info['sign'] = md5($showid.'_'.$info['id']);
				$info['token']=$token;
				if($uid==$showid)
				{
					$info['userType']=50;
				}
				else
				{
					$info['userType']=40;
				}
			}else{
				/* 游客 */
				$sign= mt_rand(1000,9999);
				$info['id'] = '-'.$sign;
				$info['user_nicename'] = '游客'.$sign;
				$info['avatar'] = '';
				$info['avatar_thumb'] = '';
				$info['sex'] = '0';
				$info['signature'] = '0';
				$info['consumption'] = '0';
				$info['votestotal'] = '0';
				$info['province'] = '';
				$info['city'] = '';
				$info['level'] = '0';
				$info['sign'] = md5($showid.'_'.$sign);
				$info['token']=$info['sign'];
				$info['liveuid']=$showid;
				$info['userType']=0;
				$token =$info['sign'];
			}	
			$info['roomnum']=$showid;
			//判断该房间是否在直播
			$live=M("users_live")->where("uid=".$showid." and islive=1")->find();
			if($live)
			{
				$info['stream']=$live['stream'];
			}
			else
			{
				if($uid==$showid)
				{
					$info['stream']=$stream;
				}
				else
				{
					$info['stream']=$showid."_".$showid;
				}
			}
			$redis = connectionRedis();
			$redis  -> set($token,json_encode($info));
			$redis -> close();	
			/*判断改房间是否开启僵尸粉*/
			$iszombie=isZombie($showid);
			$data=array(
				'error'=>0,
				'userinfo'=>$info,
				'iszombie'=>$iszombie,
			);
			echo  json_encode($data);					
    }
	//开播设置
	public function createRoom()
	{
		$token=I("token");
		$stream=I("stream");
		$uid=I("uid");
		$title=I("title");
		$type=I("type");
		$type_val=I("stand");
		$User=M("users");
		$live=M("users_live");
		$userinfo= $User->field('coin,token,expiretime,user_nicename,avatar,avatar_thumb')->where("id='{$uid}'")->find();
		if($userinfo['token']!=session("token") || $userinfo['expiretime']<time())
		{
			echo '{"state":"1002","data":"","msg":"Token以过期，请重新登录"}';
			exit;	
		}
		else
		{
			$getConfigPri=getConfigPri();
			$orderid=explode("_",$stream);
			$showid=$orderid[1];
			$users_live=$live->field("uid")->where('uid='.$uid)->find();
			$data=array(
				"avatar"=>$userinfo['avatar'],
				"avatar_thumb"=>$userinfo['avatar_thumb'],
				"showid"=>$showid,
				"user_nicename"=>$userinfo['user_nicename'],
				"islive"=>"1",
				"starttime"=>$showid,
				"title"=>$title,
				"province"=>"",
				"city"=>"好像在火星",
				"stream"=>$stream,
				"pull"=>"rtmp://".$getConfigPri['pull_url']."/5showcam/".$stream,
				"lng"=>"",
				"lat"=>"",
				"topicid"=>"",
				"type"=>$type,
				"type_val"=>$type_val,
			);
			if($users_live){
				/* 更新 */
				$rs=$live->where('uid='.$uid)->save($data);
			}else{
				/*新增*/
				$data['uid']=$uid;
				$rs=$live->add($data);
			}
			
			if($rs)
			{
				$result['city']="好像在火星";
				$result['sign'] = md5($uid.'_'.$uid);
				$redis = connectionRedis();
				$redis  -> set($token,json_encode($result));
				$redis  -> hSet("livenums",$stream,0);
				$redis -> close();
				echo '{"state":"0","data":"","msg":""}';
			}
			else
			{
				echo '{"state":"1000","data":"","msg":"直播信息处理失败"}';
			}
			
		}
	}
	/*
	用户列表弹出信息
	uid_admin 50本房间主播 60超管 40管理员 30观众 10为游客(判断当前用户身份) 
	touid_admin 50本房间主播 60超管 40管理员 30观众 10为游客(判断当前点击用户身份)
	*/
	public function popupInfo()
	{
		$uid=session("uid");
		$touid=I('touid');
		$roomid=I('roomid');
		$users=M("Users");
		$info=$users->field("id,avatar,avatar_thumb,user_nicename")->where("id='{$touid}'")->find();
		if($uid>0 &&$uid!=null)
		{
			$uid_admin=getIsAdmin($uid,$roomid);
			$isBlack=isBlack($uid,$touid);
		}
		else
		{
			$uid_admin=10;
			$isBlack="";
		}
		if($touid>0 &&$touid!=null)
		{
			$touid_admin=getIsAdmin($touid,$roomid);
		}
		else
		{
			$touid_admin=10;
		}
		
		$popupInfo=array(
			"state"=>"0",
			"uid_admin"=>$uid_admin,
			"touid_admin"=>$touid_admin,
			"info"=>$info,
			"isBlack"=>$isBlack
		);
		$popupInfo=json_encode($popupInfo);
		echo $popupInfo;
	}
	public function live()
	{
		$uid=I('uid');
		$liveinfo=M("users_live")->where("uid='{$uid}' and islive='1'")->find();
		$data=array(
			'error'=>0,
			'data'=>$liveinfo,
			'msg'=>'',
		);
		echo json_encode($data);
		exit;
	}
		/* 排行榜 */
		public function rank(){
			$touid=I('touid');
			$showid=I('showid');
			$list=array();

			if(!$touid){
				echo json_encode($list);
					exit;
			}
			$Coinrecord=M('users_coinrecord');
			//本房间魅力榜
			$now=$Coinrecord->field("uid,sum(totalcoin) as total")->where("type='expend' and touid='{$touid}'")->group("uid")->order("total desc")->limit(0,20)->select();
			foreach($now as $k=>$v){
				$userinfo=getUserInfo($v['uid']);
				$now[$k]['userinfo']=$userinfo;
			}	
			$list['now']=$now;
			//全站魅力榜
			$all=$Coinrecord->field("uid,sum(totalcoin) as total")->where("type='expend'")->group("uid")->order("total desc")->limit(0,20)->select();
			foreach($all as $k=>$v){
				$userinfo=getUserInfo($v['uid']);
				$all[$k]['userinfo']=$userinfo;
			}	
			$list['all']=$all;		
			echo json_encode($list);
		}
		/*进入直播间检查房间类型*/
		public function checkLive()
		{
			$rs = array('code' => 0, 'msg' => '', 'info' => array());
			$uid=session("uid");
			$token=session("token");
			$liveuid=I("liveuid");
			$stream=I("stream");
			$rs['land']=0;
			$islive=M('users_live')->field("islive,type,type_val,starttime")->where("uid='{$liveuid}' and stream='{$stream}'")->find();
			if($islive['type']==2)
			{
				if($uid>0){
					$rs['land']=0;
				}else{
					$rs['land']=1;
					$rs['type']=$islive['type'];
					$rs['type_msg']='当前房间为付费房间，请先登陆';
					echo json_encode($rs);
					exit;
				}
			}
			if(!$islive ||$islive['islive']==0)
			{
				$rs['code'] = 1005;
				$rs['msg'] = '直播已结束，请刷新';
				echo json_encode($rs);
				exit;
			}
			else
			{
				$rs['type']=$islive['type'];
				$rs['type_msg']='';
				if($islive['type']==1){
					$rs['type_msg']=$islive['type_val'];
				}
				else if($islive['type']==2)
				{
					$rs['type_msg']='本房间为收费房间，需支付'.$islive['type_val'].'钻石';
					$isexist=M("users_coinrecord")->field('id')->where("uid=".$uid." and touid=".$liveuid." and showid=".$islive['starttime']." and action='roomcharge' and type='expend'")->find();
					if($isexist)
					{
						$rs['type']='0';
						$rs['type_msg']='';
					}
				}
				else if($islive['type']==3)
				{
					$rs['type_msg']='本房间为计时房间，每分钟支付需支付'.$islive['type_val'].'钻石';
				}
			}
			echo  json_encode($rs);
		}
		/* 直播/回放 结束后推荐 */
		public function endRecommend(){
			/* 推荐列表 */
				
			$list=M("users_live")->where("islive='1'")->order("rand()")->limit(0,4)->select();
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
       $data=array(
					'error'=>0,
					'data'=>$list,
			 );
			echo  json_encode($data);
		}
		/* 关注 */
		public function attention(){
			/* 推荐列表 */
			$uid=session("uid");
			if($uid == 0 || $uid == '0' || $uid == ""){
				$data=array(
					'error'=> 1,
					'msg'=> "请登录",
					'data'=> "请登录",
				);	
				
			}else{
				$anchorid=(int)I("roomnum");
				if($uid == $anchorid){
					$data=array(
						'error'=> 1,
						'msg'=> "不能关注自己",
						'data'=> "不能关注自己"
					);						
				}else{
					$add=array(
						'uid'	=>	$uid,
						'touid'	=>	$anchorid
					);			
					if(isAttention($uid,$anchorid)){
						$check=M('users_attention')->where($add)->delete();
						if($check !== false){
							$data=array(
								'error'=> 0,
								'msg'=> "+关注",
								'data'=> getFansnums($anchorid)
							);	
						}else{
							$data=array(
								'error'=> 1,
								'data'=> '',
								'msg'=> "取消关注失败",
							);					
						}
					}else{
						$check=M('users_attention')->add($add);
						$black=M('users_black')->where($add)->delete();
						if($check !== false){
							$data=array(
								'error'=> 0,
								'msg'=> "已关注",
								'data'=> getFansnums($anchorid)
							);
						}else{
							$data=array(
								'error'=> 1,
								'msg'=> "关注失败",
								'data'=> "",
							);					
						}	
					}
				}
			}
			echo  json_encode($data);
		}
		/*主播页面特殊直播弹窗*/
		public function selectplay()
		{
			$this->display();
		}
		public function getUserList(){
			$showid=I("showid");
			$lists=array();
			$live=M("users_live")->where("uid=".$showid." and islive=1")->find();
			if($live)
			{
				$stream=$live['stream'];
			}
			else
			{
				$stream=$showid."_".$showid;
			}
			
			$redis = connectionRedis();
			$list=$redis -> hVals('userlist_'.$stream);
			$nums=$redis->hget("livenums",$stream);
			if(!$nums)
			{
				$nums=0;
			}
			foreach($list as $v)
			{
				$lists[]=json_decode($v,true);
				$n++;
			}
			$redis -> close();
			$data['list']=$lists;
			$data['nums']=$nums;
			echo  json_encode($data);			
		} 		
	}


