<?php

/**
 * 提现
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CashController extends AdminbaseController {
    function index(){

					if($_REQUEST['status']!=''){
						  $map['status']=$_REQUEST['status'];
							$_GET['status']=$_REQUEST['status'];
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
 
					 if($_REQUEST['keyword']!=''){
						 $map['uid|orderno|trade_no']=array("like","%".$_REQUEST['keyword']."%"); 
						 $_GET['keyword']=$_REQUEST['keyword'];
					 }		
			
    	$cashrecord=M("users_cashrecord");
    	$count=$cashrecord->where($$map)->count();
    	$page = $this->page($count, 20);
    	$lists = $cashrecord
    	->where($map)
    	->order("addtime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				   $userinfo=M("users")->field("user_nicename")->where("id='$v[uid]'")->find();
				   $lists[$k]['userinfo']= $userinfo;
					 
			}			
			
    	$this->assign('lists', $lists);
    	$this->assign('formget', $_GET);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("users_cashrecord")->delete($id);				
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

		
		function edit(){
			 	$id=intval($_GET['id']);
					if($id){
						$cash=M("users_cashrecord")->find($id);
						$cash['userinfo']=M("users")->field("user_nicename")->where("id='$cash[uid]'")->find();
						$this->assign('cash', $cash);						
					}else{				
						$this->error('数据传入失败！');
					}								  
					$this->display();				
		}
		
		function edit_post(){
				if(IS_POST){		
            if($_POST['status']=='0'){							
							  $this->error('未修改订单状态');			
						}
				
					 $cash=M("users_cashrecord");
					 $cash->create();
					 $cash->uptime=time();
					 $result=$cash->save(); 
					 if($result){
						  if($_POST['status']=='2'){
								 M("users")->where("id='".$_POST['uid']."'")->setInc("votes",$_POST['votes']);
							}
						  $this->success('修改成功',U('Cash/index'));
					 }else{
						  $this->error('修改失败');
					 }
				}			
		}		
    
}
