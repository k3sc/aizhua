<?php

class Model_Login extends Model_Common {
	
//	protected $fields='id,user_nicename,avatar,avatar_thumb,sex,signature,coin,consumption,user_status,login_type,province,city,birthday';
	protected $fields='*';

	/* 会员登录 */   	
    public function userLogin($user_login,$user_pass,$sys,$androidtoken,$iphonetoken) {

		$user_pass=$this->setPass($user_pass);
		
		$info=DI()->notorm->users
				->select($this->fields.',user_pass')
				->where('user_login=? and user_type="2"',$user_login) 
				->fetchOne();
		if(!$info || $info['user_pass'] != $user_pass){
			return 1001;
		}
		unset($info['user_pass']);
		if($info['user_status']=='0'){
			return 1002;					
		}
		unset($info['user_status']);
		$info['level']=$this->getLevel($info['consumption']);
		unset($info['consumption']);
		$token=md5(md5($info['id'].$user_login.time()));
		
		$info['token']=$token;
		$info['avatar']=$this->get_upload_path($info['avatar']);
		$info['avatar_thumb']=$this->get_upload_path($info['avatar_thumb']);

		$this->updateToken($info['id'],$token,$androidtoken,$iphonetoken);
		$info['isvip']=$this->getUserVip($info['id'])<time() ? 0 : 1;

		//转换用户设置 (表中的字段为json)
        $info['user_setting'] = json_decode($info['user_setting'],true);
		
		$cache=array("token_".$info['id'],"userinfo_".$info['id']);
		$this->delcache($cache);

        $filename = './../../simplewind/Lib/Extend/TIMServerSdk/TimRestApiConfig.json';
        $json_config = file_get_contents($filename);
        $app_config = json_decode($json_config, true);

        $info['identifier'] = $app_config["identifier"];
        $info['user_sig'] = $app_config["user_sig"];
		$info['tim_uid'] = 'wawaji_'.$info['id'];
		$info['tim_pwd'] = 'wawaji_'.$info['id'];

        $invit_switch = DI()->notorm->active_config->where('id=1')->select('invitation_code')->fetchOne();
        $info['invit_switch'] =  $invit_switch['invitation_code'];

        /*更新登录方式，系统平台*/
        DI()->notorm->users->where('id='.$info['id'])->update(['sys'=>$sys]);

        $info['sys'] = $sys;

        return $info;
    }	
	
	/* 会员注册 */
    public function userReg($user_login,$user_pass) {

		$user_pass=$this->setPass($user_pass);
		
		$data=array(
			'user_login' => $user_login,
			'user_email' => $user_login,
//			'mobile' =>$user_login,
//			'user_nicename' =>'手机用户'.substr($user_login,-4),
			'user_nicename' =>$user_login,
			'user_pass' =>$user_pass,
			'signature' =>'这家伙很懒，什么都没留下',
			'avatar' =>'/default.jpg',
			'avatar_thumb' =>'/default_thumb.jpg',
			'last_login_ip' =>$_SERVER['REMOTE_ADDR'],
			'create_time' => date("Y-m-d H:i:s"),
			'last_login_time' => date("Y-m-d H:i:s"),
			'user_status' => 1,
			"user_type"=>2,//会员
            'user_activation_key' => $this->get_code(),//邀请码
            'user_setting' => json_encode(['lan'=>1,'bgmusic'=>1,'yx'=>1]),//用户设置（语言，背景音乐，音效）
		);
		$isexist=DI()->notorm->users
				->select('id')
				->where('user_login=? and user_type="2"',$user_login) 
				->fetchOne();
		if($isexist){
			return 1006;
		}

		$rs=DI()->notorm->users->insert($data);		

		include_once './../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
		$api = \createRestAPI();
		$api->register_account('wawaji_'.$rs['id'], 3, 'wawaji_'.$rs['id']);
		//$api->group_add_group_member('0', 'wawaji_'.$rs['id'], 1);//只能用户自己申请加入
		
		$info['id']=$rs['id'];
		$info['user_nicename']=$data['user_nicename'];
		$info['avatar']=$this->get_upload_path($data['avatar']);
		$info['avatar_thumb']=$this->get_upload_path($data['avatar_thumb']);
		$info['sex']='2';
		$info['signature']=$data['signature'];
		$info['coin']='0';
		$info['login_type']='phone';
		$info['level']='1';
		$info['province']='';
		$info['city']='';
		$info['birthday']='';
		
		$token=md5(md5($info['id'].$user_login.time()));
		
		$info['token']=$token;
		
		$this->updateToken($info['id'],$token);
		
		$cache=array("token_".$info['id'],"userinfo_".$info['id']);
		$this->delcache($cache);

		$info = DI()->notorm->users->select()->where('id=?',$rs['id'])->fetchOne();
        $info['user_setting'] = json_decode($info['user_setting'],true);
				
        $filename = './../../simplewind/Lib/Extend/TIMServerSdk/TimRestApiConfig.json';
        $json_config = file_get_contents($filename);
        $app_config = json_decode($json_config, true);

        $info['identifier'] = $app_config["identifier"];
        $info['user_sig'] = $app_config["user_sig"];
		$info['tim_uid'] = 'wawaji_'.$rs['id'];
		$info['tim_pwd'] = 'wawaji_'.$rs['id'];


        /* 注册送币活动 */
		$this->register_coin($rs['id']);

		return $info;
    }

