<?php
		$type=0;
		$app_key = '8253ae3ad6e91b4e222b40a1';
		$master_secret = '2116118c7eaa3689f2724d01';
		/* if($app_key && $master_secret && $type==0){ */
			require __DIR__ . '/JPush/autoload.php';

			// 初始化
			$client = new \JPush\Client($app_key, $master_secret,null);
			
			$anthorinfo=array(
				"uid"=>'111',
				"avatar"=>'222',
				"avatar_thumb"=>'222',
				"user_nicename"=>'444',
				"title"=>'555',
				"city"=>'666',
				"stream"=>'777',
				"pull"=>'888',
				"thumb"=>'999',
			);

			/* $fansids = $domain->getFansIds($uid); 
			$uids=$this->array_column2($fansids,'uid');
			$nums=count($uids);	

			for($i=0;$i<$nums;){
				$alias=array();
				for($n=0;$n<1000;$n++,$i++){
					if($uids[$i]){
						$alias[]=$uids[$i].'PUSH';								 
					}else{
						continue;
					}
				}	 */ 
				$alias=array('66PUSH');
				try{		 
					$result = $client->push()
							->setPlatform('all')
							->addAlias($alias)
							->setNotificationAlert('你的好友：'.$anthorinfo['user_nicename'].'正在直播，邀请你一起')
							->iosNotification('你的好友：'.$anthorinfo['user_nicename'].'正在直播，邀请你一起', array(
								'sound' => 'sound.caf',
								'category' => 'jiguang',
								'extras' => array(
									'userinfo' => $anthorinfo
								),
							))
							->androidNotification('你的好友：'.$anthorinfo['user_nicename'].'正在直播，邀请你一起', array(
								'extras' => array(
									'userinfo' => $anthorinfo
								),
							))
							->options(array(
								'sendno' => 100,
								'time_to_live' => 0,
								'apns_production' => false,
							))
							->send();	
							print_r($result);
				} catch (\JPush\Exceptions\APIConnectionException $e) {   
					file_put_contents('./jpush.txt',date('y-m-d h:i:s').'提交参数信息 设备名:'.json_encode($alias)."\r\n",FILE_APPEND);
					file_put_contents('./jpush.txt',date('y-m-d h:i:s').'提交参数信息:'.$e."\r\n",FILE_APPEND);
				}  						
		/* 	}					
		} */
