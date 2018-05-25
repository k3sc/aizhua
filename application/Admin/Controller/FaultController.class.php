<?php
/**
 * 故障管理
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/20
 * Time: 9:51
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class FaultController extends AdminbaseController
{

    public function index()
    {
        $count = M('fault')->count();
        $page = $this->page($count,20);
        $res = M('fault')->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        $this->assign('page',$page->show('Admin'));
        $this->assign('fault',$res);
        $this->display();
    }


    public function del()
    {
        $id = I('id','');
        if( $id ){
            M('fault')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }else{
            $this->error('数据传入失败');
        }
    }


    public function clear(){
        if( IS_AJAX ){
            $sql = "TRUNCATE TABLE cmf_fault";
            M('fault')->execute($sql);
            $this->ajaxReturn(['status'=>1]);
        }
    }


}