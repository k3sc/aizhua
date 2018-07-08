<?php

/**
 * 充值规则
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ChargerulesController extends AdminbaseController {


	//充值规则列表		
    function index(){
	
    	$rules=M("charge_rules");
    	$count=$rules->count();
    	$page = $this->page($count, 20);
    	$lists = $rules
				//->where()
				->order("id desc")
				->limit($page->firstRow . ',' . $page->listRows)
				->select();
		foreach ($lists as $key => $value) {
			$name = M('give_gift')->where('id='.$value['give'].'')->getField('name');
			$lists[$key]['gift_name'] = $name;
		}
    	$this->assign('lists', $lists);
    	$this->assign('count',$count);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }		
		
	//充值规则删除
	function del(){
		$id=intval($_GET['id']);
		if($id){
			$result=M("charge_rules")->where("id='{$id}'")->delete();				
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
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("charge_rules")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }	
	
	//充值规则添加显示页
    function add(){
    	$gift = M('give_gift')->select();
    	$this->assign('gift',$gift);
		$this->display();
    }	

	//充值规则添加
	function do_add(){
		if(IS_POST){
    		$coin   = I('coin') ? (int)I('coin') : '';			//充值的娃娃币
    		$money  = I('money') ? floatval(I('money')) : '';	//充值的金额
    		$g_id   = I('give') ? I('give') : 0;				//赠送的礼品ID
    		$number = I('number') ? (int)I('number') : 0;		//赠送的礼品数量
    		$claw   = I('claw') ? I('claw') : 0;				//赠送的甩爪次数
//    		$first  = I('firstgive') ? (int)I('firstgive') : 0; //首冲赠送的娃娃币
            $give_coin = I('give_coin',0,'intval');
            if( $number > 0 && !$g_id )$this->error('未选择礼品');
    		if($coin === '') $this->error('充值的娃娃币不能为空');
    		if($money === '') $this->error('充值的金额不能为空');
    		if($coin <= 0 || $money <= 0) $this->error('充值的娃娃币或充值的金额不能小于0');
    		if($g_id == 0 && $number >0) $this->error('输入赠送的礼品数量时必须要选择赠送的礼品');

    		$data['coin'] 	  = $coin;
    		$data['money']    = $money;
    		$data['give']	  = $g_id;
    		$data['number']   = $number;
//    		$data['firstgive']= $first;
    		$data['claw']	  = $claw;
    		$data['addtime']  = time();
    		$data['give_coin']  = $give_coin;
    		$result = M('charge_rules')->add($data);
    		if($result){
    			$this->success('添加充值规则成功','Admin/Chargerules/index');
    		}else{
    			$this->error('添加充值规则失败','Admin/Chargerules/index');
    		}
    	}
    }

    function edit(){
		$id=intval($_GET['id']);
		if($id){
			$rules	= M("charge_rules")->where("id='{$id}'")->find();
			$gift   = M('give_gift')->select();
			$string = '';
			foreach ($gift as $key => $value) {
				if($value['id'] == $rules['give']){
					$string .= '<input type="radio" class="gift" name="give" value="'.$value['id'].'" checked>'.'<img src='.$value['img'].' style="width:30px;height:30px;">'.$value['name'].'( '.str_pad($value['id'],4,"0",STR_PAD_LEFT).' )'.'&nbsp;&nbsp;';
				}else{
                    $string .= '<input type="radio" class="gift" name="give" value="'.$value['id'].'">'.'<img src='.$value['img'].' style="width:30px;height:30px;">'.$value['name'].'( '.str_pad($value['id'],4,"0",STR_PAD_LEFT).' )'.'&nbsp;&nbsp;';
				}
				if( $key % 5 == 0 && $key != 0 )$string .= '<br/><br/>';
			}
			$this->assign('gift',$string);
			$this->assign('rules', $rules);						
		}else{				
			$this->error('数据传入失败！');
		}								      	
    	$this->display();
    }		
	
	function do_edit(){
		if(IS_POST){
			$coin   = I('coin') ? (int)I('coin') : '';			//充值的娃娃币
    		$money  = I('money') ? floatval(I('money')) : '';	//充值的金额
    		$g_id   = I('give') ? I('give') : 0;				//赠送的礼品ID
    		$number = I('number') ? (int)I('number') : 0;		//赠送的礼品数量
    		$claw   = I('claw') ? I('claw') : 0;				//赠送的甩爪次数
//    		$first  = I('firstgive') ? (int)I('firstgive') : 0; //首冲赠送的娃娃币
            $give_coin = I('give_coin',0,'intval');
            if( $number > 0 && !$g_id )$this->error('未选择礼品');
    		if($coin === '') $this->error('充值的娃娃币不能为空');
    		if($money === '') $this->error('充值的金额不能为空');
    		if($coin <= 0 || $money <= 0) $this->error('充值的娃娃币或充值的金额不能小于0');
    		if($g_id == 0 && $number >0) $this->error('输入赠送的礼品数量时必须要选择赠送的礼品');

    		$data['coin'] 	  = $coin;
    		$data['money']    = $money;
    		$data['give']	  = $g_id;
    		$data['number']   = $number;
//    		$data['firstgive']= $first;
    		$data['claw']	  = $claw;
    		$data['give_coin']	  = $give_coin;
    		$data['addtime']	  = time();

			M("charge_rules")->where('id='.I('id').'')->save($data);
			if(mysqli_affected_rows() >= 0){
				$this->success('修改成功','Admin/Chargerules/index');
			}else{
				$this->error('修改失败','Admin/Chargerules/index');
			}
		}	
    }				
}