	/* 找回密码 */
	public function userFindPass($user_login,$user_pass){
		$isexist=DI()->notorm->users
				->select('id')
				->where('user_login=? and user_type="2"',$user_login) 
				->fetchOne();
		if(!$isexist){
			return 1006;
		}		
		$user_pass=$this->setPass($user_pass);

		return DI()->notorm->users
				->where('id=?',$isexist['id']) 
				->update(array('user_pass'=>$user_pass));
		
	}
		
		
	/* 第三方会员登录 */
    public function userLoginByThird($openid,$type,$nickname,$avatar,$sex,$sys,$androidtoken,$iphonetoken) {
        $info=DI()->notorm->users
            ->select($this->fields)
            ->where('openid=? and login_type=? and user_type="2"',$openid,$type)
            ->fetchOne();

		if(!$info){
			/* 注册 */
			$user_pass='yunbaokeji';
			$user_pass=$this->setPass($user_pass);
			$user_login=$type.'_'.time().rand(100,999);

			if(!$nickname){
				$nickname=$type.'用户-'.substr($openid,-4);
			}else{
				$nickname=urldecode($nickname);
			}
			if(!$avatar){
				$avatar='/default.jpg';
				$avatar_thumb='/default_thumb.jpg';
			}else{
				if( strpos($avatar,'http') !== 0 ){
					$avatar = '/default.jpg';
					$avatar_thumb = '/default_thumb.jpg';
				}else{
					$avatar=urldecode($avatar);
					$avatar_thumb=$avatar;
				}
			}

			$data=array(
				'user_login' => $user_login,
				'user_nicename' =>$nickname,
				'user_pass' =>$user_pass,
				'signature' =>'这家伙很懒，什么都没留下',
				'avatar' =>$avatar,
				'avatar_thumb' =>$avatar_thumb,
				'last_login_ip' =>$_SERVER['REMOTE_ADDR'],
				'create_time' => date("Y-m-d H:i:s"),
				'last_login_time' => date("Y-m-d H:i:s"),
				'user_status' => 1,
				'openid' => $openid,
				'login_type' => $type, 
				"user_type"=>2,//会员
                'user_setting' => json_encode(['lan'=>1,'bgmusic'=>1,'yx'=>1]),//用户设置（语言，背景音乐，音效）
                'sex' => $sex,
                'user_activation_key' => $this->get_code(),
			);

			$rs=DI()->notorm->users->insert($data);

			include_once './../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
			$api = \createRestAPI();
			$api->register_account('wawaji_'.$rs['id'], 3, 'wawaji_'.$rs['id']);
			//$api->group_add_group_member('0', 'wawaji_'.$rs['id'], 1);//只能用户自己申请加入

            $info=DI()->notorm->users
                ->select($this->fields)
                ->where('id=?',$rs['id'])
                ->fetchOne();

            /* 注册送币活动 */
            $this->register_coin($rs['id']);

		}
		
		if($info['user_status']=='0'){
			return 1001;					
		}
		
		unset($info['user_status']);
		$info['level']=$this->getLevel($info['consumption']);
		unset($info['consumption']);

		
		$token=md5(md5($info['id'].$openid.time()));
		
		$info['token']=$token;

		$this->updateToken($info['id'],$token,$androidtoken,$iphonetoken);
		$info['isvip']=$this->getUserVip($info['id'])<time() ? 0 : 1;
        //转换用户设置 (表中的字段为json)
        $info['user_setting'] = json_decode($info['user_setting'],true);

		$cache=array("token_".$info['id'],"userinfo_".$info['id']);
		$this->delcache($cache);

        $filename = './../../simplewind/Lib/Extend/TIMServerSdk/TimRestApiConfig.json';
        $json_config = file_get_contents($filename);
        $app_config = json_decode($json_config, true);

        $info['identifier'] = $app_config["identifier"];
        $info['user_sig'] = $app_config["user_sig"];
		$info['tim_uid'] = 'wawaji_'.$info['id'];
		$info['tim_pwd'] = 'wawaji_'.$info['id'];

        $invit_switch = DI()->notorm->active_config->where('id=1')->select('invitation_code')->fetchOne();
        $info['invit_switch'] =  $invit_switch['invitation_code'];

        /*更新登录系统平台信息*/
        DI()->notorm->users->where('id='.$info['id'])->update(['sys'=>$sys]);
        $info['sys'] = $sys;

        return $info;
    }

