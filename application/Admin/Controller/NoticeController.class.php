<?php
/**
 * 系统消息管理
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/21
 * Time: 18:56
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class NoticeController extends AdminbaseController
{

    public function index()
    {
//        $sql = "select count(*) as num from (select * from cmf_notice group by title) as a";
//        $data = M('notice')->query($sql);
//        $count = $data[0]['num'];
        $count = M('notice_sys')->count();
        $page = $this->page($count,20);
//        $row = M('notice')->group('title')->limit($page->firstRow.','.$page->listRows)->order('ctime desc')->select();
        $row = M('notice_sys')->limit($page->firstRow.','.$page->listRows)->order('ctime desc')->select();
        $this->assign('row',$row);
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    public function clear()
    {
        if( IS_AJAX ){
            $sql = "TRUNCATE table cmf_notice_sys";
            M('notice_sys')->execute($sql);
            $this->ajaxReturn(['status'=>1]);
        }else{
            $this->ajaxReturn(['status'=>-1]);
        }
    }

    public function del()
    {
        $id = I('id');
        if( $id ){
            M('notice_sys')->where(['id'=>$id])->delete();
            $this->success('删除成功');exit;
        }else{
            $this->error('数据传入失败');
        }
    }


    public function add()
    {
        $this->display();
    }


    public function add_post()
    {
        set_time_limit(0);
        $post            = I('post.');
        $data['title']   = $post['title'];
        $data['desc']    = $post['desc'];
        $data['content'] = $post['content'];
        $data['ctime']   = time();
        
        $res = M('users')->where(['user_status'=>1])->select();
        /* 系统消息记录 */
        M('notice_sys')->add($data);
        foreach ($res as $v) {
            $data['user_id'] = $v['id'];
            $mesid = M('notice')->add($data);
            //友盟推送
             $row = M('users')->where(['id'=>$v['id']])->field('androidtoken,iphonetoken')->find();
             $this->umengpush($row['androidtoken'],$row['iphonetoken'],'系统消息',$post['title'],$mesid);
             unset($row);
        }
//        $this->umeng_broadcast($data['title'],$data['content']);
        $this->success('发布成功');
    }


    public function add_post_listcast()
    {
        $post            = I('post.');
        $data['title']   = $post['title'];
        $data['desc']    = $post['desc'];
        $data['content'] = $post['content'];
        $data['ctime']   = time();
        $mesid = M('notice')->add($data);
        $res = M('users')->where(['user_status'=>1,'iphonetoken'=>array('neq','')])->field('iphonetoken')->select();
        $count=count($res);

        $times=ceil($count/500);
        /* 系统消息记录 */
        M('notice_sys')->add($data);

        for($j=0;$j<$times;$j++){/**ios用列播方式推送**/
            $androidtoken='';
            $iphonetoken='';
            for ($i=$j*500;$i<($j+1)*500;$i++) {
                if($i>=$count) break;
//                $androidtoken.=$res[$i]['androidtoken'].",";
                $iphonetoken.=$res[$i]['iphonetoken'].",";
            }
            $this->umengpush_listcast(trim($androidtoken,','),trim($iphonetoken,','),'系统消息',$post['title'],$mesid);
        }

        $this->umengpush_broadcast('系统消息',$post['title'],$mesid,0);/**安卓用广播方式推送**/

        $this->success('发布成功');
    }
    public function add_post_broadcast()
    {
        $post            = I('post.');
        $data['title']   = $post['title'];
        $data['desc']    = $post['desc'];
        $data['content'] = $post['content'];
        $data['ctime']   = time();
        $mesid = M('notice')->add($data);
        M('notice_sys')->add($data);
        $this->umengpush_broadcast('系统消息',$post['title'],$mesid);
        $this->success('发布成功');
    }

    public function content()
    {
        $id             = I('id');
        $row            = M('notice_sys')->find($id);
        $row['content'] = htmlspecialchars_decode($row['content']);
        $this->assign('row',$row);
        $this->display();
    }

}