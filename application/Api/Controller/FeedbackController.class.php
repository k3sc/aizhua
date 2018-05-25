<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/13
 * Time: 11:07
 */
namespace Api\Controller;
class FeedbackController extends BaseController
{

    public function api()
    {
        $api_name = I('api_name');
        switch ($api_name)
        {
            case 'feedback':
                $this->feedback();
                break;
            default:
                $this->_return(404,'接口不存在');
                break;
        }
    }


    /**
     * 提交问题反馈
     */
    private function feedback()
    {
        $post = I('post.');
        if( empty($post['content']) )$this->_return(-1,'反馈内容不能为空');
        if( empty($post['contact']) )$this->_return(-1,'联系方式不能为空');
        if( empty($post['version']) )$this->_return(-1,'操作系统版本不能为空');
        if( empty($post['appversion']) )$this->_return(-1,'APP版本不能为空');
        if( empty($post['model']) )$this->_return(-1,'手机型号不能为空');
        if( !$this->is_mobile($post['contact']) && !$this->is_email($post['contact']) )$this->_return(-1,'联系方式只能是邮箱或手机号码');
        if( strlen($post['content']) > 600 )$this->_return(-1,'反馈内容不能大于200字');
        M('feedback')->add(array(
            'uid'           => $this->user_id,
            'content'       => $post['content'],
            'addtime'       => time(),
            'contact'       => $post['contact'],
            'version'       => $post['version'],
            'appversion'    => $post['appversion'],
            'model'         => $post['model'],
        ));
        $this->_return(1,'提交成功');
    }

}