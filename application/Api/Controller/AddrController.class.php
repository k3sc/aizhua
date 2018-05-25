<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/12
 * Time: 18:40
 */
namespace Api\Controller;
class AddrController extends BaseController
{

    public function api()
    {
        $api_name = I('api_name');
        switch ($api_name)
        {
            case 'addr_list':
                $this->addr_list();
                break;
            case 'addr_add':
                $this->addr_add();
                break;
            case 'addr_del':
                $this->addr_del();
                break;
            case 'addr_update':
                $this->addr_update();
                break;
            default:
                $this->_return(404,'接口不存在');
                break;
        }
    }

    /**
     * 收货地址列表
     */
    private function addr_list()
    {
        $data = M('user_addr')->where(['user_id'=>$this->user_id])->select();
        $this->_return(1,'获取成功',$data);
    }


    /**
     * 新增收获地址
     */
    private function addr_add()
    {
        $post = I('post.');
        if( empty($post['username']) )$this->_return(-1,'收货人不能为空');
        if( empty($post['mobile']) )$this->_return(-1,'手机号码不能为空');
        if( !is_numeric($post['mobile']) )$this->_return(-1,'联系号码只能为8位或11位数字');
        if( strlen($post['mobile']) != 11 && strlen($post['mobile']) != 8 )$this->_return(-1,'联系号码只能为8位或11位数字');
        if( strlen($post['mobile']) == 11 ){
            if( !$this->is_mobile($post['mobile']) )$this->_return(-1,'联系号码格式错误');
        }
        if( empty($post['addr']) )$this->_return(-1,'省市县不能为空');
        if( empty($post['addr_info']) )$this->_return(-1,'详细地址不能为空');
        if( strlen($post['addr_info']) > 300 )$this->_return(-1,'详细地址不能超过100字');
        $post['user_id'] = $this->user_id;
        if( M('user_addr')->where($post)->find() )$this->_return(-1,'该地址已存在');
        $post['ctime']   = time();
        M('user_addr')->add($post);
        $this->_return(1,'新增成功');
    }


    /**
     * 删除收货地址
     */
    private function addr_del()
    {
        $addr_id = I('addr_id');
        if( empty($addr_id) )$this->_return(-1,'地址id不能为空');
        if( !is_numeric($addr_id) || $addr_id <= 0 )$this->_return(-1,'地址id错误');
        $res = M('user_addr')->where(['addr_id'=>$addr_id,'user_id'=>$this->user_id])->delete();
        if( $res )$this->_return(1,'删除成功');
        $this->_return(-1,'删除失败');
    }

    /**
     * 修改收货地址
     */
    private function addr_update()
    {
        $post = I('post.');
        if( empty($post['addr_id']) )$this->_return(-1,'地址id不能为空');
        if( !is_numeric($post['addr_id']) || $post['addr_id'] <= 0 )$this->_return(-1,'地址id错误');

        if( isset($_POST['username']) )
            if( empty($post['username']) )$this->_return(-1,'收件人不能为空');
        if( isset($_POST['mobile']) )
        {
            if( empty($post['mobile']) )$this->_return(-1,'手机号码不能为空');
            if( !is_numeric($post['mobile']) )$this->_return(-1,'联系号码只能为8位或11位数字');
            if( strlen($post['mobile']) != 11 && strlen($post['mobile']) != 8 )$this->_return(-1,'联系号码只能为8位或11位数字');
            if( strlen($post['mobile']) == 11 ){
                if( !$this->is_mobile($post['mobile']) )$this->_return(-1,'联系号码格式错误');
            }
        }
        if( isset($_POST['addr']) )
            if( empty($post['addr']) )$this->_return(-1,'省市县不能为空');
        if( isset($_POST['addr_info']) )
        {
            if( empty($post['addr_info']) )$this->_return(-1,'详细地址不能为空');
            if( strlen($post['addr_info']) > 300 )$this->_return(-1,'详细地址不能超过100字');
        }
        M('user_addr')->where(['addr_id'=>$post['addr_id'],'user_id'=>$this->user_id])->save($post);
        $this->_return(1,'修改成功');
    }


}