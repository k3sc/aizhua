<?php
namespace Home\Controller;
use Think\Controller;
class ShareController extends Controller {
    public function index(){
		$config=getConfigPub();
		$this->assign('config',$config);
		$Model = new \Think\Model();
		
		$list=$Model->query("select l.uid,l.avatar,l.avatar_thumb,l.user_nicename,l.title,l.city,l.stream,l.pull,l.thumb from __PREFIX__users_live l left join __PREFIX__users u on l.uid=u.id where l.islive= '1'  order by u.isrecommend desc,l.starttime desc limit 0,20");
		
		foreach($list as $k=>$v){
			if(!$v['thumb']){
				$list[$k]['thumb']=$v['avatar'];
			}
		}
		
		$this->assign('list',$list);
		
		/* session('uid',null);
		session('token',null);
		session('openid',null);
		session('unionid',null);
		session('userinfo',null); */


		$this->display();
		
		
    }
	
	public function show(){
		$roomnum=(int)I('roomnum');
		
		$User=M('users');
		$Live=M('users_live');
		$liveinfo=array();
		$configpri=getConfigPri();
		$this->assign('configpri',$configpri);
		
		$config=getConfigPub();
		$this->assign('config',$config);

		$liveinfo=$Live->field("uid,user_nicename,avatar,avatar_thumb,islive,stream")->where("uid='{$roomnum}' and islive='1'")->find();
		if(!$liveinfo){
			$anchor=$User->field("id,user_nicename,avatar,avatar_thumb")->where("id='{$roomnum}'")->find();
			$liveinfo['uid']=$anchor['id'];
			$liveinfo['user_nicename']=$anchor['user_nicename'];
			$liveinfo['avatar']=$anchor['avatar'];
			$liveinfo['avatar_thumb']=$anchor['avatar_thumb'];
			$liveinfo['islive']='0';
		}
		
		$hls=$liveinfo['islive'] ? 'http://'.$configpri['pull_url'].'/5showcam/'.$liveinfo['stream'].'.m3u8':'';

		$this->assign('hls',$hls);
		$this->assign('liveinfo',$liveinfo);
		
		$isattention=0;
		$uid=session("uid");
		//$uid=12;
		if($uid){
			$userinfo=getUserPrivateInfo($uid);
			
			$isexist=M("users_attention")->where("uid='{$uid}' and touid='{$liveinfo['uid']}'")->find();
			if($isexist){
				$isattention=1;
			}
		}
		$this->assign('isattention',$isattention);
		$this->assign('userinfo',$userinfo);
		$this->assign('userinfoj',json_encode($userinfo));

		$this->display();
	}
	
	public function wxLogin(){
		$roomnum=I('roomnum');
		$configpri=getConfigPri();
		
		$AppID = $configpri['login_wx_appid'];
		$callback  = 'http://'.$_SERVER['HTTP_HOST'].'/wxshare/index.php/Share/wxLoginCallback?roomnum='.$roomnum; //回调地址
		//微信登录
		session_start();
		//-------生成唯一随机串防CSRF攻击
		$state  = md5(uniqid(rand(), TRUE));
		$_SESSION["wx_state"]    = $state; //存到SESSION
		$callback = urlencode($callback);
		//snsapi_base 静默  snsapi_userinfo 授权
		$wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$AppID}&redirect_uri={$callback}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect ";
		
