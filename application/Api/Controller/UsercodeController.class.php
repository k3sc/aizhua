<?php
/**
 * 用户邀请码
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/13
 * Time: 13:18
 */
namespace Api\Controller;
class UsercodeController extends BaseController
{

    public function api()
    {
        $api_name = I('api_name');
        switch ($api_name)
        {
            case 'get_code':
                $this->get_code();
                break;
            case 'convert_code':
                $this->convert_code();
                break;
            case 'get_code_config':
                $this->get_code_config();
                break;
            case 'online':
                $this->online();
                break;
            case 'get_version':
                $this->get_version();
                break;
            case 'get_app_url':
                $this->get_app_url();
                break;
            default:
                $this->_return(404,'接口不存在');
                break;
        }
    }
	
	public function online(){
		$id = M('user_online')->where('user_id='.$this->user_id)->getField('id');
		if(!$id){
			M('user_online')->add(array('user_id' => $this->user_id, 'ctime' => time()));
		}else{
			M('user_online')->where(['id' => $id])->save(array('ctime' => time()));
		}
		$this->_return(1,'执行成功');
	}


    /**
     * 获取我的邀请码
     */
    private function get_code()
    {
        $this->_return(1,'获取成功',M('users')->where(['id'=>$this->user_id])->getField('user_activation_key'));
    }


    /**
     * 兑换邀请码
     */
    private function convert_code()
    {
        $code = I('code');
        if( empty($code) )$this->_return(-1,'邀请码不能为空');
        if( !M('users')->where(['user_activation_key'=>$code])->find() )$this->_return(-1,'邀请码不存在','',2);
        //获取邀请码拥有者id
        $owner_id = M('users')->where(['user_activation_key'=>$code])->getField('id');
        //检测使用的邀请码是否是本人的
        if( $owner_id == $this->user_id ){echo json_encode(['code'=>-1,'msg'=>'不能填写自己的邀请码','data'=>'']);die;};
        //检测是否已经使用过该邀请码
        if( M('code_record')->where(['user_id'=>$this->user_id,'owner_id'=>$owner_id])->find() )
            $this->_return(-1,'你已经使用过该邀请码，不能重复使用');
        //计算兑换者通过填写邀请码获得的娃娃币总数
        $sum = M('code_record')->where(['user_id'=>$this->user_id])->sum('num');
        //计算拥有者通过分享邀请码获得的娃娃币总数
        $sum_owner = M('code_record')->where(['owner_id'=>$owner_id])->sum('num');
        //获得通过填写邀请码得到的娃娃币上限
        $res = M('config')->where(['id'=>1])->field('code_wawabi,code_wawabi_max')->find();
        $max = $res['code_wawabi_max'];
        $code_wawabi = $res['code_wawabi'];
        //赠送娃娃币给兑换者
//        if( $max-$sum < $code_wawabi && $max-$sum > 0 ){
//            $this->set_coin($this->user_id,($max-$sum));
//            $this->to_code_record($this->user_id,$owner_id,$code,($max-$sum));
//        }else if( $max - $sum >= $code_wawabi ){
            $this->set_coin($this->user_id);
            $this->to_code_record($this->user_id,$owner_id,$code,-1);
//        }else{
//            $this->to_code_record($this->user_id,$owner_id,$code,0);
//        }
        //赠送娃娃币给邀请码拥有者
//        if( $max - $sum_owner < $code_wawabi && $max-$sum_owner > 0 ){
//            $this->set_coin($owner_id,($max-$sum_owner));
//        }else if( $max - $sum_owner >= $code_wawabi ){
            $this->set_coin($owner_id);
//        }
        M('users')->where(['id'=>$this->user_id])->save(['pid' => $owner_id, 'is_code'=>1,'input_code'=>$code]);

        $this->_return(1,'兑换成功');
    }


    /**
     * 获取邀请码配置信息 （填写邀请码获得多少娃娃币，填写邀请码获得娃娃币的上限值）
     */
    private function get_code_config()
    {
        $res = M('config')->where(['id'=>1])->find();
        $str = '邀请好友，得'.$res['code_wawabi'].'娃娃币';
        $regsiter = M('active_config')->where(['id'=>1])->getField('register_coin');
        $arr = ['str'=>$str,'register_coin'=>$regsiter,'num'=>$res['code_wawabi']];
        $this->_return(1,'获取成功',$arr);
    }

    /**
     * 获取APP版本号，APP下载地址
     */
    private function get_version(){
        $row = M('config')->where(['id'=>1])->find();
        $version['apk_ver'] = $row['apk_ver'];
        $version['apk_url'] = $row['apk_url'];
        $version['app_ver_name'] = $row['app_ver_name'];
        $row['app_update_content'] = '<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"><style type="text/css">img{max-width:100%;}</style>'.$row['app_update_content'];
        $version['app_update_content'] = htmlspecialchars_decode($row['app_update_content']);
        $this->_return(1,'获取成功',$version);
    }

    /**
     * 获取推广下载链接
     */
    private function get_app_url()
    {
        $this->_return(1,'获取成功',M('config')->where(['id'=>1])->getField('app_url'));
    }


}