<?php
/**
 * 甩爪设置
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/18
 * Time: 22:21
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class ClawController extends AdminbaseController
{

    public function index()
    {
        $this->assign('claw_config',M('config')->find(1));
        $this->display();
    }


    public function edit()
    {
        $id = I('get.id');
        if( $id ){
            $this->assign('data',M('config')->find($id));
        }
        $this->display();
    }

    public function edit_post()
    {
        if( IS_POST ){
            $post = I('post.');
            if( in_array('',$post) )$this->error('各项参数不能为空');
            if( M('config')->where(['id'=>$post['id']])->save($post) )
                $this->success('修改成功');
            $this->error('修改失败');
        }else{
            $this->error('数据传入失败');
        }
    }

}