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

				$mobile_code = random(6,1);

			  /*$post_data = "account=".$config['ihuyi_account']."&password=".$config['ihuyi_ps']."&mobile=".$mobile."&content=".rawurlencode("您的验证码是：".$mobile_code."。请不要把验证码泄露给其他人。");
				//密码可以使用明文密码或使用32位MD5加密
				$gets = $this->xml_to_array($this->Post($post_data, $target)); 
				if($gets['SubmitResult']['code']==2){
					$_SESSION['mobile'] = $mobile;
					$_SESSION['mobile_code'] = $mobile_code;
					//$rs['info']['code']=$mobile_code;
				}else{
					 $rs['code']=2;
					 $rs['msg']=$gets['SubmitResult']['msg'];
					 
				} */
				
			    $_SESSION['mobile'] = $mobile;
				$_SESSION['mobile_code'] = '123456';  
				
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
			
				$user_pass=sp_password($pass); 
				
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
				cookie('uid',$userinfo['id'],3600000);
				cookie('token',$userinfo['token'],3600000);

				echo $_GET['callback']."({'errno':0,'data':{},'errmsg':'登陆成功'})";
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

				$user_pass=sp_password($pass); 
				
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

				session('uid',$userinfo['id']);
				session('token',$userinfo['token']);
				session('user',$userinfo);
				cookie('uid',$userinfo['id'],3600000);
				cookie('token',$userinfo['token'],3600000);

				echo $_GET['callback']."({'errno':0,'data':{},'errmsg':'注册成功'})";
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


}


