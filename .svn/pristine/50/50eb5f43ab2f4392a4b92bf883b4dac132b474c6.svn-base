<?php

/**
 * 直播记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MonitorController extends AdminbaseController {
    function index(){

		$config=M("config_private")->where("id='1'")->find();
			
		$this->assign('config', $config);
			
    	$live=M("users_live");
    	$count=$live->where("islive='1'")->count();
    	$page = $this->page($count, 20);
    	$lists = $live
    	->where("islive='1'")
    	->order("starttime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				 $userinfo=M("users")->field("user_nicename")->where("id='{$v['uid']}'")->find();
				 $lists[$k]['userinfo']=$userinfo;
			}
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
	public function full()
	{
		$uid=$_GET['uid'];
		$live=M("users_live")->where("uid='{$uid}' and islive='1'")->find();
		$config=M("config_private")->where("id='1'")->find();	
		if($live['title']=="")
		{
			$live['title']="直播监控后台";
		}
		$this->assign('config', $config);
		$this->assign('live', $live);
		$this->display();
	}
	public function stopRoom(){
		
		$uid=I('uid');
		
		M("users")->where("id='{$uid}'")->save(array("islive"=>0,"showid"=>0));
		$liveinfo=M("users_live")->where(" uid='{$uid}' and islive='1' ")->find();
		M("users_live")->where(" uid='{$uid}'")->delete();
		if($liveinfo){
			$liveinfo['islive']=0;
			$liveinfo['endtime']=time();
			
			$redis = $this->connectionRedis();
			
			$nums=$redis->hget("livenums",$uid);
			$redis->hdel("livenums",$uid);
			$redis -> close();
			
			$liveinfo['nums']=$nums;
			
			M("users_liverecord")->add($liveinfo);
		}
		
		echo $_GET['callback']."({'status':0,'data':{},'info':''})";
		exit;	
		/* echo json_encode(array("status"=>'0','info'=>''));
		exit; */
		
	}		
/* //==========node改====================connectionRedis=========================================== */
		public function connectionRedis(){
			$REDIS_HOST= C('REDIS_HOST');
			$REDIS_AUTH= C('REDIS_AUTH');
			$redis = new \Redis();
			$redis -> connect($REDIS_HOST,6379);
			$redis -> auth($REDIS_AUTH);

			return $redis;
		}
/* //==========node改====================connectionRedis===========================================		 */				
}
