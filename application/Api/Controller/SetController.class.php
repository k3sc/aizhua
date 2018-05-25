<?php
/**
 * 用户个人设置控制器（简体繁体转换，背景音乐开关，音效开关）
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/10/12
 * Time: 21:39
 */
namespace Api\Controller;
class SetController extends BaseController
{

    public function api()
    {
        $api_name = I('api_name');
        switch ($api_name)
        {
            case 'trans':
                $this->trans();
                break;
            case 'bgmusic':
                $this->bgmusic();
                break;
            case 'yx':
                $this->yx();
                break;
            case 'get_faq':
                $this->get_faq();
                break;
            case 'get_user_agreement':
                $this->get_user_agreement();
                break;
            case 'get_yd_img':
                $this->get_yd_img();
                break;
            case 'email_status':
                $this->email_status();
                break;
            case 'get_company_name':
                $this->get_company_name();
                break;
            case 'get_about':
                $this->get_about();
                break;
            case 'get_know':
                $this->get_know();
                break;
            case 'get_quick':
                $this->get_quick();
                break;
            case 'get_keyword':
                $this->get_keyword();
                break;
            case 'get_service':
                $this->get_service();
                break;
            default:
                $this->_return(404,'接口不存在');
                break;
        }
    }


    /**
     * 简繁转换
     */
    private function trans()
    {
        $type = I('type');
        if( $type != 1 && $type != 2 )$this->_return(-1,'type只能是1或2');
        $config = M('users')->where(['id'=>$this->user_id])->getField('user_setting');
        $config = json_decode($config,true);
        $config['lan'] = $type;
        $config = json_encode($config);
        $sql = "update cmf_users set user_setting = '".$config."' where id = $this->user_id";
        M('users')->execute($sql);
        sleep(1);
        $this->_return(1,'操作成功');
    }


    /**
     * 背景音乐开关
     */
    private function bgmusic()
    {
        $type = I('type');
        if( $type != 1 && $type != 2 )$this->_return(-1,'type只能是1或2');
        $config = M('users')->where(['id'=>$this->user_id])->getField('user_setting');
        $config = json_decode($config,true);
        $config['bgmusic'] = $type;
        $config = json_encode($config);
        $sql = "update cmf_users set user_setting = '".$config."' where id = $this->user_id";
        M('users')->execute($sql);
        $this->_return(1,'操作成功');
    }


    /**
     * 音效开关
     */
    private function yx()
    {
        $type = I('type');
        if( $type != 1 && $type != 2 )$this->_return(-1,'type只能是1或2');
        $config = M('users')->where(['id'=>$this->user_id])->getField('user_setting');
        $config = json_decode($config,true);
        $config['yx'] = $type;
        $config = json_encode($config);
        $sql = "update cmf_users set user_setting = '".$config."' where id = $this->user_id";
        M('users')->execute($sql);
        $this->_return(1,'操作成功');
    }


    /**
     * 获取常见问题
     */
    private function get_faq()
    {
//        $this->_return(1,'获取成功',M('config')->where(['id'=>1])->getField('FAQ'));
        $row = M('posts')->where(['post_title'=>'常见问题'])->getField('post_content');
        /* 富文本内容自适应 */
        $row = '<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"><style type="text/css">img{max-width:100%;}</style>'.$row;
        $row = htmlspecialchars_decode($row);
        $this->_return(1,'获取成功',$row);
    }

    /**
     * 获取用户协议
     */
    private function get_user_agreement()
    {
//        $this->_return(1,'获取成功',M('config')->where(['id'=>1])->getField('user_agreement'));
        $row = M('posts')->where(['post_title'=>'用户协议'])->getField('post_content');
        /* 富文本内容自适应 */
        $row = '<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"><style type="text/css">img{max-width:100%;}</style>'.$row;
        $row = htmlspecialchars_decode($row);
        $this->_return(1,'获取成功',$row);
    }

    /**
     * 获取服务条款
     */
    private function get_service()
    {
        $this->_return(1,'获取成功',M('posts')->where(['post_title'=>'服务条款'])->getField('post_content'));
    }

    /**
     * 关于我们
     */
    private function get_about()
    {
//        $this->_return(1,'获取成功',M('config')->where(['id'=>1])->getField('user_agreement'));
        $this->_return(1,'获取成功',M('posts')->where(['post_title'=>'关于我们'])->getField('post_content'));
    }

    /**
     * 获取引导页轮播图
     */
    private function get_yd_img()
    {
        $row = M('slide')->where(['slide_cid'=>2])->field('slide_pic')->select();
        $arr = [];
        foreach ($row as $item) {
            $arr[] = $item['slide_pic'];
        }
        $this->_return(1,'获取成功',$arr);
    }

    /**
     * 获取邮箱登陆开关
     */
    private function email_status()
    {
        $this->_return(1,'获取成功',M('setting_other')->where(['id'=>1])->getField('yx'));
    }

    /**
     * 获取公司名
     */
    private function get_company_name(){
        $row = M('config')->field('company_name,company_name_en')->find(1);
        $row['company_name'] = '';
        $row['company_name_en'] = 'Copyright © 2017 Aizhua. All Rights Reserved.';
        $this->_return(1,'获取成功',$row);
    }

    /**
     * 获取兑换须知
     */
    private function get_know(){
        $row = M('slide')->where(['slide_cid'=>10])->find();
//        $res['url'] = $row['slide_url'];
        /* 富文本内容自适应 */
        $row['slide_content'] = '<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"><style type="text/css">img{max-width:100%;}</style>'.$row['slide_content'];
        $res['content'] = htmlspecialchars_decode($row['slide_content']);
        $res['pic'] = $row['slide_pic'];
        $this->_return(1,'获取成功',$res);
//        $this->_return(1,'获取成功',M('posts')->where(['post_title'=>'兑换须知'])->getField('post_content'));
    }

    /**
     * 获取聊天快捷短语
     */
    private function get_quick(){
        $res = M('setting_other')->where(['id'=>1])->getField('quick');
        $res = explode("\r\n", trim($res));
        $res = array_filter($res);
        $this->_return(1,'获取成功',$res);
    }

    /**
     * 获取聊天屏蔽关键字
     */
    private function get_keyword(){
        $res = M('setting_other')->where(['id'=>1])->getField('keyword');
        $res = explode(",", trim($res));
        $res = array_filter($res);
        $this->_return(1,'获取成功',$res);
    }


}