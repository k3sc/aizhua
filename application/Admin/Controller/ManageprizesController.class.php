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

class ManageprizesController extends AdminbaseController
{
    public function index()
    {
        $where = ' 1=1 ';

        /*收集过滤条件*/
        $user_id = I('user_id');
        if($user_id){
            $where .= " and u.id={$user_id} ";
            $filter['user_id'] = $user_id;
        }
        $user_nicename = I('user_nicename');
        if($user_nicename){
            $where .= " and u.user_nicename='{$user_nicename}' ";
            $filter['user_nicename'] = $user_nicename;
        }

        $count = $this->getGameCount($where);
        $page  = $this->page($count,500);
        $prizedata = $this->getGameData($where,$page);


        /*
         *  连续抓取需要满足三个条件
         *  1、还是当前用户
         *  2、还在当前房间
         *  3、抓中娃娃之前
         */
        $start_userid = $prizedata[0]['user_id'];
        $start_roomid = $prizedata[0]['room_id'];
        $i = 0;
        /* 得到了分组之后的数据  一个连续抓取的过程为一个数组 */
        foreach ($prizedata as $key=>&$val){

            $val['cdate'] = date('Y-m-d H:i:s',$val['ctime']);
            /*foreach ($prizedata as $k=>$v){
                if($v['room_id']==$val['room_id']){
                    $roomData[$val['room_id']][$v['id']] = $v;
                }
            }*/

            if($val['success'] > 0){
                if($val['user_id']!=$start_userid || $val['room_id']!=$start_roomid){
                    $i++;
                    $start_userid = $prizedata[$key]['user_id'];
                    $start_roomid = $prizedata[$key]['room_id'];
                    $zhuaData[$i][] = $val;
                    $i++;
                    continue;
                }else{
                    $zhuaData[$i][] = $val;
                    $i++;
                    $start_userid = $prizedata[$key+1]['user_id'];
                    $start_roomid = $prizedata[$key+1]['room_id'];
                    continue;
                }
            }

            if($val['user_id']!=$start_userid || $val['room_id']!=$start_roomid){
                $i++;
                $start_userid = $prizedata[$key]['user_id'];
                $start_roomid = $prizedata[$key]['room_id'];
            }
            $zhuaData[$i][] = $val;

        }




        foreach ($zhuaData as $key=>$val){
            $success_arr = array_column($val,'success');
            $success_arr = array_unique($success_arr);
            if(array_sum($success_arr) <= 0 ){
                unset($zhuaData[$key]);
            }else{
                $resData[$key]['data'] = $val;
                //获取用户的等级
                $user = M('users')->field('id,user_nicename,avatar,mobile,coin,free_coin,create_time,user_status,claw,strong,coin_sys_give,openid,sys,sex,last_login_time,
                total_payed ,total_get,total_get_num,last_active_time')->where(['id'=>$val[0]['user_id']])->find();
                $res = A('Users')->getGrade($user);
                $resData[$key]['ext']['level'] = $res['title'];
                $resData[$key]['ext']['count'] = count($val);
            }
        }


        $this->assign('filter',$filter);
        $this->assign('row',array_values($resData));
//        $this->assign('row',$resData);
        $this->assign('rowJson',json_encode($resData));
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }

    public function getGameData($where,$page){
        $sql = "SELECT
                    h.id,h.room_id,h.user_id,h.success,h.ctime,h.is_strong,
                    u.user_nicename,u.avatar,
                    roomwawa.*
                FROM
                    cmf_game_history AS h
                    LEFT JOIN cmf_users AS u ON u.id = h.user_id
                    LEFT JOIN (
                SELECT
                    r.room_no,
                    r.room_name,
                    r.id AS room_id,
                    r.`status`,
                    r.type_id AS wawaid,
                    g.giftname,
                    g.id AS wawa_id,
                    g.gifticon,
                    g.spendcoin,
                    g.cost,
                    g.stock
                FROM
                    cmf_game_room AS r
                    LEFT JOIN cmf_gift AS g ON g.id = r.type_id 
                    ) as roomwawa on h.room_id=roomwawa.room_id
                WHERE {$where}    
                    LIMIT {$page->firstRow},{$page->listRows}";
        $result = M()->query($sql);
        return $result;
    }

    public function getGameCount($where){
        $sql = "SELECT
                    count(h.id) as count
                FROM
                    cmf_game_history AS h
                    LEFT JOIN cmf_users AS u ON u.id = h.user_id
                    LEFT JOIN (
                SELECT
                    r.room_no,
                    r.room_name,
                    r.id AS room_id,
                    r.`status`,
                    r.type_id AS wawaid,
                    g.giftname,
                    g.id AS wawa_id,
                    g.gifticon,
                    g.spendcoin,
                    g.cost,
                    g.stock
                FROM
                    cmf_game_room AS r
                    LEFT JOIN cmf_gift AS g ON g.id = r.type_id 
                    ) as roomwawa on h.room_id=roomwawa.room_id
                WHERE {$where}";
        $result = M()->query($sql);
        return $result[0]['count'];
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