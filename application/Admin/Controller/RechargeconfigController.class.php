<?php
/**
 * 充值设置
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/19
 * Time: 21:19
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class RechargeconfigController extends AdminbaseController
{

    private $payArr = [1=>'微信支付',2=>'支付宝支付',3=>'paypal支付',4=>'苹果支付'];

    public function index()
    {
        $arr = M('recharge_config')->order('money desc')->select();
        foreach ($arr as $k => $v) {
            $temp = '';
            $res = M('give_gift')->where(['id'=>['in',$v['gift']]])->select();
            foreach ($res as $vv) {
                $temp .= $vv['name'].' , ';
            }
            $arr[$k]['gift'] = $temp;
        }
        $this->assign('recharge_config',$arr);
        $this->display();
    }

    public function del()
    {
        $id=intval($_GET['id']);
        if($id){
            if( M('recharge_config')->where(['id'=>$id])->delete() !== false ){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('数据传入失败！');
        }
        $this->display();
    }


    public function add()
    {
        $gift = M('give_gift')->select();
        $this->assign('gift',$gift);
        $this->display();
    }

    public function add_post()
    {
        if( IS_POST ){
            $post = I('post.');
            if( empty($post['money']) )$this->error('充值金额不能为空');
            $post['ctime'] = time();
            $post['gift'] = implode(',',$post['gift']);
            M('recharge_config')->add($post);
            $this->success('添加成功');
        }
    }

    public function edit()
    {
        $id = I('id');
        if( $id ){
            $gift = M('give_gift')->select();
            $this->assign('gift',$gift);
            $res = M('recharge_config')->find($id);
            $res['gift'] = explode(',',$res['gift']);
            $this->assign('row',$res);
        }
        $this->display();
    }


    public function edit_post($id)
    {
        if( $id ){
            $post = I('post.');
            if( empty($post['money']) )$this->error('充值金额不能为空');
            $res = M('recharge_config')->where(['money'=>$post['money']])->find();
            if( $res && $res['id'] != $id )
                $this->error('该配置已存在');
            $post['ctime'] = time();
            $post['gift'] = implode(',',$post['gift']);
            M('recharge_config')->where(['id'=>$id])->save($post);
            if( mysqli_affected_rows() >= 0 ){
                $this->success('修改成功');exit;
            }
            $this->error('修改失败');exit;
        }
        $this->display();
    }


    public function type()
    {
        $this->assign('payarr',$this->payArr);
        $res = M('config')->find(1);
        $res['paytype'] = explode(',',$res['paytype']);
        $this->assign('row',$res);

        if( IS_POST ){
            $post = I('post.');
            $post['paytype'] = implode(',',$post['paytype']);
            M('config')->where(['id'=>1])->save($post);
            $this->success('修改成功');exit;
        }

        $this->display();
    }

}