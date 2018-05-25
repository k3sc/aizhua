<?php
/**
 * 其他设置
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/21
 * Time: 15:06
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class SettingotherController extends AdminbaseController
{

    public function index()
    {
        $row = M('setting_other')->find(1);
        $paytype = M('config')->field('paytype')->find(1);
        $row['paytype'] = explode(',',$paytype['paytype']);
        $phone = M('config')->where(['id'=>1])->getField('phone');
        $code_wawabi = M('config')->where(['id'=>1])->getField('code_wawabi');
        /* 版本信息 */
        $version = M('config')->where(['id'=>1])->field('apk_ver,apk_url,ipa_ver,app_ios,app_ver_name,app_update_content')->find();
        $rate = M('config')->where(['id'=>1])->getField('rate');
        /* 应用市场下载链接 */
        $app_url = M('config')->where(['id'=>1])->getField('app_url');
        $this->assign('app_url',$app_url);
        $this->assign('rate',$rate);
        $this->assign('version',$version);
        $this->assign('phone',$phone);
        $this->assign('code_wawabi',$code_wawabi);
        $this->assign('row',$row);
        $this->display();
    }

    public function post()
    {
        if( IS_POST ){
//            if( $_FILES['apk'] ) $apk_url = $this->upload_apk();
            //$apk_url = I('file');
			$apk_url = I('apk_url');
            M('setting_other')->where(['id'=>1])->save($_POST);
            $paytype = implode(',',$_POST['paytype']);
            $phone = $_POST['phone'];
            if( $phone && !is_mobile($phone) ) $this->error('手机号码格式错误');
            $code_wawabi = $_POST['code_wawabi'];
            $apk_ver = I('apk_ver',1,'intval');
            $app_ver_name = I('app_ver_name');
            $app_update_content = I('app_update_content');
            $app_url = I('app_url');
            if( $app_url ){
                if( strpos($app_url,'http',0) !== 0 && strpos($app_url,'https',0) !== 0 )
                    $app_url = 'http://'.$app_url;
            }
//            $ipa_ver = $_POST['ipa_ver'];
//            $app_ios = $_POST['app_ios'];
            if( !$apk_ver )$this->error('Android版本号不能为空');
//            if( !$ipa_ver )$this->error('iOS版本号不能为空');
//            if( !$app_ios )$this->error('iOS下载地址不能为空');
            if( empty($apk_url) ){
                $arr = [
                    'paytype'       => $paytype,
                    'phone'         => $phone,
                    'code_wawabi'   => $code_wawabi,
                    'apk_ver'       => $apk_ver,
                    'app_ver_name'  => $app_ver_name,
                    'app_update_content' => $app_update_content,
                    'rate'          => I('post.rate',0),
                    'app_url'       => $app_url,
                ];
            }else{
                $arr = [
                    'paytype'       => $paytype,
                    'phone'         => $phone,
                    'code_wawabi'   => $code_wawabi,
                    'apk_ver'       => $apk_ver,
                    'apk_url'       => $apk_url,
                    'app_ver_name'  => $app_ver_name,
                    'app_update_content' => $app_update_content,
                    'rate'          => I('post.rate',0),
                    'app_url'       => $app_url,
                ];
            }
            M('config')->where(['id'=>1])->save($arr);
            $this->success('操作成功');
        }
    }

    public function bgmusic(){
        $this->assign('row',M('bgmusic')->select());
        $this->display();
    }


    public function del_bgmusic(){
        $id = I('id');
        if( $id ) {
            M('bgmusic')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }
    }

    public function upload_bgmusic(){
        $file = $this->uploadImage($_FILES);
        $title = I('title');
        if( !$title )$this->error('标题不能为空');
        M('bgmusic')->add(['title'=>$title,'addr'=>$file]);
        $this->success('上传成功');
    }


    public function setphone()
    {
        $this->assign('config',M('config')->find(1));
        if( IS_POST ){
            M('config')->where(['id'=>1])->save($_POST);
            $this->success('设置成功');exit;
        }
        $this->display();
    }


    /**
     * 游戏音效
     * @return 
     */
    public function gameAudio()
    {
        $this->assign('row',M('game_audio')->select());
        $this->display();
    }

    /**
     * 上传游戏音效
     * @return [type] [description]
     */
    public function upload_game_audio()
    {
        $file = $this->uploadImage($_FILES);
        $title = I('title');
        $type = I('type');
        if( !$title )$this->error('标题不能为空');
        M('game_audio')->add(['title'=>$title,'addr'=>$file,'type'=>$type,'ctime'=>time()]);
        $this->success('上传成功');
    }


    public function del_game_audio(){
        $id = I('id');
        if( $id ) {
            M('game_audio')->where(['id'=>$id])->delete();
            $this->success('删除成功');
        }
    }

    /**
     * 上传apk文件
     */
    public function upload_apk(){
        if( $_FILES['apk']['error'] ) die($_FILES['apk']['error']);
        $url = $this->uploadImage($_FILES);
        if(is_array($url)){
            $this->error($url['error']);
        }
        return $url;
    }


    public function getFile()
    {
        if (empty($_FILES)) $this->error('未上传图片');
        //if( $_FILES['apk']['type'] != 'application/vnd.android.package-archive' ) $this->error('请上传.apk文件！');
        $file = $this->uploadImage($_FILES);
        $this->ajaxReturn(['filename'=>$file]);
    }

}