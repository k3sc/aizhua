<?php
/**
 * 设备管理
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/17
 * Time: 10:47
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class DeviceController extends AdminbaseController
{
    protected $device_model;

    function _initialize()
    {
        parent::_initialize();
        $this->device_model = M("device");
    }

    public function index()
    {
    }

    /**
     * 设备列表
     */
    public function device_list()
    {
        $where = ' 1=1 ';
        if( IS_POST ){
            $post = I('post.');
            if( $post['unicode'] ) $where .= ' and a.device_unique_code like "%'.$post['unicode'].'%"';
            if( $post['device_no'] ) $where .= ' and a.deveci_no like "%'.$post['device_no'].'%"';
            if( is_numeric($post['device_addr_id']) ) $where .= ' and a.device_addr_id = "'.$post['device_addr_id'].'"';
            if( is_numeric($post['status']) ) $where .= ' and a.status = "'.$post['status'].'"';
            $this->assign('post',$post);
        }

        $statusArr = ['故障', '正常','离线'];
        $model = $this->device_model;
        $count = $model->count();
        $page = $this->page($count, 20);
        $device_list = M('device as a')
            ->join('left join cmf_gift as c on a.wawa_id = c.id')
            ->join('left join cmf_game_room as d on a.device_unique_code = d.fac_id')
            ->join('left join cmf_device_addr as e on a.device_addr_id = e.id')
            ->field('a.*,c.giftname,c.giftname,c.cost,d.id as room_id,d.room_name,d.room_no,e.addr')
            ->where($where)
            ->order('status')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $addr = M('device_addr')->select();
        $this->assign('addr',$addr);
        $this->assign('page', $page->show('Admin'));
        $this->assign('statusArr', $statusArr);
        $this->assign('device_list', $device_list);
        $this->display();
    }

    public function device_edit()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $game_config = M('game_config')->select();
            $this->assign('game_config', $game_config);
            $wawa = M('gift')->select();
            $this->assign('wawa', $wawa);
            $addr = M('device_addr')->select();
            $this->assign('addr',$addr);
            $device = $this->device_model->find($id);
            $this->assign('device', $device);
            $room = M('game_room')->select();
            $this->assign('room', $room);
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    public function device_edit_post()
    {
        $id = I('get.id');
        if (IS_POST) {
            $post = I('post.');
            $post['ctime'] = time();
            if (in_array('', $post)) $this->error('各项值不能为空');
            $res = M('device')->where(['deveci_no' => $post['deveci_no']])->find();
            if ( $res && $res['id'] != $id ) $this->error('设备编号已存在');
//            if ($post['device_stock'] <= 0) $this->error('库存不能小于0');
//            if ($post['device_stock_predict'] <= 0) $this->error('预警库存不能小于0');
//            if ($post['device_stock'] <= 0) $this->error('库存不能小于0');
//            if ($post['device_stock_predict'] <= 0) $this->error('预警库存不能小于0');
            if ($this->device_model->where(['id' => $post['device_id']])->save($post) !== false) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }
    }


    public function device_del()
    {
        $id = intval($_GET['id']);
        if ($id) {
            if ($this->device_model->where(['id' => $id])->delete() !== false) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    public function device_add()
    {
        $game_config = M('game_config')->select();
        $this->assign('game_config', $game_config);
        $addr = M('device_addr')->select();
        $this->assign('addr',$addr);
        $wawa = M('gift')->select();
        $this->assign('wawa', $wawa);
        $room = M('game_room')->select();
        $this->assign('room', $room);
        $this->display();
    }

    public function device_add_post()
    {
        if (IS_POST) {
            $post = I('post.');
            $post['ctime'] = time();
            if (in_array('', $post)) $this->error('各项值不能为空');
            if (M('device')->where(['device_unique_code' => $post['device_unique_code']])->find()) $this->error('设备唯一码已存在');
            if (M('device')->where(['deveci_no' => $post['deveci_no']])->find()) $this->error('设备编号已存在');
//            if ($post['device_stock'] <= 0) $this->error('库存不能小于0');
//            if ($post['device_stock_predict'] <= 0) $this->error('预警库存不能小于0');
            if ($this->device_model->add($post)) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }
    }

    public function response_ajax()
    {
        $id = I('post.id');
        $data = M('game_config')->find($id);
        $this->ajaxReturn($data);
    }

    // 设备故障列表
    public function device_fault()
    {
        $deveci_no = I('deveci_no');
        if (empty($deveci_no))
            $this->error('设备id不能为空');

        $model = M('fault');
        $count = $model->count();
        $page = $this->page($count, 20);
        $list = $model
            ->field('*')
            ->where('device_no="' . $deveci_no . '"')
            ->order('id desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign('page', $page->show('Admin'));
        $this->assign('list', $list);
        $this->display();
    }

    public function fault_del()
    {
        $id = intval($_GET['id']);
        if ($id) {
            if (M('fault')->where(['id' => $id])->delete() !== false) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    /**
     * 设备所在地
     */
    public function addr(){
        $row = M('device_addr')->select();
        $this->assign('row',$row);
        $this->display();
    }

    public function addr_del()
    {
        $id = intval($_GET['id']);
        if ($id) {
            if (M('device_addr')->where(['id' => $id])->delete() !== false) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    public function addr_add(){
        if( IS_PSOT ){
            $post = I('post.');
            if( $post ){
                if( M('device_addr')->where(['addr'=>$post['addr']])->find() )$this->error('该地址已存在');
                M('device_addr')->add($post);
                $this->success('添加成功','admin/device/addr');die;
            }
        }
        $this->display();
    }


    public function addr_edit(){
        $id = I('get.id');
        if( $id ) $this->assign('row',M('device_addr')->find($id));
        if( IS_POST ){
            $post = I('post.');
            if( $post ){
                $res = M('device_addr')->where(['addr'=>$post['addr']])->find();
                if( $res && $id == $res['id'] )$this->error('该地址已存在');
                M('device_addr')->where(['id'=>$id])->save($post);
                $this->success('修改成功','admin/device/addr');
            }
        }
        $this->display();
    }


}