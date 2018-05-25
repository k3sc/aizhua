<?php

/**
 * 消费记录
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CoinrecordController extends AdminbaseController {
    function index(){

					if($_REQUEST['type']!=''){
						  $map['type']=$_REQUEST['type'];
							$_GET['type']=$_REQUEST['type'];
					 }
					 
					 if($_REQUEST['action']!=''){
						  $map['action']=$_REQUEST['action'];
							$_GET['action']=$_REQUEST['action'];
					 }
					 
				   if($_REQUEST['start_time']!=''){
						  $map['addtime']=array("gt",strtotime($_REQUEST['start_time']));
							$_GET['start_time']=$_REQUEST['start_time'];
					 }
					 
					 if($_REQUEST['end_time']!=''){
						 
						   $map['addtime']=array("lt",strtotime($_REQUEST['end_time']));
							 $_GET['end_time']=$_REQUEST['end_time'];
					 }
					 if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
						 
						 $map['addtime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
						 $_GET['start_time']=$_REQUEST['start_time'];
						 $_GET['end_time']=$_REQUEST['end_time'];
					 }
 
					 if($_REQUEST['uid']!=''){
						 $map['uid']=$_REQUEST['uid']; 
						 $_GET['uid']=$_REQUEST['uid'];
					 }
					  if($_REQUEST['touid']!=''){
						 $map['touid']=$_REQUEST['touid']; 
						 $_GET['touid']=$_REQUEST['touid'];
					 }

			
    	$coin=M("users_coinrecord");
    	$count=$coin->where($map)->count();
    	$page = $this->page($count, 20);
    	$lists = $coin
    	->where($map)
    	->order("addtime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				   $userinfo=M("users")->field("user_nicename")->where("id='$v[uid]'")->find();
				   $lists[$k]['userinfo']= $userinfo;
					 $touserinfo=M("users")->field("user_nicename")->where("id='$v[touid]'")->find();
				   $lists[$k]['touserinfo']= $touserinfo;
					 
					 $giftinfo = null;
					 if($v['giftid']){
						 if($v['action'] == 'coin'){
							$giftinfo=M("give_gift")->field("name as giftname")->where("id='$v[giftid]'")->find();
						 }else{
							$giftinfo=M("gift")->field("giftname")->where("id='$v[giftid]'")->find();
						 }
					   	  $lists[$k]['giftinfo']= $giftinfo;
					 }
					 
			}
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("users_coinrecord")->delete($id);				
							if($result){
									$this->success('删除成功');
							 }else{
									$this->error('删除失败');
							 }			
					}else{				
						$this->error('数据传入失败！');
					}								  
					$this->display();				
		}		

    	
}
