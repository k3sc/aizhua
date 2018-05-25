<?php

/**
 * 兑换礼品管理
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class GiftController extends AdminbaseController
{

    public function default2()
    {
    }

    //上传多张图片
    public function getFile()
    {
        if (empty($_FILES)) {
            $this->error('轮播图不能为空');
        }
        $file = $this->uploadImage($_FILES);
        $this->ajaxReturn($file);
    }

    //兑换礼品列表
    public function index()
    {
        $map = ' 1=1 ';
        $type = I('type_id');
        $keyword = I('keyword');
        $quantity1 = I('quantity1');
        $quantity2 = I('quantity2');
        $class = M('gift_class')->select();
        $string = '';
        foreach ($class as $key => $value) {
            if ($type !== '' && $type == $value['id']) {
                $string .= "<option  value = '{$value['id']}' selected = 'selected'>{$value['name']}</option >";
            } else {
                $string .= "<option  value = '{$value['id']}' >{$value['name']}</option >";
            }
        }
        if ($type) {
            $map .= ' and a.type_id =' . $type;
            $this->assign('type_id', $type);
        }
        if ($keyword) {
            $map .= ' and a.id="' . $keyword . '" or a.name like "%' . $keyword . '%"';
            $this->assign('keyword', $keyword);
        }
        if ($quantity1 && $quantity2) {
            $map .= ' and a.quantity >= ' . $quantity1 . ' and a.quantity <= ' . $quantity2;
            $this->assign('quantity1', $quantity1);
            $this->assign('quantity2', $quantity2);
        }
        //礼品列表
        $gift_model = M("give_gift as a");
        $count = $gift_model->count();
        $page = $this->page($count, 20);
        $lists = $gift_model
            ->join("left join cmf_gift_class as b on b.id=a.type_id")
            ->field("a.id,a.is_show,a.cost,a.name,a.convert_num,a.quantity,b.name as class_name,a.shipment_num,a.ctime,a.img")
            ->where($map)
            ->order("a.id desc")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $this->assign('type', $string);//礼品分类
        $this->assign('lists', $lists);

        $this->assign("page", $page->show('Admin'));

        $this->display();
    }
	public function record()
    {
        $giftId = I('gift_id');
        $nickname = I('nickname');
        $userId = I('userId');
        $status = I('status');
        $class = M('give_gift')->select();
        $string = '';
        foreach ($class as $key => $value) {
            if ($giftId !== '' && $giftId == $value['id']) {
                $string .= "<option  value = '{$value['id']}' selected = 'selected'>{$value['name']}</option >";
            } else {
                $string .= "<option  value = '{$value['id']}' >{$value['name']}</option >";
            }
        }

        $yjstring = '';
        $ss =[1=>'寄存中',2=>'待邮寄',3=>'已发货',4=>'已收货'];
        foreach ($ss as $key => $value) {
            if ($status !== '' && $status == $key) {
                $yjstring .= "<option  value = '{$key}' selected = 'selected'>{$value}</option >";
            } else {
                $yjstring .= "<option  value = '{$key}' >{$value}</option >";
            }
        }



        $map = ' 1 ';
        if ($giftId) {
            $map .= ' and b.id =' . $giftId;
            $this->assign('giftId', $giftId);
            $_GET['giftId'] = $giftId;
        }
        if ($nickname) {
            $map .= ' and d.user_nicename like "%' . $nickname . '%"';
            $this->assign('nickname', $nickname);
            $_GET['nickname'] = $nickname;
        }
        if ($status) {
            $map .= ' and c.type = "' . $status . '"';
            $this->assign('status', $status);
            $_GET['status'] = $status;
        }
        if ($userId) {
            $map .= ' and d.id = ' . $userId;
            $this->assign('userId', $userId);
            $_GET['user_id']=$userId;
        }
        $gift_model = M('users_convert')->alias('a')
            ->join('cmf_give_gift as b on b.id=a.gift_id')//礼品
            ->join(' cmf_users as d on a.user_id =d.id')
            ->join('left join cmf_users_gift as c on c.covert_id=a.gift_id')//cmf_users_gift 查询邮寄
            ->field('a.id,a.user_id,a.gift_id,b.name,b.img,a.ctime,a.body_id,a.number,c.type,d.user_nicename')
            ->where($map);
        $count = $gift_model->where($map)->count();
        $page = $this->page($count, 20);
        $list = M('users_convert')->alias('a')
            ->join('cmf_give_gift as b on b.id=a.gift_id')//礼品
                ->join(' cmf_users as d on a.user_id =d.id')
            ->join('left join cmf_users_gift as c on c.covert_id=a.id')//cmf_users_gift 查询邮寄
            ->field('a.id,a.user_id,a.gift_id,b.name,b.img,a.ctime,a.body_id,a.number,c.type,d.user_nicename')
            ->where($map)
            ->order('a.ctime desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $data = [];//echo M('users_convert')->_sql();exit;
        if ($list) {
            foreach ($list as $key => $value) {
                $arr = explode(',', $value['img']);
                $bodyid = explode(',', $value['body_id']);
                $bodynum = explode(',', $value['number']);
                $data[$key]['id'] = $value['id'];
                $data[$key]['user_id'] = $value['user_id'];
                $data[$key]['gift_id'] = $value['gift_id'];
                $data[$key]['name'] = $value['name'];
                $data[$key]['user_nickname'] = $value['user_nicename'];
                $data[$key]['img'] = $arr[0];
                $data[$key]['number'] = 1;
                $data[$key]['ctime'] = $value['ctime'];
		$data[$key]['statusTxt'] = $ss[$value['type']];
                foreach ($bodyid as $k => $v) {
                    //获取娃娃的名称和ID
                    $body = M('gift')->where("id={$v}")->find();
                    $data[$key]['body'][$k]['body_id'] = $body['id'];
                    $data[$key]['body'][$k]['body_name'] = $body['giftname'];
                    $data[$key]['body'][$k]['body_num'] = $bodynum[$k];
                }
            }
        }

        $this->assign('data', $data);//礼品分类
        $this->assign('gift', $string);//礼品分类
        $this->assign('statuslist', $yjstring);//礼品分类


        $this->assign("page", $page->show('Admin'));

        $this->display();
    }


    /**
     * 上架/下架
     */
    public function isshow()
    {
        $id = I('id');
        $action = I('action');
        if ($id) {
            M('give_gift')->where(['id' => $id])->save(['is_show' => $action]);
        }
        $this->redirect('Gift/index');
    }


    //查看兑换礼品的详情
    public function look()
    {
        $id = I('id');
        if (empty($id)) {
            $this->error('ID不能为空');
        }
        $gift = M('give_gift')->where("id={$id}")->find();
        $string1 = '';
        $string2 = '';
        foreach (explode(',', $gift['body_id']) as $key => $value) {
            if (!empty($value)) {
                $body = M('gift')->where("id={$value}")->find();
                if ($key % 6 == 0) {
                    $string2 .= "<br/>";
                }
                $string2 .= "<input type='checkbox' name='body_id[]'  value = '{$value}' checked='checked' />{$body['giftname']} &nbsp;&nbsp;";
            }


        }
        $class = M('gift_class')->where("id={$gift['type_id']}")->find();
        $gift['type_id'] = $class['name'];
        $gift['body_id'] = $string2;
        // echo '<pre>';
        // print_r($gift);
        $this->assign('data', $gift);
        $this->display();
    }

    //兑换礼品的删除
    function del()
    {
        $id = I('id');
        if ($id) {
            $result = M("give_gift")->where("id={$id}")->delete();
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    //兑换礼品的添加查看
    function add()
    {
        $map['convert'] = ['neq', 1];
        $string1 = '';
        $string2 = '';
        $string3 = '';
        $id = I('id') ? I('id') : '';
        $name = I('name') ? I('name') : '';
        $type = I('type') ? I('type') : '';
        $tage = I('tage') ? I('tage') : '';
        if ($id !== '') {
            $map['id'] = ['eq', $id];//娃娃编号搜索
            $this->assign("id", $id);
        }
        if ($name !== '') {
            $map['giftname'] = ['eq', $name];//娃娃名称搜索
            $this->assign("name", $name);
        }
        if ($type !== '') {
            $map['type_id'] = ['eq', $type];//娃娃分类搜索
        }
        if ($tage !== '') {
            $map['label_id'] = ['eq', $tage];//娃娃标签搜索
        }

        //娃娃标签
        $bodyTage = M('gift_label')->select();
        foreach ($bodyTage as $key => $value) {
            if ($value['id'] == $tage) {
                $string3 .= "<option  value = '{$value['id']}' selected = 'selected'>{$value['name']}</option >";
            } else {
                $string3 .= "<option  value = '{$value['id']}'>{$value['name']}</option >";
            }
        }
        //娃娃的分类
        $bodyType = M('gift_type')->select();
        foreach ($bodyType as $key => $value) {
            if ($value['id'] == $type) {
                $string2 .= "<option  value = '{$value['id']}' selected = 'selected'>{$value['name']}</option >";
            } else {
                $string2 .= "<option  value = '{$value['id']}'>{$value['name']}</option >";
            }
        }
        // var_dump($map)exit;
        //兑换礼品分类
        $class = M('gift_class')->select();
        foreach ($class as $key => $value) {
            $string1 .= "<option  value = '{$value['id']}' >{$value['name']}</option >";
        }
        //娃娃列表
        $model = M('gift');
        $count = $model->order('addtime desc')->where($map)->count();
        $page = $this->page($count, 6);
        $list = $model->order('addtime desc')->where($map)->limit($page->firstRow . ',' . $page->listRows)->select();

        foreach ($list as $key => $value) {
            $string = '';
            $arr = explode(',', $value['convert_lipin']);
            foreach ($arr as $k => $v) {
                $gift = M('give_gift')->where("id='{$v}'")->find();
                $string .= ' ' . $gift['name'] . ' ,';
            }
            $label = M('gift_label')->where("id='{$value['label_id']}'")->find();
            $type = M('gift_type')->where("id='{$value['type_id']}'")->find();
            $list[$key]['label_id'] = $label['name'];
            $list[$key]['type_id'] = $type['name'];
            $list[$key]['convert_lipin'] = substr($string, 0, -1);
        }
        $this->assign('list', $list);               //娃娃列表内容
        $this->assign('count', $count);               //娃娃列表总数
        $this->assign('page', $page->show('Admin'));//娃娃列表分页
        $this->assign('class', $string1);           //兑换礼品分类
        $this->assign('bodyType', $string2);           //娃娃列表分类
        $this->assign('bodyTage', $string3);           //娃娃标签
        $this->display();
    }

    //兑换礼品的添加
    public function add_post()
    {
        if (IS_POST) {
            $post = I('post.');
            $gift = M("give_gift");
            if (in_array('', $post)) $this->error('内容不能为空');
            if (empty($post['body_id'])) $this->error('请选择可兑换礼品的娃娃');
            //判断是否是重复的礼品
            $name = $gift->where("name='{$post['name']}' and is_del=0")->find();
            if ($name) {
                $this->error('此礼品已存在了，请重新输入');
            }
            if (is_numeric($post['convert_num']) === false) {
                $this->error('可兑换的娃娃数量只能是数字类型');
            }
            // foreach ($post['file'] as $key => $value) {
            // 	$post['file'][$key] = strtr($value,"sttp","http");
            // }
            $update = $gift->where("name='{$post['name']}' and is_del=1")->find();
            unset($_SESSION['cost']['value']);
            unset($_SESSION['gift_name']['value']);
            unset($_SESSION['quantity']['value']);
            unset($_SESSION['introduce']['value']);
            unset($_SESSION['BodyName']);
            unset($_SESSION['convert_num']['value']);
            $data['cost']        = $post['cost'];
            $data['type_id']     = $post['type_id'];             //礼品分类ID
            $data['name']        = $post['gift_name'];             //礼品名称
            $data['quantity']    = $post['quantity'];             //礼品库存
            $data['introduce']   = $post['introduce'] ?: '';             //礼品介绍
            $data['convert_num'] = $post['convert_num'];             //要兑换礼品的娃数量
            $data['img'] = $post['img'];                     //礼品图片
            $data['content'] = $post['content'];                 //礼品兑换的详情图片
            $data['body_id'] = implode(',', $post['body_id']); //能兑换礼品的娃娃的ID
	   
            if ($update) {
                $data['is_del'] = 0;
                $result = $gift->where("id={$update['id']}")->save($data);
                if ($result) {
                    $this->success('添加成功');
                    exit;
                } else {
                    $this->error('添加失败');
                    exit;
                }
            } else {
                $data['ctime'] = time();
                $result = $gift->add($data);
                if ($result) {
			
		    /* 关联礼品 */
		    $ids = $result.',';
		    $sql = "update cmf_gift set convert_lipin = CONCAT('".$ids."',convert_lipin) where id in ($data[body_id])";
		    M()->execute($sql);
			
                    $this->success('添加成功');
                    exit;
                } else {
                    $this->error('添加失败');
                    exit;
                }
            }

        }
    }


    //兑换礼品编辑的显示
    function edit()
    {
        $id = I('id');
        $gift = M('give_gift')->where("id={$id}")->find();
        $string1 = '';
        $string2 = '<input type=\'checkbox\' id=\'all\'>全选<br/><br/>';
        // $body = M('gift')->where(['convert' => ['neq', 1]])->select();
        $sql = "select * from cmf_gift where FIND_IN_SET('3',`convert`)";
        $body = M()->query($sql);
        $bodyId = explode(',', $gift['body_id']);

        //兑换礼品的娃娃
        foreach ($body as $key => $value) {
            if ($key % 6 == 0 && $key != 0) {
                $string2 .= "<br/><br/>";
            }
            if (in_array($value['id'], $bodyId)) {
                $string2 .= "<input type='checkbox' name='body_id[]'  value = '{$value['id']}' checked='checked' /><img style='width: 25px;height: 25px;' src='{$value['gifticon']}'>{$value['giftname']}( ID:".str_pad($value['id'],4,"0",STR_PAD_LEFT)." ) &nbsp;&nbsp;&nbsp;&nbsp;";
            } else {
                $string2 .= "<input type='checkbox' name='body_id[]'  value = '{$value['id']}'/><img style='width: 25px;height: 25px;' src='{$value['gifticon']}'>{$value['giftname']}( ID:".str_pad($value['id'],4,"0",STR_PAD_LEFT)." ) &nbsp;&nbsp;&nbsp;&nbsp;";
            }
        }
        //礼品分类
        $class = M('gift_class')->select();
        foreach ($class as $key => $value) {
            if ($value['id'] == $gift['type_id']) {
                $string1 .= "<option  value = '{$value['id']}' selected='selected' />{$value['name']}</option >";
            } else {
                $string1 .= "<option  value = '{$value['id']}'/>{$value['name']}</option >";
            }

        }
        $gift['body_id'] = $string2;
        //echo '<pre>';
        //print_r($bodyId);
        //print_r($string2);
        $this->assign('class', $string1);
        $this->assign('data', $gift);
        $this->display();
    }

    //兑换礼品的编辑
    function edit_post()
    {
        if (IS_POST) {
            $post = I('post.');
            $gift = M("give_gift");
            if (in_array('', $post)) {
                $this->error('内容不能为空');
            }
	    $row = $gift->find($post['id']);//print_r($row);
            $old = $row['body_id'];//echo $old."<br>";
            $new = isset($post['body_id']) ? implode(',', $post['body_id']) : '';
            $oldArray = explode(',',$old);
            $newArray = explode(',',$new);
            $del = array_filter(array_diff($oldArray, $newArray));
            $add = array_filter(array_diff($newArray,$oldArray));

            if (is_numeric($post['convert_num']) === false) {
                $this->error('可兑换的娃娃数量只能是数字类型');
            }
            // foreach ($post['file'] as $key => $value) {
            // 	$post['file'][$key] = strtr($value,"sttp","http");
            // }
            $data['cost']         = $post['cost'];
            $data['type_id']      = $post['type_id'];             //礼品分类ID
            $data['name']         = $post['name'];                 //礼品名称
            $data['quantity']     = $post['quantity'];             //礼品库存
            $data['introduce']    = $post['introduce'] ?:'';             //礼品介绍
            $data['convert_num']  = $post['convert_num'];             //要兑换礼品的娃数量
            $data['img']          = $post['img'];                     //礼品图片
            $data['content']      = $post['content'];                 //礼品兑换的详情图片
            $data['body_id']      = $post['body_id'] ? implode(',', $post['body_id']) : ''; //能兑换礼品的娃娃的ID
            $data['shipment_num'] = $post['shipment_num'];         //出库数量
	    
	    //if($data['body_id']){
	    //	/* 关联礼品 */
	    //    $ids = $post['id'].',';
	    //    $sql = "update cmf_gift set convert_lipin = CONCAT('".$ids."',convert_lipin) where id in ($data[body_id])";
	    //    M()->execute($sql);
	    //}
           if ($del)
            {

                    $result = M('gift')->where('id in('.implode(',',$del).')')->select();
                    foreach ($result as $row)
                    {
                        $ids = explode(',',$row['convert_lipin']);
                        $ids = array_diff($ids,[$post['id']]);
                        $sql ='update cmf_gift set convert_lipin = "'.implode(',',array_unique($ids)).'" where id = '.$row['id'];
                        M()->execute($sql);
                    }


            }

            if ($add)
            {

                    $result = M('gift')->where('id in('.implode(',',$add).')')->select();
                    foreach ($result as $row)
                    {
                        $ids = explode(',',$row['convert_lipin']);
                        $ids = array_merge($ids,[$post['id']]);
                        $sql ='update cmf_gift set convert_lipin = "'.implode(',',array_unique($ids)).'" where id = '.$row['id'];
                        M()->execute($sql);
                    }


            }

	    
            $result = $gift->where("id={$post['id']}")->save($data);
            if ($result) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }
    }

    public function ajax()
    {
        session_start();
        // $post = explode('&',$POST);
        // $this->ajaxReturn($post);
        // foreach ($variable as $key => $value) {
        // 	# code...
        // }
        // $string  ='';
        // $model   =  M('gift')->select();
        // foreach ($model as $key => $value) {
        // 	if($value['id'] == $_GET['bodyId']){
        // 		$string .= "<input name='body_id[]' type='checkbox' value='{$value['id']}' checked='checked'/>{$value['giftname']}&nbsp;";
        // 	}
        // }

        $_SESSION[$_GET['name']]['value'] = $_GET['value'];
        $_SESSION['BodyName'][$_GET['bodyId']] = $_GET['bodyBame'];
        //unset();
        // if($_GET['delId']){
        //unset($_SESSION['BodyName'][$_GET['delId']);
        // }

        // session($_SESSION['BodyName'][$_GET['delId'],null);
        $this->ajaxReturn($_GET['delId']);

        // unsert($_SESSION['BodyName'][$_GET['delId']]);
        // $this->ajaxReturn($_SESSION['BodyName'][$_GET['delId']);

    }

    public function clear_session()
    {
        unset($_SESSION['BodyName']);
        echo 1;
    }

    // 礼品分类列表
    public function type_list()
    {
        $gift_model = M("gift_class");
        $count = $gift_model->count();
        $page = $this->page($count, 20);
        $lists = $gift_model->alias('a')
            ->order("id DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign('lists', $lists);
        $this->assign("page", $page->show('Admin'));

        $this->display();
    }

    //娃娃分类的删除
    function del_type()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $result = M("gift_class")->delete($id);
            if ($result) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    // 娃娃分类添加/编辑
    public function type_add()
    {
        if (IS_POST) {
            $gift_type = M('gift_class');
            $post = I('post.');
            if (!$post['name']) {
                $this->error('分类名称不能为空');
            }
            $name = $gift_type->where("name='{$post['name']}'")->find();
            if ($name) {
                $this->error('此分类的名称已存在了，请重新输入');
            }

            if ($post['id']) {
                if ($gift_type->where('id=' . $post['id'])->save($post)) {
                    $this->success('修改成功', U('type_list'));
                    exit;
                }
            } else {
                if ($gift_type->add($post)) {
                    $this->success('添加成功', U('type_list'));
                    exit;
                } else {
                    $this->error('添加失败');
                    exit;
                }
            }
        }
        $id = intval(I('id'));
        if ($id) {
            $arrInfo = M('gift_class')->where('id=' . $id)->find();
            $this->assign('arrInfo', $arrInfo);
        }

        $this->display();
    }

}
