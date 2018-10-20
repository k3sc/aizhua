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


    /*public function test(){
        //进行友盟推送app状态栏  title：娃娃退币  content：您连续次数达到保夹要求，共退币XXX币，祝你抓抓愉快咩~
        $umengpush = [
            'title'=>'保夹娃娃币退币',
            'content'=>"您连续次数达到保夹要求，共退币999999999999999币，祝你抓抓愉快咩~",
        ];
        //测试专用
        $userData = [
            'androidtoken'=>'Ah4Bvt1luAec0H62jLjJlS26PSmkl362KQcu7e80oon4',
            'iphonetoken'=>'464d62ef34d7cfa584b6265f41c54996ee531dcdc873859f34826b9527c38329'
        ];
        $this->umengpush($userData['androidtoken'],$userData['iphonetoken'],$umengpush['title'],$umengpush['content']);
    }*/

    public function index()
    {
        $where = ' 1=1 and continuity!=0 ';

        /*收集过滤条件*/
        $user_id = I('user_id');
        if($user_id){
            $where .= " and u.id={$user_id} ";
            $filter['user_id'] = $user_id;
        }
        $user_nicename = I('user_nicename');
        if($user_nicename){
            $where .= " and u.user_nicename like '%{$user_nicename}%' ";
            $filter['user_nicename'] = $user_nicename;
        }

        $count = $this->getGameCount($where);
        $page  = $this->page($count,20);
        $prizedata = $this->getGameData($where,$page);

        foreach ($prizedata as $key=>$val){
            /* 获取用户的记录信息 */
            $hData = M('game_history as h')->where("id in ({$val['h_id_s']})")->order("ctime desc")->select();
            $prizedata[$key]['history'] = $hData;



            //获取同一房间娃娃的最近200条记录
            $hh_data = M('game_history as h')->field("h.*,u.id,u.user_nicename")->join("left join cmf_users as u on u.id=h.user_id")->where("h.room_id={$val['room_id']}")->limit("0,200")->order("h.ctime desc")->select();
            $prizedata[$key]['hhistory'] = $hh_data;

            //退还币数
            $retreat = M('game_history')->where("id in ({$val['h_id_s']}) and is_retreat=1")->sum('coin');
            $prizedata[$key]['retreat'] = $retreat?:0;

            /*->field('h.*,u.user_nicename')->join('left join cmf_users as u on u.id=h.user_id')*/

            //获取用户的等级
            $user = M('users')->field('id,user_nicename,avatar,mobile,coin,free_coin,create_time,user_status,claw,strong,coin_sys_give,openid,sys,sex,last_login_time,
            total_payed ,total_get,total_get_num,last_active_time')->where(['id'=>$val['user_id']])->find();
            $usersData = A('Users')->getGrade($user);
            $prizedata[$key]['level'] = $usersData['title'];

            /*获取是否强抓力*/
            $prizedata[$key]['is_strong'] = end($hData)['is_strong'];
            //$prizedata[$key]['is_strong'] = end(explode(',',$val['h_is_strong_s']));

        }

        $this->assign('filter',$filter);
        $this->assign('row',array_values($prizedata));
        $this->assign('rowJson',json_encode($prizedata));
        $this->assign('page',$page->show('Admin'));
        $this->display();
    }
    public function getGameCount($where){
        $sql = "
            SELECT 
                count(ss.id) as count
            FROM (
            SELECT
                h.id,
                sum(h.success) as csuccess
            FROM
                cmf_game_history as h
            LEFT JOIN cmf_users as u on u.id=h.user_id
            LEFT JOIN (
                SELECT
                    g.id as wawa_id,
	                g.giftname,
	                g.gifticon,
	                g.spendcoin,
                    g.cost,
                    g.stock,
	                r.id AS room_id 
                FROM
	                cmf_game_room AS r
	            LEFT JOIN cmf_gift AS g ON r.type_id = g.id 
	        ) as wawa on wawa.room_id=h.room_id
            WHERE {$where}
            GROUP BY
                h.continuity
                HAVING csuccess > 0
              ) as ss
              ";
        $result = M()->query($sql);

        return $result[0]['count'];
    }
    public function getGameData($where,$page){
        $sql = "
            SELECT
                GROUP_CONCAT(h.id) as h_id_s,
                GROUP_CONCAT(h.is_strong) as h_is_strong_s,
                h.room_id,h.user_id,h.success,h.ctime,h.is_strong,
                u.user_nicename,u.avatar,
                count(h.id) as zhuacount,
                sum(h.success) as csuccess,
                wawa.*
            FROM
                cmf_game_history as h
            LEFT JOIN cmf_users as u on u.id=h.user_id
            LEFT JOIN (
                SELECT
                    g.id as wawa_id,
	                g.giftname,
	                g.gifticon,
	                g.spendcoin,
                    g.cost,
                    g.stock,
	                r.id AS room_id 
                FROM
	                cmf_game_room AS r
	            LEFT JOIN cmf_gift AS g ON r.type_id = g.id 
	        ) as wawa on wawa.room_id=h.room_id
            WHERE {$where}
            GROUP BY
                h.continuity
                HAVING csuccess > 0
            ORDER BY 
	            h.continuity desc
              LIMIT {$page->firstRow},{$page->listRows}
        ";
        $result = M()->query($sql);
        return $result;
    }

    public function inde2()
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
    public function getGameData2($where,$page){
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

    public function getGameCount2($where){
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

    public function updateGamehistory(){
        set_time_limit(0);
        Ignore_User_Abort(True);
        ini_set('memory_limit', '1024M');
        if(empty(M()->query("describe cmf_game_history continuity"))){
            M()->query("ALTER TABLE cmf_game_history ADD COLUMN continuity int(10) NOT NULL DEFAULT 1");
            M()->query("CREATE INDEX continuity ON cmf_game_history(continuity)");
        }

        $res = M('game_history')->field('id')->order('id asc')->select();
        foreach ($res as $key=>$val){
            if($key!=0){
                /*取出当前条数据与上一条对比相似度*/
                $rData = M('game_history')->where("id={$val['id']}")->find();
                /*取出上一条数据*/
                $sData = M('game_history')->where("id={$res[$key-1]['id']}")->find();
                /*优先判断上一组是否中奖*/
                if($sData['success']>0){
                    /*若上一组是中奖状态  则直接分配当前为新组  并且跳出当前循环*/
                    M('game_history')->where("id={$val['id']}")->save(['continuity'=> $sData['continuity']+1]);
                    continue;
                }

                /*抓中奖的情况下*/
                if($rData['success'] > 0){
                    /*抓中奖的情况下还是原来用户*/
                    if($rData['room_id']==$sData['room_id'] && $rData['user_id']==$sData['user_id']){
                        /*与上一列为同一组*/
                        M('game_history')->where("id={$val['id']}")->save(['continuity'=> $sData['continuity']]);
                    }
                    else{
                        /*抓中奖的情况下不是原来的用户  连续组+1 */
                        M('game_history')->where("id={$val['id']}")->save(['continuity'=> $sData['continuity']+1]);
                    }
                }
                /*没有抓中奖的情况下*/
                else{
                    /*没有抓中奖的情况下还是原来用户*/
                    if($rData['room_id']==$sData['room_id'] && $rData['user_id']==$sData['user_id']){
                        /*与上一组相同*/
                        M('game_history')->where("id={$val['id']}")->save(['continuity'=> $sData['continuity']]);
                    }
                    /*抓中奖的情况下不再是原来的用户  连续组+1 */
                    else{
                        M('game_history')->where("id={$val['id']}")->save(['continuity'=> $sData['continuity']+1]);
                    }

                }
            }


        }

    }

}