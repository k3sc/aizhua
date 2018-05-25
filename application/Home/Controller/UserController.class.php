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
 * 会员相关
 */
class UserController extends HomebaseController {
	
    //首页
	public function index() {
       $ip = get_client_ip();
		echo $ip;

    }	
		/* 手机验证码 */
		public function getCode(){
			
				$verify = new \Think\Verify();
			  $checkverify=$verify->check($_REQUEST['captcha'], "");	
				if(!$checkverify){
					echo $_GET['callback']."({'errno':1120,'data':{},'errmsg':'图片验证码不正确'})";
					exit();
				}
			
				$config=getConfigPri();
			
				$target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

				$mobile = I("mobile");

				$mobile_code = $this->random(6,1);

				$post_data = "account=".$config['ihuyi_account']."&password=".$config['ihuyi_ps']."&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。");
				//密码可以使用明文密码或使用32位MD5加密
				$gets = $this->xml_to_array($this->Post($post_data, $target)); 
				if($gets['SubmitResult']['code']==2){
					$_SESSION['mobile'] = $mobile;
					$_SESSION['mobile_code'] = $mobile_code;
					$_SESSION['reg_mobile_expiretime'] = time() +60*1;
					//$rs['info']['code']=$mobile_code;
				}else{
					 $rs['code']=2;
					 $rs['msg']=$gets['SubmitResult']['msg'];
					 
				}
				
			   /*  $_SESSION['mobile'] = $mobile;
				$_SESSION['mobile_code'] = '123456';  
				$_SESSION['mobile_expiretime'] = time() +60*1; */
				
