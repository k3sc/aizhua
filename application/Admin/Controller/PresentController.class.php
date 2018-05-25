<?php

/**
 * 娃娃管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GiftController extends AdminbaseController {

	public function default2(){}

	//娃娃列表
    function index(){

		$gift_sort=M("gift_sort")->getField("id,sortname");
		$gift_sort[0]="默认分类";
		$this->assign('gift_sort', $gift_sort);
		$map 	= "1=1 ";
		$type1  = I('type1') ? I('type1') : '';	
		$keyword= I('keyword') ? I('keyword') : '';
		if($type1 == 1){
			$map .= "and type='{$type1}'";
            $this->assign('type1', $type1);
		}
		if($type1 == 2){
			$map .= "and type='{$type1}'";
            $this->assign('type1', $type1);
		}
		if($keyword !== ''){
			$map .= "and id='{$keyword}' or giftname='{$keyword}'";
			// $map['id'] = array('eq', "$keyword");
			// $map['giftname'] = array('like', "%$keyword%");
			$this->assign('keyword', $keyword);
		}
    	$gift_model=M("gift");
    	$count=$gift_model->count();
    	$page = $this->page($count, 20);
    	$lists = $gift_model
    	->where($map)
    	->order("addtime DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
	
	//娃娃的删除	
	function del(){
	 	$id=intval($_GET['id']);
		if($id){
			$result=M("gift")->delete($id);				
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

    //娃娃排序
    public function listorders() { 
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("gift")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
    

	function add(){
		$gift_sort=M("gift_sort")->getField("id,sortname");
		$this->assign('gift_sort', $gift_sort);					
	
		$this->display();				
	}	

	//娃娃的添加
	function add_post(){
		if(IS_POST){	
			$post = I('post.');	
			$gift = M("gift");
			if(in_array('',$post)){
				$this->error('内容不能为空');
			}	
			$name = $gift->where("giftname='{$post['giftname']}'")->find();
			if($name){
				$this->error('此娃娃的名称已存在了，请重新输入');
			}
			if(is_numeric($post['needcoin']) === false){
				$this->error('可兑换的娃娃数量只能是数字类型');
			}
			$post['addtime'] = time();
			$result=$gift->add($post); 
			if($result){
				$this->success('添加成功');exit;
			}else{
				$this->error('添加失败');exit;
			}
		}			
	}	

	function edit(){
	 	$id=intval($_GET['id']);
			if($id){
				$gift=M("gift")->find($id);
				$this->assign('gift', $gift);						
			}else{				
				$this->error('数据传入失败！');
			}								  
			$this->display();				
	}
	
	//娃娃的编辑
	function edit_post(){
		if(IS_POST){	
			$post = I('post.');	
			$gift = M("gift");
			if(in_array('',$post)){
				$this->error('内容不能为空');
			}	
			$name = $gift->where("giftname='{$post['giftname']}'")->find();
			if($name){
				$this->error('此娃娃的名称已存在了，请重新输入');
			}
			if(is_numeric($post['needcoin']) === false){
				$this->error('可兑换的娃娃数量只能是数字类型');
			}
			$result=$gift->save($post); 
			if($result){
				$this->success('修改成功');
			}else{
				$this->error('修改失败');
			}
		}				
	}
		
    function sort_index(){
	
    	$gift_sort=M("gift_sort");
    	$count=$gift_sort->count();
    	$page = $this->page($count, 20);
    	$lists = $gift_sort
    	->where()
    	->order("orderno asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }		
		
	function sort_del(){
		 	$id=intval($_GET['id']);
				if($id){
					$result=M("gift_sort")->delete($id);				
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
    //排序
    public function sort_listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("gift_sort")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }				
    function sort_add(){
		    	
    	$this->display();
    }		
	function do_sort_add(){

		if(IS_POST){	
	        if($_POST['sortname']==''){
				$this->error('分类名称不能为空');
			}
			$gift_sort=M("gift_sort");
			$gift_sort->create();
			$gift_sort->addtime=time();
			 
			$result=$gift_sort->add(); 
			if($result){
				$this->success('添加成功');
			}else{
				$this->error('添加失败');
			}
		}				
    }		
    function sort_edit(){

			 	$id=intval($_GET['id']);
					if($id){
						$sort	=M("gift_sort")->find($id);
						$this->assign('sort', $sort);						
					}else{				
						$this->error('数据传入失败！');
					}								      	
    	$this->display();
    }			
	function do_sort_edit(){
			if(IS_POST){			
				 $gift_sort=M("gift_sort");
				 $gift_sort->create();
				 $result=$gift_sort->save(); 
				 if($result){
					  $this->success('修改成功');
				 }else{
					  $this->error('修改失败');
				 }
			}	
    }
}
