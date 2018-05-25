<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LotteryController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
	}
	function index(){
		$list=M("lottery")->order("lottery_level DESC")->select();

		$this->assign("list",$list); 
		$this->display();
	}
 	function edit($id){
 		$data = M("lottery")->where("id = ".$id)->find();
		$this->assign("data",$data);
		$this->display(); 
	}
	function p_edit($id){
		
		$list=$_POST;
		$where['id'] = $id;
		$data = M("lottery")->where("lottery_level = ".$list['lottery_level'])->find();
		if($data){
			$this->error("奖品等级重复");
		}
		$result=M(lottery)->data($list)->where($where)->save();

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
		$list=$_POST;
		$data = M("lottery")->where("lottery_level = ".$list['lottery_level'])->find();
		if($data1){
			$this->error("奖品等级重复");
		}
	
		$result=M("lottery")->data($list)->add();
		if($result!==false){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}
		
	}
	
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("lottery")->where('id = '.$id)->delete();				
							if($result){
									$this->success('删除成功');
							 }else{
									$this->error('删除失败');
							 }			
					}else{				
						$this->error('数据传入失败！');
					}								  			
		}
}