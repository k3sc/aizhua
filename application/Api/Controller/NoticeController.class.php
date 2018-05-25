<?php
/**
 * 消息通知
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/13
 * Time: 17:16
 */
namespace Api\Controller;
class NoticeController extends BaseController
{

	public function api()
	{	
		$api_name = I('api_name');
		if(!method_exists($this, $api_name))$this->_return(404, '接口不存在');    
		$this->$api_name();
	}

    /**
     * 获取消息通知列表
     */
    private function get_new_notice()
    {
        $noticeArr = M('notice')->where('type in (1,3)')->order('notice_id desc')->field('type,notice_id,user_id,to_user_id,title,desc,content,status,ctime')->limit(5)->select();
//        $zhuazhongArr = M('notice')->where('type=1')->order('notice_id desc')->field('notice_id,user_id,title,desc,status,ctime')->limit(5)->select();

        foreach ($noticeArr as &$v) {
            $v['status_name'] = $v['status'] == 0 ? '未读' : '已读';
//            $v['desc'] = $v['content'];
            if( $v['to_user_id'] ){
                $v['avatar_thumb'] = $this->get_upload_path(M('users')->where('id='.$v['to_user_id'])->getField('avatar_thumb'));
                $v['user'] = M('users')->where(['id'=>$v['to_user_id']])->getField('user_nicename');
            }else{
                $v['avatar_thumb'] = $this->get_upload_path(M('users')->where('id='.$v['user_id'])->getField('avatar_thumb'));
                $v['user'] = M('users')->where(['id'=>$v['user_id']])->getField('user_nicename');
            }

            if( $v['to_user_id'] ){
                $v['avatar_thumb2'] = $this->get_upload_path(M('users')->where('id='.$v['user_id'])->getField('avatar_thumb'));
                $v['user2'] = M('users')->where(['id'=>$v['user_id']])->getField('user_nicename');
                $v['content'] = mb_substr($v['content'],0,-1,'utf-8');
            }else{
                $v['avatar_thumb2'] = '';
                $v['user2'] = '';
                $v['content'] = '抓中了';
            }
        }
		unset($v);
        $this->_return(1,'获取成功',$noticeArr);
    }


    /**
     * 获取消息通知列表
     */
    private function get_notice_list()
    {
        $page = I('post.page', 0, 'intval');
        $pagesize = I('post.pagesize', 20, 'intval');

        $map['user_id']=array('in',[$this->user_id,'0']);
        $noticeArr = M('notice')->where($map)->order('notice_id desc')->field('type,notice_id,user_id,to_user_id,title,desc,status,ctime')->page($page, $pagesize)->select();
        foreach ($noticeArr as $k => $v) {

            if ($v['user_id'] == 0) {
                $msg_status = M('msg_status')->where(['msgid' => $v['notice_id'],'user_id'=>$this->user_id])->getField('id');
                if ($msg_status){
                    $v['status'] = 1;
                    $noticeArr[$k]['status'] = 1;
                } else {
                    $v['status'] = 0;
                    $noticeArr[$k]['status'] = 0;
                }
            }

            $noticeArr[$k]['status_name'] = $v['status'] == 0 ? '未读' : '已读';
        }
        $this->_return(1, '获取成功', $noticeArr);
    }

    /**
     * 获取消息详情
     */
    private function get_notice_info()
    {
        $id = I('id');
        if (empty($id)) {
            $this->_return(-1, 'id不能为空');
        }

        if (!is_numeric($id) || $id <= 0) {
            $this->_return(-1, 'id错误');
        }

        M('notice')->where(['notice_id' => $id])->save(['status' => 1]);



        $notice = M('notice')->where(['notice_id' => $id])->find();

        if($notice['user_id']==0){
            $msg_status = M('msg_status')->where(['msgid' => $id,'user_id'=>$this->user_id])->getField('id');
            if(!$msg_status ){
                M('msg_status')->add(['msgid'=>$id,'user_id'=>$this->user_id]);
            }
        }


        $notice['content'] = htmlspecialchars_decode($notice['content']);
        $notice['content'] = '<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"><style type="text/css">img{max-width:100%;}</style>'.$notice['content'];
        $this->_return(1,'获取成功',$notice);
    }

}