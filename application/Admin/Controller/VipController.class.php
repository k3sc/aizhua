<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class VipController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
	}
	function index(){
		$list=M("users_vip vip")->order("id DESC")->select();
 		foreach($list as $k => $t){
			$c = M("users")->where("id = ".$t['uid'])->find();
			$list[$k]["user_nicename"] = $c["user_nicename"];
		}
		$this->assign("list",$list); 
		$this->display();
	}
 	function edit($uid){
		$data = M("users_vip")->where("uid = ".$uid)->find();
		$this->assign("data",$data);
		$this->display();
	}
	function p_edit($uid){
		$add=array(
			"buytime"	=>	time(),
			"endtime"	=>	strtotime($_POST['endtime']),
		);
		
		$where['uid'] = $uid;
		$result=M("users_vip")->data($add)->where($where)->save();

		if($result!==false){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}
	}	
	
	function add(){
		$this->display();
	}	
	function p_add(){
		$add=array(
			"uid"	=>	$_POST['uid'],
			"buytime"	=>	time(),
			"endtime"	=>	strtotime($_POST['endtime']),
		);
		
		$data = M("users")->where("id = ".$add['uid'])->find();
		$data1 = M("users_vip")->where("uid = ".$add['uid'])->find();
		if($data1){
			$this->error("已有会员信息");
		}
		if(empty($data)){
			$this->error("没有该会员");
		}else{
			$result=M("users_vip")->data($add)->add();
			if($result!==false){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}
	}
	
	//vip配置
	function vip(){
		$data = M("vip")->where("id = 1")->find();
		$this->assign("data",$data);
		$this->display();
	}		
	function p_vip(){
		$add=array(
			"thumb"	=>	$_POST['thumb'],
			"coin"	=>	$_POST['coin'],
			"name"	=>	$_POST['name']
		);
		$where['id'] = 1;
		$result=M("vip")->data($add)->where($where)->save();
		if($result!==false){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}		
		
	}	 
}