    /* 会员注册 */
    public function userMobileReg($user_login,$user_pass) {

		$user_pass=$this->setPass($user_pass);
		
		$datetime = date("Y-m-d H:i:s");
		$data=array(
			'user_login' => $user_login,
			'mobile' =>$user_login,
			'user_nicename' =>'手机用户'.substr($user_login,-4),
			'user_pass' =>$user_pass,
			'signature' =>'这家伙很懒，什么都没留下',
			'avatar' =>'/default.jpg',
			'avatar_thumb' =>'/default_thumb.jpg',
			'last_login_ip' =>$_SERVER['REMOTE_ADDR'],
			'create_time' => $datetime,
			'last_login_time' => $datetime,
			'user_status' => 1,
			"user_type"=>2,//会员
			'user_activation_key' => $this->get_code(),//邀请码
		);

		$isexist=DI()->notorm->users
				->select('id')
				->where('user_login=? and user_type="2"',$user_login) 
				->fetchOne();
		if($isexist){
			return 1006;
		}

		$rs=DI()->notorm->users->insert($data);		
		
		include_once './../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
		$api = \createRestAPI();
		$api->register_account('wawaji_'.$rs['id'], 3, 'wawaji_'.$rs['id']);
		//$api->group_add_group_member('0', 'wawaji_'.$rs['id'], 1);//只能用户自己申请加入
		
		$info['id']=$rs['id'];
		$info['user_nicename']=$data['user_nicename'];
		$info['avatar']=$this->get_upload_path($data['avatar']);
		$info['avatar_thumb']=$this->get_upload_path($data['avatar_thumb']);
		$info['sex']='2';
		$info['signature']=$data['signature'];
		$info['coin']='0';
		$info['login_type']='phone';
		$info['level']='1';
		$info['province']='';
		$info['city']='';
		$info['birthday']='';

		
		$token=md5(md5($info['id'].$user_login.time()));
		
		$info['token']=$token;
		
		$this->updateToken($info['id'],$token);
		
		$cache=array("token_".$info['id'],"userinfo_".$info['id']);
		$this->delcache($cache);
		
		$info = DI()->notorm->users->select()->where('id=?',$rs['id'])->fetchOne();
        $info['user_setting'] = json_decode($info['user_setting'],true);
				
        $filename = './../../simplewind/Lib/Extend/TIMServerSdk/TimRestApiConfig.json';
        $json_config = file_get_contents($filename);
        $app_config = json_decode($json_config, true);

        $info['identifier'] = $app_config["identifier"];
        $info['user_sig'] = $app_config["user_sig"];
		$info['tim_uid'] = 'wawaji_'.$rs['id'];
		$info['tim_pwd'] = 'wawaji_'.$rs['id'];
		
		$this->register_coin($rs['id']);
		
		return $info;
    }

    /* 找回密码 */
	public function userMobileFindPass($user_login,$user_pass){
		$isexist=DI()->notorm->users
				->select('id')
				->where('user_login=? and user_type="2"',$user_login) 
				->fetchOne();
		if(!$isexist){
			return 1006;
		}		
		$user_pass=$this->setPass($user_pass);

		return DI()->notorm->users
				->where('id=?',$isexist['id']) 
				->update(array('user_pass'=>$user_pass));
		
	}
	
	/* 更新token 登陆信息 */
    public function updateToken($uid,$token,$androidtoken,$iphonetoken) {
		$expiretime=time()+60*60*24*300;
		
        DI()->notorm->users
			->where('id=?',$uid)
            ->update(array("token"=>$token, "androidtoken"=>$androidtoken, "iphonetoken"=>$iphonetoken, "expiretime"=>$expiretime ,'last_login_time' => date("Y-m-d H:i:s"), "last_login_ip"=>$_SERVER['REMOTE_ADDR'] ));
		return 1;
    }