		header("Location: $wxurl");
	}
	
	public function wxLoginCallback(){
		$code=I('code');
		$roomnum=I('roomnum');
		if($code){
			$configpri=getConfigPri();
		
			$AppID = $configpri['login_wx_appid'];
			$AppSecret = $configpri['login_wx_appsecret'];
			/* 获取token */
			$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$AppID}&secret={$AppSecret}&code={$code}&grant_type=authorization_code";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$arr=json_decode($json,1);
			/* 刷新token 有效期为30天 */
			$url="https://api.weixin.qq.com/sns/oauth2/refresh_token?appid={$AppID}&grant_type=refresh_token&refresh_token={$arr['refresh_token']}";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			
			$url="https://api.weixin.qq.com/sns/userinfo?access_token={$arr['access_token']}&openid={$arr['openid']}&lang=zh_CN";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$wxuser=json_decode($json,1);

			/* 公众号绑定到 开放平台 才有 unionid  否则 用 openid  */
			$openid=$wxuser['unionid'];
			$User=M('users');
		
			$userinfo=$User->field("id,user_login,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,coin,votes,birthday,issuper")->where("openid='{$openid}'")->find();

			if(empty($userinfo)){	
				if($openid!=""){
					$authcode='rCt52pF2cnnKNB3Hkp';
					$user_pass="###".md5(md5($authcode.'123456'));
					
					$data=array(
						'openid' 	=>$openid,
						'user_login'	=> "wx_".time().substr($openid,-4), 
						'user_pass'		=>$user_pass,
						'user_nicename'	=> filterEmoji($wxuser['nickname']),
						'sex'=> $wxuser['sex'],
						'avatar'=> $wxuser['headimgurl'],
						'avatar_thumb'	=> $wxuser['headimgurl'],
						'login_type'=> "wx",
						'last_login_ip' =>$_SERVER['REMOTE_ADDR'],
						'create_time' => date("Y-m-d H:i:s"),
						'last_login_time' => date("Y-m-d H:i:s"),
						'user_status' => 1,
						"user_type"=>2,//会员
						'signature' =>'这家伙很懒，什么都没留下',
					);	
					$userid=$User->add($data);
					
					$userinfo=$User->field("id,user_login,user_nicename,avatar,avatar_thumb,sex,signature,consumption,votestotal,province,city,coin,votes,birthday,issuper")->where("id='{$userid}'")->find();
				}
			} 
			$userinfo['level']=getLevel($userinfo['consumption']);

			$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
			$expiretime=time()+60*60*24*300;

			$User->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
			$userinfo['token']=$token; 

			session('uid',$userinfo['id']);
			session('token',$userinfo['token']);
			session('openid',$wxuser['openid']);
			session('unionid',$wxuser['unionid']);
			session('userinfo',$userinfo);
			
			$href='http://'.$_SERVER['HTTP_HOST'].'/wxshare/index.php/Share/show?roomnum='.$roomnum;
			
		 	header("Location: $href");
			
		}else{
			
			
			
		}
		
	}
	
	/* 用户进入 写缓存 */
	public function setNodeInfo() {

		/* 当前用户信息 */
		$uid=session("uid");
		$liveuid=I('liveuid');
		$token=session("token");
		if($uid>0){ 
			$info=getUserInfo($uid);				
			$info['sign'] = md5($liveuid.'_'.$info['id']);
			$info['token']=$token;
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
			$info['sign'] = md5($liveuid.'_'.$sign);
			$info['token']=$info['sign'];
			$token =$info['sign'] ;
		}			

		$redis = connectionRedis();
		$redis  -> set($token,json_encode($info));
		$redis -> close();	
		$data=array(
			'error'=>0,
			'userinfo'=>$info,
		 );
		echo  json_encode($data);				
		
	}
	
	
	public function getGift(){
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		
		$gift=M("gift")->field("id,type,giftname,needcoin,gifticon")->order("orderno asc")->select();
		$rs['info']=$gift;
		echo json_encode($rs);
		exit;
	}
	
	/* 关注 */
	public function follow(){
		$uid=session("uid");
		$touid=(int)I('touid');
		$rs = array('code' => 0, 'msg' => '', 'info' => array());
		$data=array(
			"uid"=>$uid,
			"touid"=>$touid,
		);
		$result=M("users_attention")->add($data);
		if(!$result){
			$rs = array(
				'code' => 1001, 
				'msg' => '关注失败', 
				'info' => array()
			);
		}
		echo json_encode($rs);
		exit;
	}
	
	/* 送礼物 */
	public function sendGift(){

		$User=M("users");
		$uid=session("uid");
		$token=I("token");
		$touid=I('touid');
		$stream=I('stream');
		$giftid=I('giftid');
		$giftcount=1;
		$userinfo= $User->field('coin,token,expiretime,user_nicename,avatar')->where("id='{$uid}'")->find();
	
		/* 礼物信息 */
		$giftinfo=M("gift")->field("giftname,gifticon,needcoin,type")->where("id='{$giftid}'")->find();		
		if(!$giftinfo){
			echo '{"errno":"1001","data":"","msg":"礼物信息错误"}';
			exit;				
		}
		$total= $giftinfo['needcoin']*$giftcount;
		$addtime=time();
		if($userinfo['coin'] < $total){
			/* 余额不足 */
			echo '{"errno":"1001","data":"","msg":"余额不足"}';
			exit;	
		}		
		/* 更新用户余额 消费 */
		M()->execute("update __PREFIX__users set coin=coin-{$total},consumption=consumption+{$total} where id='{$uid}'");
		/* 更新直播 映票 累计映票 */						 
		M()->execute("update __PREFIX__users set votes=votes+{$total},votestotal=votestotal+{$total} where id='{$touid}'");
		/* 更新直播 映票 累计映票 */
		$stream2=explode('_',$stream);
		$showid=$stream2[1];
		
		M("users_coinrecord")->add(array("type"=>'expend',"action"=>'sendgift',"uid"=>$uid,"touid"=>$touid,"giftid"=>$giftid,"giftcount"=>$giftcount,"totalcoin"=>$total,"showid"=>$showid,"addtime"=>$addtime ));	
		
		$userinfo2=$User->field("consumption,coin,votestotal")->where("id='{$uid}'")->find();
		$level=getLevel($userinfo2['consumption']);				 
		$gifttoken=md5(md5('sendGift'.$uid.$touid.$giftid.$giftcount.$total.$showid.$addtime));

		$result=array("uid"=>(int)$uid,"giftid"=>(int)$giftid,"giftcount"=>(int)$giftcount,"totalcoin"=>$total,"giftname"=>$giftinfo['giftname'],"gifticon"=>$giftinfo['gifticon'],"level"=>$level,"coin"=>$userinfo2['coin'],"votestotal"=>$userinfo2['votestotal']);
		$redis = $this->connectionRedis();
		$redis  -> set($gifttoken,json_encode($result));
		$redis -> close();	
		$evensend="n";
		if($giftinfo['type']==1)
		{
			$evensend="y";
		}
		echo '{"errno":"0","uid":"'.$uid.'","level":"'.$level.'","evensend":"'.$evensend.'","coin":"'.$userinfo2['coin'].'","gifttoken":"'.$gifttoken.'","msg":"赠送成功"}';
		exit;	
			
	}

	public function connectionRedis(){
		$redis = new \Redis();
		$redis -> pconnect(C('REDIS_HOST'),6379);
		$redis -> auth(C('REDIS_AUTH'));
		return $redis;
	}	
	/* 支付页面  */
	public function pay(){
		$uid=session('uid');
		$userinfo=M("users")->field("id,user_nicename,avatar_thumb,coin")->where("id='{$uid}'")->find();
		$this->assign('userinfo',$userinfo);
		
		$chargelist=M('charge_rules')->field('id,coin,money,money_ios,product_id,give')->order('orderno asc')->select();
		
		$this->assign('chargelist',$chargelist);
		
		$this->display();
	}
	/* 获取订单号 */
	public function getOrderId(){
		$uid=session('uid');
		$chargeid=I('chargeid');
		$rs=array(
			'code'=>0,
			'data'=>array(),
			'msg'=>'',
		);
		$charge=M("charge_rules")->where("id={$chargeid}")->find();
		if($charge){
			$orderid=$uid.'_'.date('YmdHis').rand(100,999);
			$orderinfo=array(
				"uid"=>$uid,
				"touid"=>$uid,
				"money"=>$charge['money'],
				"coin"=>$charge['coin'],
				"coin_give"=>$charge['give'],
				"orderno"=>$orderid,
				"type"=>'2',
				"status"=>0,
				"addtime"=>time()
			);
			$result=M("users_charge")->add($orderinfo);
			if($result){
				$rs['data']['uid']=$uid;
				$rs['data']['money']=$charge['money'];
				$rs['data']['orderid']=$orderid;
			}else{
				$rs['code']=1001;
				$rs['msg']='订单生成失败';
			}
			
		}else{
			$rs['code']=1002;
			$rs['msg']='订单信息错误';
			
		}
		
		
		echo json_encode($rs);
		exit;
		
	}
	/* 支付 */
	public function charge(){
		

		ini_set('date.timezone','Asia/Shanghai');
		//error_reporting(E_ERROR);
		require_once "../wxpay/lib/WxPay.Api.php";
		require_once "../wxpay/pay/WxPay.JsApiPay.php";

		//打印输出数组信息
		/* function printf_info($data)
		{
			foreach($data as $key=>$value){
				echo "<font color='#00ff55;'>$key</font> : $value <br/>";
			}
		} */

		/* $uid=$_REQUEST['uid'];
		$money=$_REQUEST['money'];
		$orderid=$_REQUEST['orderid']; */
		
		$uid=12;
		$money=0.01;
		$orderid='12_123456';

		$fee=$money*100;

		$money=number_format($money,2,'.','');
		
		//①、获取用户openid
		$tools = new \JsApiPay();

		//$openId = $tools->GetOpenid();
		$openId = session('openid');
		
		//②、统一下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody("账户充值");
		$input->SetAttach("test");
		$input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
		//$input->SetOut_trade_no($orderid);
		$input->SetTotal_fee($fee);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag($uid);
		$input->SetNotify_url('http://'.$_SERVER['HTTP_HOST'].'/wxpay/pay/notify_jsapi.php');
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$WxPayApi=new \WxPayApi();
		//$order = WxPayApi::unifiedOrder($input);
		$order = $WxPayApi->unifiedOrder($input);
		//echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		//printf_info($order);
		$jsApiParameters = $tools->GetJsApiParameters($order);		
		$rs=array(
			'code'=>0,
			'data'=>$jsApiParameters,
			'msg'=>0,
		);
		echo json_encode($rs);
		exit;
	}
	
	
	
	
	
	
}