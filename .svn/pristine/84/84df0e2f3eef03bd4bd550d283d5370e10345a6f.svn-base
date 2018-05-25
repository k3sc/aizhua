<?php
/**
 * 人工服务管理
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/21
 * Time: 13:35
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class ServiceController extends AdminbaseController
{

    public function index()
    {
        $statusArr = ['待处理','已处理'];
        $count = M('game_service')->count();
        $page = $this->page($count,20);
        $res = M('game_service as a')
            ->join('left join cmf_users as b on a.user_id = b.id')
            ->join('left join cmf_service_text as c on a.service_text_id = c.id')
            ->join('left join cmf_game_room as d on a.room_id = d.id')
            ->limit($page->firstRow.','.$page->listRows)
            ->field('a.*,b.user_nicename,c.content as text,c.coin as service_coin,d.room_name,d.room_no')
            ->order('a.status')
            ->select();

        //获取设备号
        foreach ($res as $k => $v) {
            $room_info = M('game_room')->find($v['room_id']);
            $res[$k]['device_no'] = M('device')->where(['device_unique_code'=>$room_info['fac_id']])->getField('deveci_no');
        }
        $this->assign('row',$res);
        $this->assign('statusArr',$statusArr);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }


    public function handle(){
        $id = I('id');
        if( $id ){
            M('game_service')->where(['id'=>$id])->save(['status'=>1]);
        }
        $this->redirect('Service/index');
    }


    public function del()
    {
        $id = I('id','');
        if( $id ){
            M('game_service')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }else{
            $this->error('数据传入失败');
        }
    }


    public function text()
    {
        $this->assign('row',M('service_text')->select());
        $this->display();
    }

    public function text_del()
    {
        $id = I('id','');
        if( $id ){
            M('service_text')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }else{
            $this->error('数据传入失败');
        }
    }

    public function text_add()
    {
        if( IS_POST ){
            if( empty($_POST['content']) )$this->error('文案内容不能为空');
            if( M('service_text')->where(['content'=>$_POST['content']])->find() )
                $this->error('该文案已存在');
            M('service_text')->add($_POST);
            $this->success('添加成功');
            exit;
        }
        $this->display();
    }

    public function text_edit()
    {
        $id = I('get.id');
        if($id ) $this->assign('row',M('service_text')->find($id));
        if( IS_POST ){
            if( empty($_POST['content']) )$this->error('文案内容不能为空');
            $res = M('service_text')->where(['content'=>$_POST['content']])->find();
            if( $res && $id != $res['id'] ) $this->error('该文案已存在');
            M('service_text')->where(['id'=>$id])->save($_POST);
            $this->success('编辑成功');
            exit;
        }
        $this->display();
    }

    public function game_service()
    {
        $this->assign('row',M('config')->find(1));
        if( IS_POST ){
            M('config')->where(['id'=>1])->save($_POST);
            $this->success('操作成功');exit;
        }
        $this->display();
    }

    /**
     * 站内通知
     */
    public function bill(){
        if( IS_AJAX ){
            $res = M('game_service as a')
                ->join('cmf_game_room as b on a.room_id = b.id')
                ->field('a.*,b.room_name,b.room_no')
                ->where(['a.status'=>0])
                ->select();
            $this->ajaxReturn(['list'=>$res,'status'=>1,'count'=>count($res)]);
        }
    }



}