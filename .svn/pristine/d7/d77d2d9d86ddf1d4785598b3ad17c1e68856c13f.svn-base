<?php

/**
 * 直播记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LiveingController extends AdminbaseController {
    function index(){
			$config=M("config")->where("id='1'")->find();
			$map=array();
			$map['islive']=1;
		   if($_REQUEST['start_time']!=''){
				  $map['starttime']=array("gt",strtotime($_REQUEST['start_time']));
					$_GET['start_time']=$_REQUEST['start_time'];
			 }
			 
			 if($_REQUEST['end_time']!=''){
				 
				   $map['starttime']=array("lt",strtotime($_REQUEST['end_time']));
					 $_GET['end_time']=$_REQUEST['end_time'];
			 }
			 if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
				 
				 $map['starttime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
				 $_GET['start_time']=$_REQUEST['start_time'];
				 $_GET['end_time']=$_REQUEST['end_time'];
			 }

			 if($_REQUEST['keyword']!=''){
				 $map['uid']=$_REQUEST['keyword']; 
				 $_GET['keyword']=$_REQUEST['keyword'];
			 }
			
	
			
    	$live=M("users_live");
    	$count=$live->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $live
    	->where($map)
    	->order("starttime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				 $userinfo=M("users")->field("user_nicename,recommendorderby")->where("id='{$v['uid']}'")->find();
				 $lists[$k]['userinfo']=$userinfo;
			}
			
    	$this->assign('config', $config);
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
}
