<?php
/**
 * 娃娃标签设置
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/18
 * Time: 14:52
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class LabelwawaController extends AdminbaseController
{

    public function index()
    {
        $this->assign('wawa_label',M('gift_label')->select());
        $this->display();
    }



    public function del(){
        $id=intval($_GET['id']);
        if($id){
            $result=M("gift_label")->delete($id);
            if($result){
                $this->success('删除成功', U('index'));
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
        $this->assign('get_id',$id);
        if($id){
            $this->assign('wawa_label',M('gift_label')->find($id));
        }else{
            $this->error('数据传入失败！');
        }
        $this->display();
    }

    public function edit_post()
    {
        if( IS_POST ){
            $post = I('post.');
            if( $post['id'] ){
                M('gift_label')->where(['id'=>$post['id']])->save($post);
                if( mysqli_affected_rows() >= 0 )
                    $this->success('修改成功', U('index'));
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
            if( M('gift_label')->add($post) )
                $this->success('添加成功', U('index'));
            $this->error('添加失败');
        }else{
            $this->error('数据传入失败！');
        }
    }

}