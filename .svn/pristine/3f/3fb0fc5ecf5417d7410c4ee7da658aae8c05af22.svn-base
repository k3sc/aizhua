<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class TopicController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
	}
	function index(){
		$list=M("topic")->where("type = 0")->order("orderlist DESC")->select();

		$this->assign("list",$list); 
		$this->display();
	}
    //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderlist'] = $r;
            M("topic")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
 	function edit($id){
		$data = M("topic")->where("id = ".$id)->find();
		$this->assign("data",$data);
		$this->display();
	}
	function p_edit($id){
		$add=$_POST;
		$rule = "/[^#]+/";
		preg_match ($rule,$add['name'],$topic);
		$add['name'] = "#".$topic[0]."#";
		

		$where['id'] = $id;
		$result=M("topic")->data($add)->where($where)->save();
echo  M("topic")->getLastSql();
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
		$add=$_POST;
		$rule = "/[^#]+/";
		preg_match ($rule,$add['name'],$topic);
		$add['name'] = "#".$topic[0]."#";		
		$result=M("topic")->data($add)->add();
		if($result!==false){
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}

	}
		 
}