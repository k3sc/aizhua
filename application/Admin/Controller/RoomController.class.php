<?php
/**
 * 房间管理
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/20
 * Time: 16:53
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;

class RoomController extends AdminbaseController
{
    private $statusarr = ['在线','补货中','维修中','游戏中','离线'];
    private $isshowarr = ['下架','上架'];

    public function index()
    {
        $where = ' 1=1 ';
        if( IS_POST ){
            $status = I('status');
            if( $status >= 0 ){
                $where .= ' and a.status = '.$status;
                $this->assign('status',$status);
            }
            $room_no = I('room_no');
            if( $room_no ){
                $where .= ' and a.room_no like "%'.$room_no.'%"';
                $this->assign('room_no',$room_no);
            }
            $room_name = I('room_name');
            if( $room_name ){
                $where .= ' and a.room_name like "%'.$room_name.'%"';
                $this->assign('room_name',$room_name);
            }
            $is_show = I('is_show');
            if( $is_show >= 0 ){
                $where .= ' and a.is_show = '.$is_show;
                $this->assign('is_show',$is_show);
            }
        }
        $count = M('game_room as a')->where($where)->count();
        $page  = $this->page($count,20);
        $res   = M('game_room as a')
            ->join('left join cmf_gift as b on a.type_id = b.id')
            ->join('left join cmf_device as c on a.fac_id = c.device_unique_code')
            ->join('left join cmf_device_addr as d on c.device_addr_id=d.id')
            ->join('left join cmf_game_config as e on c.game_config_id=e.id')
            ->field('a.*,b.giftname,b.id as wawa_id,b.gifticon,b.wawa_no,b.spendcoin,c.deveci_no,c.device_addr,c.device_stock,d.addr,e.id as config_id,e.name as config_name')
            ->where($where)
            ->order('a.listorder')
            ->limit($page->firstRow.','.$page->listRows)
            ->select();

        $this->assign('row',$res);
        $this->assign('page',$page->show('Admin'));
        $this->assign('status_name',$this->statusarr);
        $this->assign('isshow_name',$this->isshowarr);
        $this->display();
    }


    public function get_game_config(){
        if( IS_AJAX ){
            $fac_id = I('fac_id');
            $res = M('device')->where(['device_unique_code'=>$fac_id])->find();
            $res = M('game_config')->find($res['game_config_id']);
            $this->ajaxReturn(['status'=>1,'row'=>$res]);
        }
    }


    public function edit()
    {
        $id = I('get.id');
        if( $id ){
            $row = M('game_room as a')
                ->where(['a.id'=>$id])
                ->join('left join cmf_gift as b on a.type_id = b.id')
                ->field('a.*,b.giftname,b.wawa_no,b.stock,b.gifticon')
                ->find();
            $row['proba_model'] = explode(',',$row['proba_model']);

            $wawaid = $row['type_id'];
            $types = M('gift')
                ->field('type_id')
                ->where('id ='.$wawaid)
                ->find();
            $row['typeid'] = $types['type_id'];
            $this->assign('row',$row);

            $giftData = $this->__getAllGift();
            $this->assign('typeData',$giftData['type']);
            $this->assign('typeDataJson',json_encode($giftData['type']));
            $this->assign('giftData',$giftData['gift']);
            $this->assign('typeid',$row['typeid']);

            /*echo "<pre>";
            print_r($giftData['gift'][$row['typeid']]);
            exit;*/
            $this->assign('giftDataJson',json_encode($giftData['gift']));

            $sql = "SELECT * FROM cmf_device WHERE device_unique_code NOT IN ((SELECT fac_id FROM cmf_game_room where id <> $id))";
            $device = M('device')->query($sql);
            $this->assign('device',$device);
            $bgmusic = M('bgmusic')->select();
            $this->assign('bgmusic',$bgmusic);
            /* 按钮音效 */
            $this->assign('anniu',M('game_audio')->where(['type'=>1])->select());
            /* 成功音效 */
            $this->assign('chenggong',M('game_audio')->where(['type'=>2])->select());
            /* 倒计时音效 */
            $this->assign('daojishi',M('game_audio')->where(['type'=>3])->select());
            /* 开始音效 */
            $this->assign('kaishi',M('game_audio')->where(['type'=>4])->select());
            /* 失败音效 */
            $this->assign('shibai',M('game_audio')->where(['type'=>5])->select());
            /* 下抓音效 */
            $this->assign('xiazhua',M('game_audio')->where(['type'=>6])->select());
        }
        $this->display();
    }


    public function edit_post()
    {
        if( IS_POST ){
            $id = I('get.id');
            $post = I('post.');
            if( empty($post['room_no']) )$this->error('房间号不能为空');
            if( empty($post['room_name']) )$this->error('房间名不能为空');
            //if( empty($post['video1_pull']) )$this->error('视频流1不能为空');
            //if( empty($post['video2_pull']) )$this->error('视频流2不能为空');
            $r = M('game_room')->where(['room_no'=>$post['room_no']])->find();
            if( $r && $id != $r['id'] )$this->error('房间号已存在');
            $post['proba_model'] = implode(',',$post['proba_model']);
            $file = $this->uploadImage($_FILES);
            $post['bgmusic'] = $file;
            if( strpos($file,'http') !== 0)unset($post['bgmusic']);
            $post['fac_id'] = M('device')->where(['id'=>$post['device_id']])->getField('device_unique_code');
			
			if(!$r['video1_push']){
				$time = time() + 86400 * 3650;
				$update = array(
					'video1_push' => getPushUrl('v1'.str_pad($id,11,'0',STR_PAD_LEFT), $time),
					'video1_pull' => getPlayUrl('v1'.str_pad($id,11,'0',STR_PAD_LEFT), $time),
					'video2_push' => getPushUrl('v2'.str_pad($id,11,'0',STR_PAD_LEFT), $time),
					'video2_pull' => getPlayUrl('v2'.str_pad($id,11,'0',STR_PAD_LEFT), $time),
					'audio_push' => getPushUrl('a1'.str_pad($id,11,'0',STR_PAD_LEFT), $time),
					'audio_pull' => getPlayUrl('a1'.str_pad($id,11,'0',STR_PAD_LEFT), $time),
				); 
				$post += $update;           
			}
			M('game_room')->where(['id'=>$id])->save($post);
			
            include_once THINK_PATH.'../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
            $api = \createRestAPI();
			if($r['status'] != $post['status']){
				$api->group_send_group_msg(false, '0', '{"type":15,"room_ids":"'.$id.'","status":'.$post['status'].'}');
			}			
            //创建腾讯IM群组
            $ret = $api->group_create_group2('AVChatRoom', strval($id), '', array('group_id' => strval($id)));
			if($ret['ActionStatus'] != 'OK' && $ret['ErrorCode'] !== 10021){
           		$this->error('修改成功, 腾讯群组创建失败, 请重新提交');
			}
           	$this->success('修改成功');
        }
    }


    public function handle_ajax()
    {
        if( IS_AJAX ){
            $id = I('id');
            $res = M('gift')->find($id);
            $this->ajaxReturn(['status'=>1,'wawa_no'=>$res['wawa_no'],'gifticon'=>$res['gifticon']]);
        }else{
            echo '数据错误';
        }
    }

    /**
     * ajax获取设备库存
     */
    public function get_device(){
        if( IS_AJAX ){
            $id = I('id');
            if($id){
                $res = M('device')->find($id);
                echo $res['device_stock'];
            }
        }
    }


    public function add()
    {
        $giftData = $this->__getAllGift();
        $this->assign('typeData',$giftData['type']);
        $this->assign('typeDataJson',json_encode($giftData['type']));
        $this->assign('giftData',$giftData['gift']);
        $this->assign('giftDataJson',json_encode($giftData['gift']));
        $sql = "SELECT * FROM cmf_device WHERE device_unique_code NOT IN ((SELECT fac_id FROM cmf_game_room))";
        $device = M('device')->query($sql);
        $this->assign('device',$device);
        $bgmusic = M('bgmusic')->select();
        $this->assign('bgmusic',$bgmusic);
        /* 按钮音效 */
        $this->assign('anniu',M('game_audio')->where(['type'=>1])->select());
        /* 成功音效 */
        $this->assign('chenggong',M('game_audio')->where(['type'=>2])->select());
        /* 倒计时音效 */
        $this->assign('daojishi',M('game_audio')->where(['type'=>3])->select());
        /* 开始音效 */
        $this->assign('kaishi',M('game_audio')->where(['type'=>4])->select());
        /* 失败音效 */
        $this->assign('shibai',M('game_audio')->where(['type'=>5])->select());
        /* 下抓音效 */
        $this->assign('xiazhua',M('game_audio')->where(['type'=>6])->select());
        $this->display();
    }
    public function __getAllGift(){
        $type = M('gift_type')
            ->select();
        $gift = M('gift')
            ->select();
        foreach ($type as $key=>$val){
            foreach ($gift as $k=>$v){
                if($val['id'] == $v['type_id']){
                    $giftAll[$val['id']][] = $v;
                }
            }
        }
        $giftAllData = [
            'type'=>$type,
            'gift'=>$giftAll
        ];
        return $giftAllData;
    }

    public function add_post()
    {
        if( IS_POST ){
            $post = I('post.');
            if( empty($post['room_no']) )$this->error('房间号不能为空');
            if( empty($post['room_name']) )$this->error('房间名不能为空');
            //if( empty($post['video1_pull']) )$this->error('视频流1不能为空');
            //if( empty($post['video2_pull']) )$this->error('视频流2不能为空');
            if( empty($post['wawa_num']) )$this->error('库存不能为空');
            if( empty($post['yj_kc']) )$this->error('预警库存不能为空');
            $r = M('game_room')->where(['room_no'=>$post['room_no']])->find();
            if( $r )$this->error('房间号已存在');
            if( !empty($post['proba_model']) ) $post['proba_model'] = implode(',',$post['proba_model']);
            $post['ctime'] = time();
            $post['fac_id'] = M('device')->where(['id'=>$post['device_id']])->getField('device_unique_code');
            if( !$post['claw_count'] )unset($post['claw_count']);
            $room_id = M('game_room')->add($post);
			$time = time() + 86400 * 3650;
			$update = array(
				'video1_push' => getPushUrl('v1'.str_pad($room_id,11,'0',STR_PAD_LEFT), $time),
				'video1_pull' => getPlayUrl('v1'.str_pad($room_id,11,'0',STR_PAD_LEFT), $time),
				'video2_push' => getPushUrl('v2'.str_pad($room_id,11,'0',STR_PAD_LEFT), $time),
				'video2_pull' => getPlayUrl('v2'.str_pad($room_id,11,'0',STR_PAD_LEFT), $time),
				'audio_push' => getPushUrl('a1'.str_pad($room_id,11,'0',STR_PAD_LEFT), $time),
				'audio_pull' => getPlayUrl('a1'.str_pad($room_id,11,'0',STR_PAD_LEFT), $time),
			);
			M('game_room')->where("id=$room_id")->save($update);
			
            include_once THINK_PATH.'../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
            $api = \createRestAPI();
            //创建腾讯IM群组
            $ret = $api->group_create_group2('AVChatRoom', strval($room_id), '', array('group_id' => strval($room_id)));
			if($ret['ActionStatus'] != 'OK'){
				$this->error('添加成功, 腾讯群组创建失败, 请返回列表编辑');
			}
            $this->success('添加成功');
        }
    }


    public function del()
    {
        $id = I('id');
        if( $id ){
            M('game_room')->where(['id'=>$id])->delete();

            include_once THINK_PATH.'../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
            $api = \createRestAPI();
            //删除腾讯IM群组
            $api->group_destroy_group(strval($id));

            $this->success('删除成功');
        }
    }


    //排序
    public function listorders() {
        $status = parent::_listorders(M('game_room'));
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }


}