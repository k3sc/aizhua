<?php
/**
 * 直播回放
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class livebackController extends HomebaseController {
	
	/* 
		回调数据格式
		{
				"channel_id": "2121_15919131751",
				"end_time": 1473125627,
				"event_type": 100,
				"file_format": "flv",
				"file_id": "9192487266581821586",
				"file_size": 9749353,
				"sign": "fef79a097458ed80b5f5574cbc13e1fd",
				"start_time": 1473135647,
				"stream_id": "2121_15919131751",
				"t": 1473126233,
				"video_id": "200025724_ac92b781a22c4a3e937c9e61c2624af7",
				"video_url": "http://200025724.vod.myqcloud.com/200025724_ac92b781a22c4a3e937c9e61c2624af7.f0.flv"
		}
	*/
	function index(){
		$request = file_get_contents("php://input");
		//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 callback request:'.$request."\r\n",FILE_APPEND);
		$result = array( 'code' => 0 );    
		$data = json_decode($request, true);

		if(!$data){
			$this->callbacklog("request para json format error");
			$result['code']=4001;
			echo json_encode($result);	
			exit;
		}
		
		if(array_key_exists("t",$data)
				&& array_key_exists("sign",$data)
				&& array_key_exists("event_type",$data) 
				&& array_key_exists("stream_id",$data))
		{
			$check_t = $data['t'];
			$check_sign = $data['sign'];
			$event_type = $data['event_type'];
			$stream_id = $data['stream_id'];
		}else {
			$this->callbacklog("request para error");
			$result['code']=4002;
			echo json_encode($result);	
			exit;
		}
		/* $md5_sign = $this-> GetCallBackSign($check_t);
		if( !($check_sign == $md5_sign) ){
			$this->callbacklog("check_sign error:" . $check_sign . ":" . $md5_sign);
			$result['code']=4003;
			echo json_encode($result);	
			exit;
		}      */   
		
		if($event_type == 100){
			/* 回放回调 */
			if(array_key_exists("video_id",$data) && 
					array_key_exists("video_url",$data) &&
					array_key_exists("start_time",$data) &&
					array_key_exists("end_time",$data) ){
						
				$video_id = $data['video_id'];
				$video_url = $data['video_url'];
				$start_time = $data['start_time'];
				$end_time = $data['end_time'];
			}else{
				$this->callbacklog("request para error:回放信息参数缺少" );
				$result['code']=4002;
				echo json_encode($result);	
				exit;
			}
		}     
		
		if($event_type == 0){        	
			/* 状态回调 关播 */
			$ret=$this->stopRoom('',$stream_id);
		}elseif ($event_type == 1){
			//$ret = $this->dao_live->callBackLiveStatus($stream_id,1);
		}elseif ($event_type == 100){
			$duration = $end_time - $start_time;
			//if ( $duration > 60 ){ 	
				$data=array(
					"video_url"=>$video_url,
					"duration"=>$duration,
					"file_id"=>$video_id,
				);								
				$ret=M("users_liverecord")->where("stream='{$stream_id}'")->save($data);
				$ret=0;
			//}else {
			//	$ret = 0;
			//	$this->callbacklog("tape duration too short:" . strval($duration) ."|" . $stream_id . "|" . $video_id);
			//}
			
		}	
		$result['code']=$ret; 
		echo json_encode($result);	
		exit;

	}
	
	public function GetCallBackSign($txTime){
		$config=M("config")->where("id='1'")->find();
		$md5_val = md5($config['live_push_key'] . strval($txTime));
		return $md5_val;
	}
	
	public function callbacklog($msg){
		//file_put_contents('./callbacklog.txt',date('Y-m-d H:i:s').' 提交参数信息 :'.$msg."\r\n",FILE_APPEND);
	}
	
	public function im(){
		$request = file_get_contents("php://input");
		//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 im request:'.$request."\r\n",FILE_APPEND);
		$data = json_decode($request, true);
		if($data['CallbackCommand']=='Group.CallbackAfterGroupDestroyed' || $data['Info']['Reason']=='LinkClose'){	

			$uid=$data['Info']['Reason']=='LinkClose'?$data['Info']['To_Account']:$data['Owner_Account'];
			$this->stopRoom($uid,'');
		}
		echo '{"ActionStatus": "OK", "ErrorCode": 0,"ErrorInfo": ""}';
		exit;

	}
	
	public function stopRoom($uid='',$stream=''){
		if($uid){
			$where="uid='{$uid}' and islive='1'";
			//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 im:'."\r\n",FILE_APPEND);
		}else{
			$where="stream='{$stream}' and islive='1'";
			//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 callback:'."\r\n",FILE_APPEND);
		}
		$users_live=M("users_live");
			
		$live=$users_live->where($where)->find();
		//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 sql:'.$where."\r\n",FILE_APPEND);
		//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 live:'.json_encode($live)."\r\n",FILE_APPEND);
		if($live){
			require_once("./PhpServerSdk/TimRestApi.php");
			/**
			 * sdkappid 是app的sdkappid
			 * identifier 是用户帐号
			 * private_pem_path 为私钥在本地位置
			 * server_name 是服务类型
			 * command 是具体命令
			 */
			#读取app配置文件
			$config=M("config_private")->where("id='1'")->find();
			$sdkappid = $config["im_sdkappid"];
			$identifier = $config["im_admin"];
			$user_sig = $config["im_user_sig"];

			$api = createRestAPI();
			$api->init($sdkappid, $identifier);
			//托管模式
			$ret = $api->set_user_sig($user_sig);
			if($ret == false){
				//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 :设置usrsig失败, 请确保配置信息正确'."\r\n",FILE_APPEND);
				return 0;
			}else{	
				//访问接口
				$groupid=$live['groupid'];
				$ret = $api->group_destroy_group($groupid);
				//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 ret1:'.json_encode($ret)."\r\n",FILE_APPEND);
				if(gettype($ret) == "string"){
					if(strstr($ret, "not enough")){
						//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 ret1:'.json_encode($ret)."\r\n",FILE_APPEND);
						return 0;
					}
				}
				//结果格式化为json，并打印
				if($ret['ErrorCode']!='0'){
					//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 ret2:'.json_encode($ret)."\r\n",FILE_APPEND);
					return 0;
				}		
				$nowtime=time();
				$users_live->where("uid='{$data['uid']}'")->save( array("islive"=>0,'endtime'=>$nowtime) );

				$data['islive']=0;							
				$data['endtime']=$nowtime;
				M("users_liverecord")->add($data);
				M("users_live_group")->where("groupid='{$data['groupid']}' and liveuid='{$data['uid']}'")->delete();
				//file_put_contents('./im.txt',date('y-m-d H:i:s').' 提交参数信息 request:关闭成功'."\r\n",FILE_APPEND);
			}				
		}		
		return 0;
	}

}