<?php

/**
 * 经验等级
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class LevelController extends AdminbaseController {
	
		protected $experlevel_model;
	
		function _initialize() {
			parent::_initialize();
			$this->experlevel_model = D("Common/Experlevel");

		}	
	
    function experlevel_index(){			
    	$experlevel=$this->experlevel_model;
    	$count=$experlevel->count();
    	$page = $this->page($count, 20);
    	$lists = $experlevel
    	->where()
    	->order("levelid asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
	function experlevel_del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("experlevel")->where("levelid='{$id}'")->delete();				
			if($result!==false){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}		



	function experlevel_add(){
		$this->display();				
	}	
	function experlevel_add_post(){
		if(IS_POST){			
			$experlevel=$this->experlevel_model;
			if($experlevel->create()){
				$experlevel->addtime=time();
				$result=$experlevel->add(); 
				if($result!==false){
					$this->success('添加成功');
				}else{
					$this->error('添加失败');
				}						 
				 
			}else{
				$this->error($this->experlevel_model->getError());
			}
		}			
	}		
	function experlevel_edit(){
		$id=intval($_GET['id']);
		if($id){
			$experlevel=M("experlevel")->where("levelid='{$id}'")->find();
			$this->assign('experlevel', $experlevel);						
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}
	
	function experlevel_edit_post(){
		if(IS_POST){			
			$experlevel=$this->experlevel_model;
			if( $experlevel->create()){
				$experlevel->addtime=time();
				$result=$experlevel->save(); 
				if($result!==false){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}					 
			}else{
				$this->error($this->experlevel_model->getError());
			}

		}			
	}
	
	function author(){
    	$m=M('authorlevel');
    	$count=$m->count();
    	$page = $this->page($count, 20);
    	$lists = $m
    	->order("levelid asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
	}
		
	function author_del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("authorlevel")->where("levelid='{$id}'")->delete();				
			if($result!==false){
				$this->success('删除成功');
			}else{
				$this->error('删除失败');
			}			
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}		



	function author_add(){
		$this->display();				
	}	
	function author_addpost(){
		if(IS_POST){			
			$author=M('authorlevel');
			if($author->create()){
				$author->addtime=time();
				$result=$author->add(); 
				if($result!==false){
					$this->success('添加成功');
				}else{
					$this->error('添加失败');
				}						 
				 
			}else{
				$this->error($m->getError());
			}
		}			
	}		
	function author_edit(){
		$id=intval($_GET['id']);
		if($id){
			$author=M("authorlevel")->where("levelid='{$id}'")->find();
			$this->assign('author', $author);						
		}else{				
			$this->error('数据传入失败！');
		}								  
		$this->display();				
	}
	
	function author_editpost(){
		if(IS_POST){			
			$author=M('authorlevel');
			if( $author->create()){
				$author->addtime=time();
				$result=$author->save(); 
				if($result!==false){
					$this->success('修改成功');
				}else{
					$this->error('修改失败');
				}					 
			}else{
				$this->error($m->getError());
			}

		}			
	}
}