				echo $_GET['callback']."({'errno':0,'data':{},'errmsg':'验证码已送'})";
			  exit;
		}
		public function Post($curlPost,$url){
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_NOBODY, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
				$return_str = curl_exec($curl);
				curl_close($curl);
				return $return_str;
		}
		public function xml_to_array($xml){
			$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
			if(preg_match_all($reg, $xml, $matches)){
				$count = count($matches[0]);
				for($i = 0; $i < $count; $i++){
				$subxml= $matches[2][$i];
				$key = $matches[1][$i];
					if(preg_match( $reg, $subxml )){
						$arr[$key] = $this->xml_to_array( $subxml );
					}else{
						$arr[$key] = $subxml;
					}
				}
			}
			return $arr;
		}
		public function random($length = 6 , $numeric = 0) {
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
		/* 图片验证码 */
		public function getCaptcha(){
				echo $_GET['callback']."({'errno':0,'data':{'captcha':'./index.php?g=api&m=checkcode&a=index&length=4&font_size=14&width=100&height=34&charset=2345678&use_noise=1&use_curve=0'},'errmsg':'请求成功'})";
				exit;
		}		
		
		/* 登录 */
	/* 	$user_login!=$_SESSION['mobile'] */
		public function userLogin(){
			  $user_login=I("mobile");
			  $pass=I("pass");
			
				$authcode='rCt52pF2cnnKNB3Hkp';
				$user_pass="###".md5(md5($authcode.$pass));
				
				$User=M("users");
				
				$userinfo=$User->where("user_login='{$user_login}' and user_pass='{$user_pass}' and user_type='2'")->find();
				
				if(!$userinfo){
					echo $_GET['callback']."({'errno':1001,'data':{},'errmsg':'账号或密码错误'})";
					exit;							
				}else if($userinfo['user_status']==0){
					echo $_GET['callback']."({'errno':1002,'data':{},'errmsg':'账号已被禁用'})";
					exit;	
				}
				$userinfo['level']=getLevel($userinfo['experience']);
				if(!$userinfo['token'] || !$userinfo['expiretime']){
						$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
							$expiretime=time()+60*60*24*300;
						$User->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
						$userinfo['token']=$token;
				}

				session('uid',$userinfo['id']);
				session('token',$userinfo['token']);
				session('user',$userinfo);
				session("avatar",$userinfo['avatar']);
				session("user_nicename",$userinfo['user_nicename']);
				cookie('uid',$userinfo['id'],3600000);
				cookie('token',$userinfo['token'],3600000);
				
				echo $_GET['callback']."({'errno':0,'userid':{$userinfo['id']},'data':{},'errmsg':'登陆成功'})";
				exit;	
		} 	
		
		/* 注册 */
		public function userReg(){
			  $user_login=I("mobile");
			  $pass=I("pass");
			  $code=I("code");
			
		 		if($user_login!=$_SESSION['mobile']){	
					echo $_GET['callback']."({'errno':3,'data':{},'errmsg':'手机号码不一致'})";
					exit;						
				}

				if($code!=$_SESSION['mobile_code']){
					echo $_GET['callback']."({'errno':1,'data':{},'errmsg':'验证码错误'})";
					exit;				
					
				}	
				$check = passcheck($pass);

				if($check==0){
					echo $_GET['callback']."({'errno':1001,'data':{},'errmsg':'密码6-12位数字与字母'})";
					exit;		
				}else if($check==2){
					echo $_GET['callback']."({'errno':1002,'data':{},'errmsg':'密码不能纯数字或纯字母'})";
					exit;		
				}	
				$authcode='rCt52pF2cnnKNB3Hkp';
				$user_pass="###".md5(md5($authcode.$pass));
				
				$User=M("users");
				
				$ifreg=$User->field("id")->where("user_login='{$user_login}'")->find();
				if($ifreg){
					echo $_GET['callback']."({'errno':1,'data':{},'errmsg':'该手机号已被注册'})";
					exit;		
				}
				
				/* 无信息 进行注册 */
				$config=getConfig();

				$data=array(
						'user_login' => $user_login,
						'user_email' => '',
						'mobile' =>$user_login,
						'user_nicename' =>'请设置昵称',
						'user_pass' =>$user_pass,
						'signature' =>'这家伙很懒，什么都没留下',
						'avatar' =>$config['site_url'].'upload/avatar/default.jpg',
						'avatar_thumb' =>$config['site_url'].'upload/avatar/default_thumb.jpg',
						'last_login_ip' =>get_client_ip(),
						'create_time' => date("Y-m-d H:i:s"),
						'last_login_time' => date("Y-m-d H:i:s"),
						'user_status' => 1,
						"user_type"=>2,//会员
				);	
				$userid=$User->add($data);
			
				$userinfo=$User->where("id='{$userid}'")->find();		
				
				$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
				$expiretime=time()+60*60*24*300;
				$User->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
				$userinfo['token']=$token;

				$userinfo['level']=getLevel($userinfo['experience']);
				if(!$userinfo['token'] || !$userinfo['expiretime']){
						$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
						$expiretime=time()+60*60*24*300;
						$User->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
						$userinfo['token']=$token;
				}
				/*session('uid',$userinfo['id']);
				session('token',$userinfo['token']);
				session('user',$userinfo);
				cookie('uid',$userinfo['id'],3600000);
				cookie('token',$userinfo['token'],3600000);*/
				echo $_GET['callback']."({'errno':0,'userid':{$userinfo['id']},'data':{},'errmsg':'注册成功'})";
				exit;				
		}
		public function forget(){
			$user_login=I("mobile");
			$pass=I("pass");
			$code=I("code");
		
			if($user_login!=$_SESSION['mobile']){	
					echo $_GET['callback']."({'errno':3,'data':{},'errmsg':'手机号码不一致'})";
					exit;						
			}

			if($code!=$_SESSION['mobile_code']){
					echo $_GET['callback']."({'errno':1,'data':{},'errmsg':'验证码错误'})";
					exit;				
				
			}	
			$authcode='rCt52pF2cnnKNB3Hkp';
			$user_pass="###".md5(md5($authcode.$pass));
			
			$User=M("users");
			
			$ifreg=$User->field("id")->where("user_login='{$user_login}'")->find();
			if(!$ifreg){
				echo $_GET['callback']."({'errno':1,'data':{},'errmsg':'该帐号不存在'})";
				exit;		
			}				
			$result=$User->where("user_login='{$user_login}'")->setField("user_pass",$user_pass);
			if($result!==false){
				echo $_GET['callback']."({'errno':0,'data':{},'errmsg':'该帐号不存在'})";
				exit;	
			}else{
				echo $_GET['callback']."({'errno':10001,'data':{},'errmsg':'该帐号不存在'})";
				exit;		
			}
		}
		/* 退出 */
		public function logout(){
			session('uid',null);		
			session('token',null);
			session('user',null);
			session('user_nicename',null);
			session('avatar',null);
			cookie('uid',null,3600000);
			cookie('token',null,3600000);
			echo $_GET['callback']."({'errno':0,'data':{},'errmsg':'退出登录'})";
			exit;	
		}	
		/* 获取用户信息 */
		public function getLoginUserInfo(){
			$uid=session("uid");			
			if($uid){
				echo $_GET['callback']."({'errno':0,'data':{user:".json_encode(getUserPrivateInfo($uid))."},'errmsg':'取消成功'})";		
			}else{
				echo $_GET['callback']."({'errno':1,'data':{},'errmsg':'未登录'})";
			}
			exit;	
		}		
		/* 关注 */
		public function follow_add(){
			$uid=session("uid");
			$touid=(int)I('touid');
			$data['uid']=$uid;
			$data['touid']=$touid;
			$result=M("users_attention")->add($data);
			if($result){
				 $follows=getFollownums($touid);
				 $fans=getFansnums($touid);
				echo $_GET['_callback']."({'errno':0,'data':{'follows':'{$follows}','fans':'{$fans}'},'errmsg':'关注成功'})";
			}else{
				echo $_GET['_callback']."({'errno':1,'data':{},'errmsg':'关注失败'})";				
			}
			exit;	
		}		
		/* 取消关注 */
		public function follow_cancel(){
			$uid=session("uid");
			$touid=(int)I('touid');	
			$result=M("users_attention")->where("uid='{$uid}' and touid='{$touid}'")->delete();
			if($result){
				$follows=getFollownums($touid);
				$fans=getFansnums($touid);
				echo $_GET['_callback']."({'errno':0,'data':{'follows':'{$follows}','fans':'{$fans}'},'errmsg':'取消成功'})";
			}else{
				echo $_GET['_callback']."({'errno':1,'data':{},'errmsg':'取消失败'})";				
			}
			exit;	
		}
		/*环信私信通过用户名查找用户*/
		public function searchMember(){
			if(session['uid']){
				$userName=I("keyword");
				$result=M("users")->where("id={$userName} and id <> {$_SESSION['uid']}")->find();/*不能查找自己*/
				if($result){
					$data=array(
						"code"=>0,
						"msg"=>"",
						"info"=>$result
					);
					}else{
					$data=array(
						"code"=>1,
						"msg"=>"",
						"info"=>""
					);}
			}else{
				$data=array(
					"code"=>2,
					"msg"=>"",
					"info"=>""
				);
			}
			echo json_encode($data);
		}
		/*环信私信功能创建陌生人信息时，通过用户id获取用户的头像和昵称*/

		public function searchUserInfo(){
			$uid=I("uid");
			$user=M("users");
			$avatar=$user->where("id={$uid}")->getField("avatar");
			$user_nicename=$user->where("id={$uid}")->getField("user_nicename");
			if(avatar){
				$data=array(
				"code"=>0,
				"avatar"=>$avatar,
				"user_nicename"=>$user_nicename,
				"msg"=>""
				);
			}else{
				$data=array(
				"code"=>1,
				"avatar"=>$avatar,
				"user_nicename"=>$user_nicename,
				"msg"=>""
				);
			}
			echo json_encode($data);
		}
		/*环信私信功能接收消息时，判断是否被拉黑（如果是被拉黑的人发送信息，直接不接收）*/
	function checkBlack($uid,$touid){
			$result=isBlack($uid,$touid);
			echo $result;
	}
	//三方开启判断
	public function threeparty()
	{
		$getConfigPri=getConfigPri();
		$data=array(
			"qq"=>$getConfigPri['login_qq_pc'],
			"weibo"=>$getConfigPri['login_sina_pc'],
			"weixin"=>$getConfigPri['login_wx_pc'],
		);
		echo json_encode($data);
	}
	//qq第三方登录========
	public function qq() 
	{
		$href=$_SERVER['HTTP_REFERER'];
		cookie('href',$href,3600000);
		$referer = $_SERVER['HTTP_REFERER'];
    session('login_referer', $referer);
    $qc1 = new \QC();
		$qc1->qq_login();
  }
  public function qqCallback()
	{
	 import('ORG.API.qqConnectAPI'); 
    $qc = new \QC();
    $token = $qc->qq_callback();
    $openid = $qc->get_openid();
    $qq = new \QC($token, $openid);
    $arr = $qq->get_user_info();
		//得到 用户资料
		$users=M("users");
		$where=array(
			'openid'	=>	$openid,
			'login_type'	=>	'qq',
		);
		$userinfo=M('users')->where($where)->find();
		$sql=M()->getLastSql();
		$post="/";
		if(empty($userinfo))
		{	
			if($openid!="")
			{
				if($arr['gender']=='女'){
					$sex=2;
				}else{
					$sex=1;
				}
				$user_login='qq_'.time().rand(100,999);
				$user_pass='yunbaokeji';
				$user_pass=setPass($user_pass);
				$data=array(
					'openid' 	=>$openid,
					'user_login'	=> $user_login, 
					'user_pass'		=>$user_pass,
					'user_nicename'	=> $arr['nickname'],
					'sex'=> $sex,
					'avatar'=> $arr['figureurl_qq_2'],
					'avatar_thumb'	=> $arr['figureurl_qq_1'],
					'login_type'=> "qq",
					'last_login_ip' =>get_client_ip(),
					'create_time' => date("Y-m-d H:i:s"),
					'last_login_time' => date("Y-m-d H:i:s"),
					'user_status' => 1,
					"user_type"=>2,//会员
					'signature' =>'这家伙很懒，什么都没留下',
				);	
				$userid=$users->add($data);
				$userinfo=$users->where("id='{$userid}'")->find();						
				$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
				$expiretime=time()+60*60*24*300;
				$users->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
				$userinfo['token']=$token;
			}
		} 
		$userinfo['level']=getLevel($userinfo['experience']);
		if(!$userinfo['token'] || !$userinfo['expiretime'])
		{
			$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
			$expiretime=time()+60*60*24*300;
			$infoid=$userinfo['id'];
			$users->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
			$userinfo['token']=$token; 
		}
		session('uid',$userinfo['id']);
		session('token',$userinfo['token']);
		session('user',$userinfo);
		cookie('uid',$userinfo['id'],3600000);
		cookie('token',$userinfo['token'],3600000);
		$href=$_COOKIE['AJ1sOD_href'];
		echo "<meta http-equiv=refresh content='0; url=$href'>"; 		
	}	
	/**
	微信登陆 
	**/
	public function weixin()
	{
		$getConfigPri=getConfigPri();	
		$pay_url=$_SERVER['SERVER_NAME'];
	//-------配置
		$href=$_SERVER['HTTP_REFERER'];
		cookie('href',$href,3600000);
		$AppID = $getConfigPri['login_wx_pc_appid'];
		$AppSecret = $getConfigPri['login_wx_pc_appsecret'];
		$callback  = 'http://'.$pay_url.'/index.php?g=home&m=User&a=weixin_callback'; //回调地址
		//微信登录
		session_start();
		//-------生成唯一随机串防CSRF攻击
		$state  = md5(uniqid(rand(), TRUE));
		$_SESSION["wx_state"]    = $state; //存到SESSION
		$callback = urlencode($callback);
		$wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=".$AppID."&redirect_uri={$callback}&response_type=code&scope=snsapi_login&state={$state}#wechat_redirect";
		header("Location: $wxurl");
	}
	/**
	微信登陆回调
	**/
	public function weixin_callback()
	{
		$getConfigPri=getConfigPri();	
		if($_GET['code']!="")
		{
			$AppID = $getConfigPri['login_wx_pc_appid'];
			$AppSecret = $getConfigPri['login_wx_pc_appsecret'];
			$url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$AppID.'&secret='.$AppSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$arr=json_decode($json,1);
			//得到 access_token 与 openid
			$url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$json =  curl_exec($ch);
			curl_close($ch);
			$arr=json_decode($json,1);
			//得到 用户资料
			$users=M("users");
			$where=array(
				'openid'	=>	$arr['openid'],
			);
			$userinfo=M('users')->where($where)->find();
			if(empty($userinfo))
			{	
				if($arr['openid']!="")
				{
					$user_login='wx_'.time().rand(100,999);
					$user_pass='yunbaokeji';
					$user_pass=setPass($user_pass);
					$data=array(
						'openid' 	=>$arr['openid'],
						'user_login'	=> $user_login, 
						'user_pass'		=>$user_pass,
						'user_nicename'	=> $arr['nickname'],
						'sex'=> $arr['sex'],
						'avatar'=> $arr['headimgurl'],
						'avatar_thumb'	=> $arr['headimgurl'],
						'login_type'=> "wx",
						'last_login_ip' =>get_client_ip(),
						'create_time' => date("Y-m-d H:i:s"),
						'last_login_time' => date("Y-m-d H:i:s"),
						'user_status' => 1,
						"user_type"=>2,//会员
						'signature' =>'这家伙很懒，什么都没留下',
					);	
					$userid=$users->add($data);
					$userinfo=$users->where("id='{$userid}'")->find();						
					$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
					$expiretime=time()+60*60*24*300;
					$users->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
					$userinfo['token']=$token;
				}
			} 
			$userinfo['level']=getLevel($userinfo['experience']);
			if(!$userinfo['token'] || !$userinfo['expiretime'])
			{
				$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
				$expiretime=time()+60*60*24*300;
				$infoid=$userinfo['id'];
				$users->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
				$userinfo['token']=$token; 
			}
			session('uid',$userinfo['id']);
			session('token',$userinfo['token']);
			session('user',$userinfo);
			cookie('uid',$userinfo['id'],3600000);
			cookie('token',$userinfo['token'],3600000);
			$href=$_COOKIE['AJ1sOD_href'];
		 	echo "<meta http-equiv=refresh content='0; url=$href'>"; 
		}
	}
	/**
	微博登陆
	**/
	public function weibo()
	{
		
		$href=$_SERVER['HTTP_REFERER'];
		cookie('href',$href,3600000);
		$getConfigPri=getConfigPri();	
		$WB_AKEY=$getConfigPri['login_sina_pc_akey'];
		$WB_SKEY=$getConfigPri['login_sina_pc_skey'];
		$pay_url=$_SERVER['SERVER_NAME'];
		$WB_CALLBACK_URL="http://".$pay_url."/index.php?g=home&m=User&a=weibo_callback";
		include_once( 'Lib/Extend/libweibo/config.php' );
		include_once( 'Lib/Extend/libweibo/saetv2.ex.class.php' );
		$o = new \SaeTOAuthV2($WB_AKEY,$WB_SKEY);
		$code_url = $o->getAuthorizeURL( $WB_CALLBACK_URL );
		header("location:".$code_url); 
	}
	/**
	微博登陆回调
	**/
	public function weibo_callback()
	{
		if($_GET['code']!="")
		{ 
			$getConfigPri=getConfigPri();	
			$WB_AKEY=$getConfigPri['login_sina_pc_akey'];
			$WB_SKEY=$getConfigPri['login_sina_pc_skey'];
			$pay_url=$_SERVER['SERVER_NAME'];
			$WB_CALLBACK_URL="http://".$pay_url."/index.php?g=home&m=User&a=weibo_callback";
			$o = new \SaeTOAuthV2( $WB_AKEY , $WB_SKEY );
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = $WB_CALLBACK_URL;
			$token = $o->getAccessToken( 'code', $keys ); 
			$c = new \SaeTClientV2( $WB_AKEY , $WB_SKEY ,$token["access_token"]);
			$ms = $c->home_timeline(); 
			$uid_get = $c->get_uid();
			$uid =  $token['uid'];
			$user_message = $c->show_user_by_id( $token['uid']);
				//得到 用户资料
		 	$users=M("users");
			$where=array(
				'openid'	=>	$user_message['id'],
			);
			$userinfo=M('users')->where($where)->find();
			if(empty($userinfo))
			{	
				if($user_message['id']!="")
				{
					$user_login='sina_'.time().rand(100,999);
					$user_pass='yunbaokeji';
					$user_pass=setPass($user_pass);
					$data=array(
						'openid' 	=>$user_message['id'],
						'user_login'	=> $user_login, 
						'user_pass'		=>$user_pass,
						'user_nicename'	=> $user_message['screen_name'],
						'sex'=>'0',
						'avatar'=> $user_message['profile_image_url'],
						'avatar_thumb'	=> $user_message['profile_image_url'],
						'login_type'=> "wb",
						'last_login_ip' =>get_client_ip(),
						'create_time' => date("Y-m-d H:i:s"),
						'last_login_time' => date("Y-m-d H:i:s"),
						'user_status' => 1,
						"user_type"=>2,//会员
						'signature' =>'这家伙很懒，什么都没留下',
					);
					$userid=$users->add($data);
					$userinfo=$users->where("id='{$userid}'")->find();						
					$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
					$expiretime=time()+60*60*24*300;
					$users->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
					$userinfo['token']=$token; 
				}	
			}		
			$userinfo['level']=getLevel($userinfo['experience']);
			if(!$userinfo['token'] || !$userinfo['expiretime'])
			{
				$token=md5(md5($userinfo['id'].$userinfo['user_login'].time()));
				$expiretime=time()+60*60*24*300;
				$infoid=$userinfo['id'];
				$users->where("id='{$userinfo['id']}'")->save(array('token'=>$token,'expiretime'=>$expiretime));
				$userinfo['token']=$token; 
			}
			session('uid',$userinfo['id']);
			session('token',$userinfo['token']);
			session('user',$userinfo);
			cookie('uid',$userinfo['id'],3600000);
			cookie('token',$userinfo['token'],3600000);
			$href=$_COOKIE['AJ1sOD_href'];
		 	echo "<meta http-equiv=refresh content='0; url=$href'>"; 

		} 

	}

}


