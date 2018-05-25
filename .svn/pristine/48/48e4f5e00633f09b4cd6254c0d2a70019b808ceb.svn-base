<?php
/**
 * 会员等级
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class SetliveController extends HomebaseController {
	
	function index(){
		$uids="10079,10080,10081,10082,10083";
		$nowtime=time();
		$live=M("users_live");
		$rs=M("users")->field("id,user_nicename,avatar,avatar_thumb")->where("id in ({$uids}) and user_type='2'")->select();
		foreach($rs as $k=>$v){
			$stream='5165_'.$v['id'].'_'.$nowtime;
			$data=array(
				"uid"=>$v['id'],
				"user_nicename"=>$v['user_nicename'],
				"avatar"=>$v['avatar'],
				"avatar_thumb"=>$v['avatar_thumb'],
				"showid"=>$nowtime,
				"islive"=>1,
				"starttime"=>$nowtime,
				"endtime"=>0,
				"nums"=>0,
				"title"=>'云豹网络',
				"address"=>'泰安市',
				"province"=>'',
				"city"=>'',
				"light"=>0,
				"groupid"=>'@TGS#a7CZNIJEK',
				"stream"=>$stream,
				"push_url"=>'rtmp://5165.livepush2.myqcloud.com/live/'.$stream.'?bizid=5165&record=flv&txSecret=3fd053770987e522531036c5a315cf85&txTime=584020b4',
				"play_url"=>'http://5165.liveplay.myqcloud.com/live/'.$stream.'.flv',
			);	
			$isexist=$live->where("uid='{$v['id']}'")->find();
			if($isexist){
				$live->where("uid='{$v['id']}'")->save($data);
			}else{
				
				$live->add($data);
			}
			
		}
	
	  echo 'OK';    
	}

}