<?php
/**
 * 申诉列表
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/20
 * Time: 11:12
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class AppealController extends AdminbaseController
{

    public function index()
    {
        $count = M('game_appeal')->count();
        $page = $this->page($count,20);
        $res = M('game_appeal as a')
            ->join('left join cmf_users as b on a.user_id = b.id')
            ->limit($page->firstRow.','.$page->listRows)
            ->field('a.*,b.user_nicename')
            ->order('a.status,a.ctime desc')
            ->select();

        foreach ($res as $k => $v) {
            $temp = '';
            $r = M('appeal_text')->where(['id'=>['in',$v['appeal_type']]])->select();
            foreach ($r as $vv) {
                $temp .= $vv['text'].' , ';
            }
            $res[$k]['appeal_type'] = $temp;
        }
        $this->assign('row',$res);
        $this->assign('page',$page->show('Admin'));

        $this->display();
    }


    public function del()
    {
        $id = I('id','');
        if( $id ){
            M('game_appeal')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }else{
            $this->error('数据传入失败');
        }
    }


    public function text()
    {
        $this->assign('row',M('appeal_text')->select());
        $this->display();
    }


    public function text_del()
    {
        $id = I('id','');
        if( $id ){
            M('appeal_text')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }else{
            $this->error('数据传入失败');
        }
    }


    public function text_add()
    {
        if( IS_POST ){
            if( empty($_POST['text']) )$this->error('文案内容不能为空');
            if( M('appeal_text')->where(['text'=>$_POST['text']])->find() )
                $this->error('该文案已存在');
            M('appeal_text')->add($_POST);
            $this->success('添加成功');
            exit;
        }
        $this->display();
    }

    public function text_edit()
    {
        $id = I('get.id');
        if($id ) $this->assign('row',M('appeal_text')->find($id));
        if( IS_POST ){
            if( empty($_POST['text']) )$this->error('文案内容不能为空');
            $res = M('appeal_text')->where(['text'=>$_POST['text']])->find();
            if( $res && $id != $res['id'] ) $this->error('该文案已存在');
            M('appeal_text')->where(['id'=>$id])->save($_POST);
            $this->success('编辑成功');
            exit;
        }
        $this->display();
    }


    public function video()
    {
        $id = I('id');
        if( $id ){
            $row = M('game_appeal')->find($id);
            $this->assign('row',$row);
        }
        $this->display();
    }


    /**
     * 标记处理
     */
    public function handle(){
        $id = I('id');
        if( $id ){
            M('game_appeal')->where(['id'=>$id])->save(['status'=>1]);
            $this->redirect('admin/appeal/index');
        }
    }


}