    private function register_coin($user_id){
        // 读取有无注册送币活动
        $sendInfo = DI()->notorm->active_config->select('*')->where('id=1')->fetchOne();

        if ($sendInfo['register_coin'] > 0) {
            DI()->notorm->users->where('id='.$user_id)->update(array('coin'=>$sendInfo['register_coin']));
            DI()->notorm->users->where('id='.$user_id)->update(array('active_coin'=>$sendInfo['register_coin']));
            // 发送消息提醒
            $arrNotice = array();
            $arrNotice['user_id'] = $user_id;
            $arrNotice['title'] = '注册送币活动';
            $arrNotice['content'] = '注册送' . $sendInfo['register_coin'] . '娃娃币';
            $arrNotice['desc'] = '注册送' . $sendInfo['register_coin'] . '娃娃币';
            $arrNotice['ctime'] = time();
            DI()->notorm->notice->insert($arrNotice);
            /* 友盟推送 */
//            $row = DI()->notorm->users->where('id='.$user_id)->select('androidtoken,iphonetoken')->fetchOne();
//            $this->umengpush($row['androidtoken'],$row['iphonetoken'],$arrNotice['title'],$arrNotice['content']);

            //流水表
            $insert=array("type"=>'income',"action"=>'regcoin',"uid"=>$user_id,"touid"=>0,"giftid"=>0,"giftcount"=>0,"totalcoin"=>$sendInfo['register_coin'],"givecoin"=>0,"giveclaw"=>0,"realmoney"=>0,"givemoney"=>0,"showid"=>0,"addtime"=>time() );
            DI()->notorm->users_coinrecord->insert($insert);
        }
    }

    /**
     * 友盟推送
     * @param $androidtoken
     * @param $iphonetoken
     * @param $title
     * @param $message
     */
    protected function umengpush($androidtoken, $iphonetoken, $title, $message){
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidBroadcast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidFilecast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidGroupcast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidUnicast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidCustomizedcast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSBroadcast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSFilecast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSGroupcast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSUnicast.php');
        require_once('./../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSCustomizedcast.php');

        if(!empty($androidtoken)){
            try {
                $unicast = new \AndroidUnicast();
                $unicast->setAppMasterSecret('yftyxfo9mce09rhwt69l3t5uurku1kqc');
                $unicast->setPredefinedKeyValue("appkey",           '5a1e663ef29d986cf9000160');
                $unicast->setPredefinedKeyValue("timestamp",        time());
                // Set your device tokens here
                $unicast->setPredefinedKeyValue("device_tokens",    $androidtoken);
                $unicast->setPredefinedKeyValue("ticker",           $title);
                $unicast->setPredefinedKeyValue("title",            $title);
                $unicast->setPredefinedKeyValue("text",             $message);
                $unicast->setPredefinedKeyValue("after_open",       "go_custom");
                $unicast->setPredefinedKeyValue("custom",       "go_custom");
                $unicast->setPredefinedKeyValue('icon', 'small_app_logo');
                $unicast->setPredefinedKeyValue('largeIcon', 'app_logo');
                //$unicast-?setPredefinedKeyValue('img', 'http://');
                // Set 'production_mode' to 'false' if it's a test device.
                // For how to register a test device, please see the developer doc.
                $unicast->setPredefinedKeyValue("production_mode", "true");
                // Set extra fields
                //$unicast->setExtraField("id", $v['id']);
                //$unicast->setExtraField("order_id", $order_id);
                $unicast->send();
            } catch (Exception $e) {
                //
            }
        }
        if(!empty($iphonetoken)){
            try {
                $unicast = new \IOSUnicast();
                $unicast->setAppMasterSecret('1awu0wjgjxv5w0wfupbpjgivnbwpufrl');
                $unicast->setPredefinedKeyValue("appkey",           '5a1e45a08f4a9d570c000142');
                $unicast->setPredefinedKeyValue("timestamp",        time());
                // Set your device tokens here
                $unicast->setPredefinedKeyValue("device_tokens",    $iphonetoken);
                $unicast->setPredefinedKeyValue("alert", $message);
                $unicast->setPredefinedKeyValue("badge", 0);
                $unicast->setPredefinedKeyValue("sound", "chime");
                // Set 'production_mode' to 'true' if your app is under production mode
                $unicast->setPredefinedKeyValue("production_mode", "false");
                // Set customized fields
                //$unicast->setCustomizedField("id", $v['id']);
                //$unicast->setCustomizedField("order_id", $order_id);
                $unicast->send();
            } catch (Exception $e) {
                //
            }
        }
    }

}
