<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;
use Think\Log;

class UsersController extends AdminbaseController
{
	protected $users_model,$role_model;

    private $paytype = array(1 => '微信支付', 2 => '支付宝支付', 3 => 'Apple支付', 4 => 'Paypal支付');
    private $status = array(1 => '充值成功', 2 => '充值失败');

	function _initialize() {
		parent::_initialize();
		$this->users_model = D("Common/Users");
		$this->role_model = D("Common/Role");
	}
public function msglist(){
// 页面筛选
        $where = '1=1';
        $post = I('post.');
        if ($post['id']) { // 活动id
            $where .= ' and id=' . intval($post['id']);
        } else {
            // 启用时间
            if ($post['sdate'] && $post['edate']) {
                $where .= ' and created_at >= ' . intval(strtotime($post['sdate']));
                $where .= ' and created_at <= ' . intval(strtotime($post['edate'] . ' 23:59:59'));
            }
            // 活动状态
            if (isset($post['type']) && $post['type'] != '') {
                $where .= ' and type=' . $post['type'];
            }
            // 活动标题
            if ($post['title']) {
                $where .= ' and title like "%' . $post['title'] . '%"';
            }
//            // 活动标题
//            if ($post['about']) {
//                $where .= ' and title like "%' . $post['title'] . '%"';
//            }
        }

        $count = M('pushmsg')->where($where)->count();
        $page = $this->page($count, 20);
        $list = M('pushmsg')
            ->where($where)
            ->order("created_at DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $this->assign("list", $list);
        $this->assign("page", $page->show('Admin'));
        $this->display();
    }
	
	public function msg()
    {
	$ids = intval(I('users'));
	$t = I('t');
        if (IS_POST) {
            $post = I('post.');
            $post['title'] = trim($post['title']);
            if (empty($post['title'])) {
                $this->error("推送标题不能为空！");
            }
            $post['about'] = trim($post['about']);
            if (empty($post['about'])) {
                $this->error("推送简介不能为空！");
            }

            if (intval($post['type']) == 1) {
                $post['users'] = trim($post['users']);
                if (empty($post['users'])) {
                    $this->error("推送用户不能为空！");
                }
            }

            $ref =  U('index');
		if ($t)
            {
                if ($t == 'feedback')
                     $ref = U('Feedback/index');
                elseif($t == 'appeal')
                {
                    $ref = U('Appeal/index');
                }
            }
		$data = $post;
            $data['created_at'] = time();
            if ($post['type']==2){
                $data['users'] = '';
            }
            M('pushmsg')->add($data);
            if (empty($post['id'])) {
                $post['ctime'] = time();

                foreach (explode(',',$ids) as $id) {
                    // 发送消息提醒
                    $arrNotice = array();
                    $arrNotice['user_id'] = $id;
                    $arrNotice['type'] = 5;
                    $arrNotice['title'] = $post['title'];
                    $arrNotice['content'] = $post['about'];
                    $arrNotice['desc'] = $post['about'];
                    $arrNotice['ctime'] = time();
                    M('notice')->add($arrNotice);
                    /* 友盟推送 */
                    $row = M('users')->where(['id'=>$arrNotice['user_id']])->field('androidtoken,iphonetoken')->find();
                    $this->umengpush( $row['androidtoken'],$row['iphonetoken'],$arrNotice['title'],$arrNotice['content'] );
                }

                if (M('activity')->add($post)) {
                    $this->success("添加推送成功！",$ref);
                }
            }
        }


        if ($ids) {
            $this->assign("ids", $ids);
        }
	$this->assign("t", $t);

        $this->display();
    }



public function setWawa()
    {
        $where = '1=1';
        $post = array_merge(I('post.'),I('get.'));
        //$_GET = array_merge($_GET,$post);
        if ($post['tt_id'] && $post['tt_id']) {
            $_GET['tt_id'] =$post['tt_id'];
            $where .= ' and tt.id = "' . $post['tt_id'] . '"';
        }
        if ($post['gg_id'] && $post['gg_id']) {
            $_GET['gg_id'] =$post['gg_id'];
            $where .= ' and gg.id = "' . $post['gg_id'] . '"';
        }
        if ($post['ff_id'] && $post['ff_id']) {
            $_GET['ff_id'] =$post['ff_id'];
            $where .= ' and ff.id = "' . $post['ff_id'] . '"';
        }
        // 活动标题
        if ($post['tt_nickname']) {
            $_GET['tt_nickname'] =$post['tt_nickname'];
            $where .= ' and tt.user_nicename like "%' . $post['tt_nickname'] . '%"';
        }
        if ($post['ff_nickname']) {
            $_GET['ff_nickname'] =$post['ff_nickname'];
            $where .= ' and ff.user_nicename like "%' . $post['ff_nickname'] . '%"';
        }
        if ($post['giftname']) {
            $_GET['giftname'] =$post['giftname'];
            $where .= ' and giftname like "%' . $post['giftname'] . '%"';
        }



        $count =  M('set_wawas')->alias('a')
            ->join('cmf_users as ff on a.user_id=ff.id', left)
            ->join('cmf_users as tt on a.to_user_id=tt.id', left)
            ->join('cmf_user_wawas as wawa on a.user_wawas_id=wawa.id')
            ->join('cmf_gift as gg on gg.id=wawa.wawa_id')

           // ->join('cmf_user_wawas as fwawa on a.user_wawas_id = a.is_receive', left)
            ->order('a.id desc')->where($where)->count();


        $page = $this->page($count, 20);
        $list = M('set_wawas')->alias('a')
            ->field('ff.id ff_id,ff.user_nicename ff_nickname, tt.id tt_id,tt.user_nicename tt_nickname,gg.id gg_id,gg.giftname,a.ctime ')
            ->join('cmf_users as ff on a.user_id=ff.id', left)
            ->join('cmf_users as tt on a.to_user_id=tt.id', left)
            ->join('cmf_user_wawas as wawa on a.user_wawas_id=wawa.id')
            ->join('cmf_gift as gg on gg.id=wawa.wawa_id')


            ->where($where)
            ->order("a.ctime DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $this->assign("list", $list);
        $this->assign("page", $page->show('Admin'));
        $this->display();
    }

    public static function getGrade($user)
    {
        static $list = null;
        if ($list === null)
        {
            $list = M('user_grade')->order("order_num asc")->select();
        }
        foreach ($list as $item)
        {

            if ($item['payed'] && $user['total_payed'] >= $item['min_payed'] && $user['total_payed'] <= $item['max_payed'])
            {
                return $item;
            }
            if ($item['num'] &&$user['total_get_num'] >= $item['min_num'] && $user['total_get_num'] <= $item['max_num'])
            {
                return $item;
            }
            if ($item['shouru'] && $user['total_payed'] >0 && $user['total_get']/$user['total_payed'] > $item['shourubi'] && $item['shouruequal'] == 1 )
            {
                return $item;
            }
            if ($item['shouru'] && $user['total_payed']>0 && $user['total_get']/$user['total_payed'] < $item['shourubi'] && $item['shouruequal'] == 0 )
            {
                return $item;
            }

            if ($item['online'])
            { //echo $user['last_active_time'];echo (time()-$user['last_active_time'])/(24*3600);
                $h = M('game_history')->field('ctime')->where('success >= 0' )->order('id desc')->limit(1)->find();
                if (empty($h)||(time()-$h['ctime'])/(24*3600) > $item['onlinedays'])
                {
                    return $item;
                }

            }

        }
        return false;
    }


    //用户列表
    public function index()
    { //Log::record('sssssssfffffffffff',Log::ERR);
        $map = "user_type<>1";
        $name = I('name');
        $mobile = I('mobile');
        $start_time = I('start_time');
        $end_time = I('end_time');
        $user_id = I('user_id','','intval');
        //用户名搜索
        if ($name !== '') {
            $map .= " and user_nicename like '%$name%'";
            $this->assign('name', $name);
        }
        //用户id搜索
        if ($user_id) {
            $map .= " and id = $user_id ";
            $this->assign('user_id', $user_id);
        }
        //手机号搜索
        if ($mobile !== '') {
            $map .= " and mobile like '%$mobile%'";
            $this->assign('mobile', $mobile);
        }
        //时间段搜索
        if ($start_time !== '' && $end_time !== '') {
            $map .= " and unix_timestamp(create_time)>=" . strtotime($start_time . ' 00:00:00');
            $map .= " and unix_timestamp(create_time)<=" . strtotime($end_time . ' 23:59:59');
            $this->assign('start_time', $start_time);
            $this->assign('end_time', $end_time);
        }
//		}else{
//			if($start_time !== ''){
//				$map .=" and create_time>'{$start_time}'";
//				$this->assign('start_time',$start_time);
//			}
//			if($end_time !== ''){
//				$map .=" and create_time<='{$end_time}'";
//				$this->assign('end_time',$end_time);
//			}
//		}

        $model = M('users');
        $count = $model->where($map)->count();
        $page = $this->page($count, 20);
        //用户列表
        $list = $model
            ->field('id,user_nicename,avatar,mobile,coin,free_coin,create_time,user_status,claw,strong,coin_sys_give,openid,sys,sex,last_login_time,
            total_payed ,total_get,total_get_num,last_active_time')
            ->order('create_time desc')
            ->where($map)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
            
        foreach ($list as $key => $value) {
            //充值总金额
            $money = M('pay_record')->where("user_id='{$value['id']}' and status=1")->sum('money');
            $list[$key]['money'] = $money ?:0;
            //抓娃娃总次数
            $list[$key]['bodyNums'] = M('game_history')->where("user_id='{$value['id']}'")->count();
            //抓中次数
            $list[$key]['grasp'] = M('game_history')->where("user_id='{$value['id']}' and success > 0")->count();
            if ($grade = $this->getGrade($value))
            {
                $list[$key]['grade'] = $grade['title'];
               //    print_r($grade);exit;
            }
            else{
                $list[$key]['grade'] = '普通';
            }
        }
        
        $this->assign("page", $page->show('Admin'));//分页
        $this->assign('list', $list);               //用户列表
        $this->assign('count', $count);               //总条数
        $this->display();
    }


    public function video()
    {
        $url = I('url');
        $this->assign('url', $url);
        $this->display();
    }

    //封号
    public function delete()
    {
        $id = I('id');
        if (empty($id)) {
            $this->error('ID不能为空');
        }
        /* 发送通知消息 */
        $data['type'] = 6;
        $data['user_id'] = $id;
        $data['title'] = "账户变动";
        $data['content'] = "您的账号已被停封，请联系我们";
        $data['desc'] = "您的账号已被停封";
        $data['ctime'] = time();
        M('notice')->add($data);
        /* 友盟推送 */
        $row = M('users')->where(['id'=>$id])->field('androidtoken,iphonetoken')->find();
        $this->umengpush($row['androidtoken'],$row['iphonetoken'],$data['title'],$data['content']);
        /* 推送用户状态 */
        include_once './simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
        $api = \createRestAPI();
        $api->openim_send_msg(0, 'wawaji_'.$id, '{"type":16,"user_id":"'.$id.'","status":0}');

        M('users')->where("id=$id")->save(array('user_status' => 0));
        if (mysqli_affected_rows() >= 0) {
            $this->success('封号成功');
        } else {
            $this->error('封号失败');
        }
    }

    //解封
    public function relieve()
    {
        $id = I('id');
        if (empty($id)) {
            $this->error('ID不能为空');
        }
        /* 发送通知消息 */
        $data['type'] = 6;
        $data['user_id'] = $id;
        $data['title'] = "账户变动";
        $data['content'] = "您的账号已解封";
        $data['desc'] = "您的账号已解封";
        $data['ctime'] = time();
        M('notice')->add($data);
        /* 友盟推送 */
        $row = M('users')->where(['id'=>$id])->field('androidtoken,iphonetoken')->find();
        $this->umengpush($row['androidtoken'],$row['iphonetoken'],$data['title'],$data['content']);
        M('users')->where("id='{$id}'")->save(array('user_status' => 1));
        /* 推送用户状态 */
        include_once './simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
        $api = \createRestAPI();
        $api->openim_send_msg(0, 'wawaji_'.$id, '{"type":16,"user_id":"'.$id.'","status":1}');
        if (mysqli_affected_rows() >= 0) {
            $this->success('解封成功');
        } else {
            $this->error('解封失败');
        }
    }

    //用户娃娃列表
    public function body()
    {
        $statusArr = [0=>'寄存中',1=>'待邮寄',2=>'已发货',5=>'已确认'];
        $this->assign('statusArr',$statusArr);
        $id = I('id');
        if (empty($id)) {
            $this->error('ID不能为空');
        }
        $map = "a.user_id=$id";
        $this->assign('id', $id);

        $name = I('name') ? I('name') : '';
        $status = I('status');
	$_GET['status'] = $status;
        $_GET['id'] = $id;
        $_GET['name'] = $name;
        if ($name !== '') {
            $map .= " and b.giftname='{$name}'";
            $this->assign('name', $name);
        }
        if ($status >= 0) {
            $map .= " and a.status='{$status}' and a.is_del = 0";
            $this->assign('status', $status);
        }else{
            $map .= " and a.is_del = 0";
        }
        $model = M('user_wawas');
        $count = $model->alias('a')
            ->join('cmf_gift as b on b.id=a.wawa_id', left)
            ->join('cmf_gift_type as c on c.id=b.type_id', left)
            ->join('cmf_users as d on d.id = a.is_receive', left)
            ->order('a.id desc')->where($map)->count();
        $page = $this->page($count, 20);
        $list = $model->alias('a')
            ->join('cmf_gift as b on b.id=a.wawa_id', left)
            ->join('cmf_gift_type as c on c.id=b.type_id', left)
            ->join('cmf_users as d on d.id = a.is_receive', left)
            ->field('a.id,b.giftname as name,b.gifticon as img,c.name as class_name,a.status,a.is_del,a.is_receive,a.wawa_id,a.ctime,d.user_nicename')
            ->order('a.id desc')->where($map)
            ->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign('count', $count);
        $this->assign('page', $page->show('Admin'));
        $this->assign('list', $list);
        $this->display();
    }


    //快递列表
    public function express()
    {
        $id = I('id');
        if (empty($id)) {
            $this->error('ID不能为空');
        }
        $model = M('waybill');
        $map = "a.user_id=$id";
        $status = I('status') ? I('status') : '';
        if ($status !== '') {
            $map .= " and a.status=$status";
            $this->assign('status', $status);
        }
        $this->assign('id', $id);
        $count = $model->alias('a')->join('cmf_users as b on b.id=a.user_id')
            ->field('a.*,b.user_nicename')->where($map)->order('a.ctime desc')->count();
        $page = $this->page($count, 20);
        $list = $model->alias('a')
            ->join('cmf_users as b on b.id=a.user_id')
            ->field('a.*,b.user_nicename')
            ->where($map)
            ->order('a.ctime desc')
            ->select();
        foreach ($list as $key => $value) {
            if (!empty($value['user_wawas_id'])) {
                $id = M('user_wawas')->where("id=".$value['user_wawas_id'])->getField('wawa_id');
                $name = M('gift')->where('id=' . $id)->getField('giftname');
                $type = 1;
            } else {
                $id = M('users_gift')->where("id=".$value['user_gift_id'])->getField('gift_id');
                $name = M('give_gift')->where('id=' . $id )->getField('name');
                $type = 2;
            }
            $list[$key]['giftname'] = $name;
            $list[$key]['type'] = $status;
        }
        $this->assign('list', $list);
        $this->assign('count', $count);
        $this->assign('page', $page->show('Admin'));
        $this->display();
    }

    /**
     * 充值流水
     */
    public function bill()
    {
        $id = I('id');
        if ($id) {
            $count = M('pay_record')->where(['user_id' => $id])->count();
            $page = $this->page($count, 20);
            $row = M('pay_record')
            ->where(['user_id' => $id])
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order('ctime desc')
            ->select();
            $this->assign('row', $row);
            $this->assign('page', $page->show('Admin'));
            $this->assign('count', $count);
        }
        $this->assign('paytype', $this->paytype);
        $this->assign('status', $this->status);
        $this->display();
    }

    /**
     * 消费流水
     */
    public function consume()
    {
        $id = I('id');
        if ($id) {
            $count = M('users_coinrecord')->where(['uid' => $id,'action'=>['in','zhuawawa,service,claw']])->count();
            $page = $this->page($count, 20);
            $row = M('users_coinrecord')->where(['uid' => $id,'action'=>['in','zhuawawa,service,claw']])->limit($page->firstRow . ',' . $page->listRows)->order('addtime desc')->select();
            $this->assign('row', $row);
            $this->assign('page', $page->show('Admin'));
            $this->assign('count', $count);
        }
        $this->display();
    }


    /**
     * 系统赠送流水
     */
    public function sysbill()
    {
        $id = I('id');
        if ($id) {
            $count = M('users_coinrecord')
                ->where(['uid' => $id,'action'=>['in','regcoin,loginbonus,active_dingshi,active_shangbi,invite']])
                ->count();
            $page = $this->page($count, 20);
            $row = M('users_coinrecord')
                ->where(['uid' => $id,'action'=>['in','regcoin,loginbonus,active_shangbi,active_dingshi,invite']])
                ->limit($page->firstRow . ',' . $page->listRows)
                ->order('addtime desc')
                ->select();
            $this->assign('row', $row);
            $this->assign('page', $page->show('Admin'));
            $this->assign('count', $count);
        }
        $this->display();
    }

    /**
     * 游戏记录列表
     */
    public function game_list()
    {
        $id = I('id');
        if ($id) {
            $count = M('game_history')->where(['user_id' => $id])->count();
            $page = $this->page($count, 20);
            $row = M('game_history')
                ->alias('a')
                ->field('a.*, b.fac_id, b.room_no')
                ->join('cmf_game_room as b on a.room_id = b.id')
                ->where(['a.user_id' => $id])->limit($page->firstRow . ',' . $page->listRows)
                ->order('id desc')
                ->select();
            $this->assign('row', $row);
            $this->assign('page', $page->show('Admin'));
            $this->assign('count', $count);
        }

        $this->display();
    }

    protected function log_message($msg = ''){
        $file=THINK_PATH."../../data/runtime/log.txt";
        file_put_contents($file, date('Y-m-d H:i:s')."\r\n".var_export($msg, true)."\r\n\r\n",FILE_APPEND | LOCK_EX);
    }

    // 设置游戏记录成功或失败
    public function setSuccess()
    {
        $data['success'] = I('post.success');
        $id = I('post.id',0,'intval');
        if (isset($data['success']) && !empty($id)) {
            /* 设置成功 */
            if( !$data['success'] ){
                /* 从流水表中获取本次游戏的娃娃id,用户id */
                $res = M('users_coinrecord')->where(['showid'=>$id,'action'=>'zhuawawa'])->find();
                /* 取得娃娃id */
                $wawa_id = $res['giftid'];
                /* 取得用户id */
                $user_id = $res['uid'];
                /* 更新流水表中该条记录的娃娃数量 */
                M('users_coinrecord')->where(['showid'=>$id,'action'=>'zhuawawa'])->save(['giftcount'=>1]);
                /* cmf_user_wawas表中插入一条数据 */
                $insert_id = M('user_wawas')->add([
                    'wawa_id' => $wawa_id,
                    'user_id' => $user_id,
                    'ctime' => M('game_history')->where(['id'=>$id])->getField('ctime'),
                    'status' => 0,
                    'is_del' => 0,
                    'is_receive' => 0
                ]);
                /* 更新cmf_game_history表中 success字段 */
                M('game_history')->where(['id'=>$id])->save(['success'=>$insert_id]);
                /* 发送通知消息 */
                $data['type'] = 1;
                $data['user_id'] = $user_id;
                $data['title'] = "恭喜";
                $data['content'] = "你抓中了1个".M('gift')->where(['id'=>$wawa_id])->getField('giftname');
                $data['desc'] = $data['content'];
                $data['ctime'] = time();
                M('notice')->add($data);
                /* 友盟推送 */
                $row = M('users')->where(['id'=>$user_id])->field('androidtoken,iphonetoken')->find();

                $update['total_get'] = array('exp', 'total_get+'.(M('gift')->where(['id'=>$wawa_id])->getField('cost')));
                $update['total_get_num'] = array('exp', 'total_get_num+1');
                M('users')->where("id={$user_id}")->save($update);

                $this->umengpush($row['androidtoken'],$row['iphonetoken'],$data['title'],$data['content']);

//                $notice_data = $this->notice_add(1, $user_id, $data['title'],0,$data['desc'],$data['content']);
//                $this->notice_gameover('0', json_encode(array("type" => 12, "new_notice" => $notice_data, "timestamp" => microtime())));//推送消息

                $this->ajaxReturn(['res'=>$insert_id]);
            }else{ /* 设置失败 */
                /* 更新流水表中该条记录的娃娃数量 */
                M('users_coinrecord')->where(['showid'=>$id,'action'=>'zhuawawa'])->save(['giftcount'=>0]);
                /* 删除cmf_user_wawas表中该条记录 */
                M('user_wawas')->where(['id'=>$data['success']])->delete();
                /* 更新cmf_game_history表中 success字段 */
                M('game_history')->where(['id'=>$id])->save(['success'=>0]);
                $this->ajaxReturn(['res'=>0]);
            }

        }

    }


    /**
     * 用户地址列表
     */
    public function addr_list(){
        $id = I('id');
        if( $id ){
            $row = M('user_addr')->where(['user_id'=>$id])->select();
            $this->assign('row',$row);
        }
        $this->display();
    }

    /**
     * 用户申诉列表
     */
    public function service_list(){
        $id = I('id');
        if( $id ){
            $row = M('game_service as a')
                ->join('left join cmf_game_room as b on a.room_id = b.id')
                ->field('a.*,b.room_name,b.room_no')
                ->where(['a.user_id'=>$id])
                ->select();
            $this->assign('row',$row);
        }
        $this->display();
    }



    public function edit_user()
    {
        $id = I('id');
        if ($id) {
            $wawa_count = M('game_history')->where("user_id=$id")->count();
            $this->assign('wawa_count', $wawa_count);
            $wawa_count_success = M('game_history')->where("user_id = $id and success > 0")->count();
            $this->assign('wawa_count_success', $wawa_count_success);
            $row = M('users')->find($id);
            $this->assign('row', $row);
            /* 统计邀请人数 */
            $invite_count = M('users')->where(['pid'=>$id])->count();
            $this->assign('invite_count',$invite_count);
            /* 推荐人 */
            $invite_person = M('users')->where(['id'=>$row['pid']])->find();
            $this->assign('invite_person',$invite_person);

			$roles=$this->role_model->where("status=1")->order("id desc")->select();
			$this->assign("roles",$roles);
			$role_user_model=M("RoleUser");
			$role_ids=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
			$this->assign("role_ids",$role_ids);
        }
        if (IS_POST) {
            $id = I('get.id');
            $post = I('post.');

    		$role_ids=$post['role_id'];
    		unset($post['role_id']);
//print_r($post);exit;
            $post['free_coin_strong_num'] = intval($post['free_coin_strong_num'] );
            $post['coin_strong_num'] = intval($post['coin_strong_num'] );
            M('users')->where(['id' => $id])->save($post);
			
     		$role_user_model=M("RoleUser");
			$role_user_model->where(array("user_id"=>$id))->delete();
			foreach ($role_ids as $role_id){
				$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$id));
			}			
			
            $this->success('编辑成功');
        }
        $this->display();
    }




     function add(){
     	$roles=$this->role_model->where("status=1")->order("id desc")->select();
     	$this->assign("roles",$roles);
     	$this->display();
     }

     function add_post(){
     	if(IS_POST){
     		if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
     			$role_ids=$_POST['role_id'];
     			unset($_POST['role_id']);
     			if ($this->users_model->create()) {
     				$result=$this->users_model->add();
     				if ($result!==false) {
     					$role_user_model=M("RoleUser");
     					foreach ($role_ids as $role_id){
     						$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$result));
     					}
     					$this->success("添加成功！", U("user/index"));
     				} else {
     					$this->error("添加失败！");
     				}
     			} else {
     				$this->error($this->users_model->getError());
     			}
     		}else{
     			$this->error("请为此用户指定角色！");
     		}

     	}
     }


    // function edit(){
    // 	$id= intval(I("get.id"));
    // 	$roles=$this->role_model->where("status=1")->order("id desc")->select();
    // 	$this->assign("roles",$roles);
    // 	$role_user_model=M("RoleUser");
    // 	$role_ids=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
    // 	$this->assign("role_ids",$role_ids);

    // 	$user=$this->users_model->where(array("id"=>$id))->find();
    // 	$this->assign($user);
    // 	$this->display();
    // }

    // function edit_post(){
    // 	if (IS_POST) {
    // 		if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
    // 			if(empty($_POST['user_pass'])){
    // 				unset($_POST['user_pass']);
    // 			}
    // 			$role_ids=$_POST['role_id'];
    // 			unset($_POST['role_id']);
    // 			if ($this->users_model->create()) {
    // 				$result=$this->users_model->save();
    // 				if ($result!==false) {
    // 					$uid=intval($_POST['id']);
    // 					$role_user_model=M("RoleUser");
    // 					$role_user_model->where(array("user_id"=>$uid))->delete();
    // 					foreach ($role_ids as $role_id){
    // 						$role_user_model->add(array("role_id"=>$role_id,"user_id"=>$uid));
    // 					}
    // 					$this->success("保存成功！");
    // 				} else {
    // 					$this->error("保存失败！");
    // 				}
    // 			} else {
    // 				$this->error($this->users_model->getError());
    // 			}
    // 		}else{
    // 			$this->error("请为此用户指定角色！");
    // 		}

    // 	}
    // }

    // /**
    //  *  删除
    //  */
    // function delete(){
    // 	$id = intval(I("get.id"));
    // 	if($id==1){
    // 		$this->error("最高管理员不能删除！");
    // 	}

    // 	if ($this->users_model->where("id=$id")->delete()!==false) {
    // 		M("RoleUser")->where(array("user_id"=>$id))->delete();
    // 		$this->success("删除成功！");
    // 	} else {
    // 		$this->error("删除失败！");
    // 	}
    // }


    // function userinfo(){
    // 	$id=get_current_admin_id();
    // 	$user=$this->users_model->where(array("id"=>$id))->find();
    // 	$this->assign($user);
    // 	$this->display();
    // }

    // function userinfo_post(){
    // 	if (IS_POST) {
    // 		$_POST['id']=get_current_admin_id();
    // 		$create_result=$this->users_model
    // 		->field("user_login,user_email,last_login_ip,last_login_time,create_time,user_activation_key,user_status,role_id,score,user_type",true)//排除相关字段
    // 		->create();
    // 		if ($create_result) {
    // 			if ($this->users_model->save()!==false) {
    // 				$this->success("保存成功！");
    // 			} else {
    // 				$this->error("保存失败！");
    // 			}
    // 		} else {
    // 			$this->error($this->users_model->getError());
    // 		}
    // 	}
    // }

    //     function ban(){
    //        $id=intval($_GET['id']);
    //    	if ($id) {
    //    		$rst = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','0');
    //    		if ($rst) {
    //    			$this->success("管理员停用成功！", U("user/index"));
    //    		} else {
    //    			$this->error('管理员停用失败！');
    //    		}
    //    	} else {
    //    		$this->error('数据传入失败！');
    //    	}
    //    }

    //    function cancelban(){
    //    	$id=intval($_GET['id']);
    //    	if ($id) {
    //    		$rst = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','1');
    //    		if ($rst) {
    //    			$this->success("管理员启用成功！", U("user/index"));
    //    		} else {
    //    			$this->error('管理员启用失败！');
    //    		}
    //    	} else {
    //    		$this->error('数据传入失败！');
    //    	}
    //    }


}
