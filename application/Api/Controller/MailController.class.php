<?php
/**
 * 发送邮件
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/14
 * Time: 10:09
 */
namespace Api\Controller;
class MailController extends BaseController
{

    public function send()
    {
        $code       = mt_rand(1000,9999);
        $tomail     = I('email');
        $action     = I('action');
        if( !$action ) $this->_return(-1,'action不能为空');
        if( !in_array($action,['register','forget_password']) ) $this->_return(-1,'action错误');
        if( $action == 'register' ){
            /* 检测邮箱是否已被注册 */
            $row = M('users')->where(['user_login'=>$tomail])->find();
            if( $row ) $this->_return(-1,'该邮箱已被注册');
        }
        $subject    = '验证码';
        $body       = '【爱抓娃娃机】&nbsp;&nbsp;您的验证码是<font color="red"><b> '.$code.'</b></font> ，有效期10分钟。&nbsp;&nbsp;如若误发，请忽略。';
        $res = $this->sendmail('',$tomail,$subject,$body);
        if( $res === true )
        {
            M('email_code')->add(array(
                'email' => $tomail,
                'code'  => $code,
                'ctime' => time()
            ));
            $this->_return(1,'发送成功');
        }

        $this->_return(-1,'发送失败',$res);

    }


}