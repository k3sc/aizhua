<?php
/**
 * 游戏参数配置
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/18
 * Time: 16:11
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class GameconfigController extends AdminbaseController
{

    public function index()
    {
        $this->assign('gameconfig',M('game_config')->select());
        $this->display();
    }

    public function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("game_config")->delete($id);
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

    public function edit()
    {
        $id=intval($_GET['id']);
        $this->assign('id',$id);
        if($id){
            $this->assign('gameconfig',M('game_config')->find($id));
        }else{
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    public function edit_post()
    {
        if( IS_POST ){
            $post = I('post.');
            if( in_array('',$post) )$this->error('各项参数不能为空');
            $res = M('game_config')->where(['name'=>$post['name']])->find();
            if( $post['id'] != $res['id'] && $res  )$this->error('参数名已存在');
            if( $post['id'] ){
                $post['ctime'] = time();
                M('game_config')->where(['id'=>$post['id']])->save($post);
                if( mysqli_affected_rows() >= 0 )
                    $this->success('修改成功');
                $this->error('修改失败');
            }else{
                $this->error('数据传入失败！');
            }
        }
    }

    public function add()
    {
        $this->display();
    }

    public function add_post()
    {
        if( IS_POST ){
            $post = I('post.');
            if( in_array('',$post) )$this->error('各项参数不能为空');
            if( M('game_config')->where(['name'=>$post['name']])->find() )$this->error('参数名已存在');
            $post['ctime'] = time();
            if( M('game_config')->add($post) )
                $this->success('添加成功');
            $this->error('添加失败');
        }else{
            $this->error('数据传入失败！');
        }
    }

}