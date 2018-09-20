<?php
/**
 * Created by PhpStorm.
 * User: xiajunwei
 * Date: 2017/10/12
 * Time: 11:09
 */

namespace Api\Controller;

class MywawaController extends BaseController
{
    // 娃娃状态数组
    private $statusArr = ['寄存', '待邮寄', '已发货', '已转赠'];

    public function api()
    {
        $api_name = I('api_name');
        switch ($api_name) {
            case 'my':
                $this->my();
                break;
            case 'get_info':
                $this->get_info();
                break;
            case 'wawa_detail':
                $this->wawa_detail();
                break;
            case 'get_wawa_list':
                $this->get_wawa_list();
                break;
            case 'mail':
                $this->mail();
                break;
            case 'set_mail':
                $this->set_mail();
                break;
            case 'set_coin':
                $this->set_coin();
                break;
            case 'game_history':
                $this->game_history();
                break;
            case 'game_detail':
                $this->game_detail();
                break;
            case 'add_appeal':
                $this->add_appeal();
                break;
            case 'index':
                $this->index();
                break;
            case 'game_audience':
                $this->game_audience();
                break;
            case 'audience_detail':
                $this->audience_detail();
                break;
            case 'audience_wawa':
                $this->audience_wawa();
                break;
            case 'my_donation_wawa':
                $this->my_donation_wawa();
                break;
            case 'donation_wawa':
                $this->donation_wawa();
                break;
            case 'appeal_list':
                $this->appeal_list();
                break;
            case 'send_coin':
                $this->send_coin();
                break;
            case 'before_into_room':
                $this->before_into_room();
                break;
	    case 'wawa_list':
	    	$this->wawa_list();
		break;
            default:
                $this->_return(404, '接口不存在');
                break;
        }
    }

    // 获取我的娃娃上面个人信息
    public function get_info()
    {

        // 获取用户信息
        $userInfo = M('users')->field('id as user_id, user_nicename, avatar, sex')->where('id=' . $this->user_id)->find();
        // 总抓中次数
//        $total = M('user_wawas')->field('count(*) as total')->where('user_id=' . $this->user_id)->find();
        $total = M('game_history')->field('count(*) as total')->where('user_id=' . $this->user_id .' and success > 0')->find();
        // 转赠数量
        $ztotal = M('set_wawas')->field('count(*) as total')->where('user_id=' . $this->user_id)->find();
        // 获赠数量
        $gtotal = M('set_wawas')->field('count(*) as total')->where('to_user_id=' . $this->user_id)->find();
        $userInfo['total'] = $total['total'];
        $userInfo['ztotal'] = $ztotal['total'];
        $userInfo['gtotal'] = $gtotal['total'];

        if (strpos($userInfo['avatar'], 'http') === false) {
            $userInfo['avatar'] = 'http://' . $_SERVER['HTTP_HOST'] . $userInfo['avatar'];
        }

        $this->_return(1, '获取我的信息成功', $userInfo);
    }

    // 我的娃娃列表
    public function my()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $model = M('user_wawas');

        // 筛选
        $status = I('status');
        $strWhere = 'a.is_del = 0 and a.user_id=' . $this->user_id;

        if (isset($status) && $status != '') {
            if ($status == 4) {
                $strWhere .= ' and a.is_receive>0';
            } else {
                $strWhere .= ' and a.status=' . $status;
            }
        }

        $list = $model->alias('a')->join('left join cmf_gift as b on a.wawa_id=b.id')->join('cmf_game_history as c on a.id=c.success', 'left')->field('a.id as w_id, a.is_receive, a.ctime, a.status, b.giftname, b.needcoin, b.gifticon, ifnull(c.video,"") as video')->where($strWhere)->order('a.ctime desc')->page($page, $size)->select();

        $this->_return(1, '获取我的娃娃记录成功', $list);
    }

    // 娃娃详情
    public function wawa_detail()
    {
        //$this->log_message($_POST);
        $order_id = intval(I('wawa_id'));

        if (empty($order_id)) $this->_return(-1, '娃娃id不能为空');
        $arrInfo = M('user_wawas')->alias('a')->join('cmf_gift as b on a.wawa_id=b.id')->field('a.id, a.is_receive,a.status, a.ctime, b.giftname, b.gifticon')->where('a.id=' . $order_id)->find();
        if (empty($arrInfo)) $this->_return(-1, '该娃娃不存在');
        // 寄存中
        if ($arrInfo['status'] == 0) {

            if ($arrInfo['is_receive'] > 0) { // 获赠
                // 读取留言,转赠人id,转赠人头像,转赠人昵称
                $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.user_id, a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.user_wawas_id=' . $order_id)->find();

                $source['uid'] = $arr['user_id'];
                $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
                $source['user_nicename'] = $arr['user_nicename'];
            }

            $arrInfo['nums'] = 1; // 娃娃数量
            $arrInfo['source'] = $source; // 来源
            $arrInfo['msg'] = $arr['msg'] ? $arr['msg'] : ''; // 转赠留言

            $arrInfo['status'] = $arrInfo['status']; // 状态
            $arrInfo['remarks'] = ''; // 备注
            $arrInfo['express'] = ''; // 快递公司
            $arrInfo['ex_num'] = ''; // 运单编号

            // 待邮寄
        } else if ($arrInfo['status'] == 1) {
            if ($arrInfo['is_receive'] > 0) {
                // 读取留言,转赠人id,转赠人头像,转赠人昵称
                $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.user_id, a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.user_wawas_id=' . $order_id)->find();

                $source['uid'] = $arr['user_id'];
                $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
                $source['user_nicename'] = $arr['user_nicename'];
            } else {
                // 读取留言,转赠人id,转赠人头像,转赠人昵称
                if ($arrInfo['status'] == 3) {
                    $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.user_id, a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.old_wawas_id=' . $order_id)->find();
                    $source['uid'] = $arr['user_id'];
                    $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
                    $source['user_nicename'] = $arr['user_nicename'];
                }
            }

            // 获取运单表的邮寄备注
            $row = M('waybill')->field('IFNULL(remark,"") as remark')->where('user_wawas_id like "%' . $arrInfo['id'] . '%"')->find();

            $arrInfo['nums'] = 1; // 娃娃数量
            $arrInfo['source'] = $source; // 来源
//            if( $arrInfo['status'] != 3 ){
            $arrInfo['msg'] = $arr['msg'] ? $arr['msg'] : ''; // 转赠留言
//            }
            $arrInfo['status'] = $arrInfo['status']; // 状态
            $arrInfo['remarks'] = $row['remark']; // 备注
            $arrInfo['express'] = ''; // 快递公司
            $arrInfo['ex_num'] = ''; // 运单编号

            // 已发货
        } else if ($arrInfo['status'] == 2) {

            if ($arrInfo['is_receive'] > 0) {
                // 读取留言,转赠人id,转赠人头像,转赠人昵称
                $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.user_id, a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.user_wawas_id=' . $order_id)->find();

                $source['uid'] = $arr['user_id'];
                $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
                $source['user_nicename'] = $arr['user_nicename'];
            } else {
                // 读取留言,转赠人id,转赠人头像,转赠人昵称
                if ($arrInfo['status'] == 3) {
                    $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.user_id, a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.old_wawas_id=' . $order_id)->find();
                    $source['uid'] = $arr['user_id'];
                    $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
                    $source['user_nicename'] = $arr['user_nicename'];
                }
            }

            // 读取运单表
            $row = M('waybill')->field('remark, kdname, kdno')->where('user_wawas_id like "%' . $arrInfo['id'] . '%"')->find();
            $arrInfo['nums'] = 1; // 娃娃数量
            $arrInfo['source'] = $source; // 来源
//            if( $arrInfo['status'] != 3 ){
            $arrInfo['msg'] = $arr['msg'] ? $arr['msg'] : ''; // 转赠留言
//            }
            $arrInfo['status'] = $arrInfo['status']; // 状态
            $arrInfo['remarks'] = $row['remark'] ? $row['remark'] : ''; // 备注
            $arrInfo['express'] = $row['kdname'] ? $row['kdname'] : ''; // 快递公司
            $arrInfo['ex_num'] = $row['kdno'] ? $row['kdno'] : ''; // 运单编号

            // 已转赠
        } else if ($arrInfo['status'] == 3) {

            // 读取转赠留言
            $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.user_wawas_id=' . $order_id)->find();
            $source['uid'] = $arr['id'];
            $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
            $source['user_nicename'] = $arr['user_nicename'];
            $arrInfo['nums'] = 1; // 娃娃数量
            $arrInfo['source'] = $source; // 来源

//            if( $arrInfo['status'] != 3 ){
            $arrInfo['msg'] = $arr['msg'] ? $arr['msg'] : ''; // 转赠留言
//            }
            $arrInfo['status'] = $arrInfo['status']; // 状态
            $arrInfo['remarks'] = ''; // 备注
            $arrInfo['express'] = ''; // 快递公司
            $arrInfo['ex_num'] = ''; // 运单编号

        } else if ($arrInfo['status'] == 5) {    //已确认
            if ($arrInfo['is_receive'] > 0) {
                // 读取留言,转赠人id,转赠人头像,转赠人昵称
                $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.user_wawas_id=' . $order_id)->find();

                $source['uid'] = $arr['id'];
                $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
                $source['user_nicename'] = $arr['user_nicename'];
            } else {
                // 读取留言,转赠人id,转赠人头像,转赠人昵称
                if ($arrInfo['status'] == 3) {
                    $arr = M('set_wawas')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.user_id, a.msg, a.to_user_id, b.id, b.avatar_thumb, b.user_nicename')->where('a.old_wawas_id=' . $order_id)->find();
                    $source['uid'] = $arr['user_id'];
                    $source['avatar_thumb'] = $this->get_upload_path($arr['avatar_thumb']);
                    $source['user_nicename'] = $arr['user_nicename'];
                }
            }
            // 读取运单表
            $row = M('waybill')->field('remark, kdname, kdno')->where('user_wawas_id like "%' . $arrInfo['id'] . '%"')->find();
            $arrInfo['nums'] = 1; // 娃娃数量
            $arrInfo['source'] = $source; // 来源
//            if( $arrInfo['status'] != 3 ){
            $arrInfo['msg'] = $arr['msg'] ? $arr['msg'] : ''; // 转赠留言
//            }
            $arrInfo['status'] = $arrInfo['status']; // 状态
            $arrInfo['remarks'] = $row['remark'] ? $row['remark'] : ''; // 备注
            $arrInfo['express'] = $row['kdname'] ? $row['kdname'] : ''; // 快递公司
            $arrInfo['ex_num'] = $row['kdno'] ? $row['kdno'] : ''; // 运单编号
        }

        // 获取原始wawaid
        $arrInfo['ori_id'] = $this->get_source_wawa_id($order_id);

        $this->_return(1, '获取娃娃详细成功', $arrInfo);
    }

    // 递归获取转赠娃娃原始id
    public function get_source_wawa_id($wawa_id){
        $arr = M('set_wawas')->field('old_wawas_id')->where('user_wawas_id='.$wawa_id)->find();
        if(!$arr){
            return $wawa_id;
        }else{
            return $this->get_source_wawa_id($arr['old_wawas_id']);
        }
    }

    // 获取娃娃(换币或者邮寄)
    public function get_wawa_list()
    {

        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 1000;
        $type = I('type');//1换币  2邮寄
        //if( $type != 1 && $type != 2 ) $this->_return(-1,'type只能是1或者2');
        //if( $type == 1 ){
            //$strWhere = 'a.user_id=' . $this->user_id . ' and a.status=0 and a.is_del=0 and find_in_set(2,b.`convert`)';
        //}else{
            $strWhere = 'a.user_id=' . $this->user_id . ' and a.status=0 and a.is_del=0';
        //}

        $list = M('user_wawas')->alias('a')->join('cmf_gift as b on a.wawa_id=b.id')->field('a.wawa_id,count(*) as total, b.giftname, b.gifticon, b.needcoin')->where($strWhere)->group('a.wawa_id')->page($page, $size)->select();

        $this->_return(1, '获取我的娃娃数量成功', $list);
    }
    
    
    // 获取娃娃(换币或者邮寄)
    public function wawa_list()
    {

        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $type = I('type');//1换币  2邮寄
        if( $type != 1 && $type != 2 ) $this->_return(-1,'type只能是1或者2');
        if( $type == 1 ){
            //$strWhere = 'a.user_id=' . $this->user_id . ' and a.status=0 and a.is_del=0 and find_in_set(2,b.`convert`)';
        }else{
            $strWhere = 'a.user_id=' . $this->user_id . ' and a.status=0 and a.is_del=0';
        }

        $list = M('user_wawas')->alias('a')->join('cmf_gift as b on a.wawa_id=b.id')->field('a.wawa_id,count(*) as total, b.giftname, b.gifticon, b.needcoin')->where($strWhere)->order('a.ctime desc')->group('a.wawa_id')->page($page, $size)->select();

        $this->_return(1, '获取我的娃娃数量成功', $list);
    }
    

    // 确定邮寄娃娃
    public function mail()
    {
        $wawaList = I('w_list');
        if (empty($wawaList)) $this->_return(0, '要邮寄的娃娃不能为空');
        $this->_return(1, '邮寄娃娃添加成功', $wawaList);
    }

    // 选择收货地址和邮寄备注
    public function set_mail()
    {
        /*    $data['billno'] = I('billno');
            if (empty($data['billno'])) {
                $this->_return(0, '运单号不能为空', array());
            }
        */
        $data['addr_id'] = I('addr_id');
        if (empty($data['addr_id'])) {
            $this->_return(0, '地址id不能为空', array());
        }

        $wawaList = I('w_list');
        if (empty($wawaList)) $this->_return(0, '要邮寄的娃娃不能为空');
        $getArr = array();
        $arr = explode(',', $wawaList);
        foreach ($arr as $k => $v) {
            $getArr[$k] = explode(':', $v);
        }
        unset($wawaList);

        $model = M('waybill');
        $data1['waybillno'] = $this->get_orderno();//运单号
        //file_put_contents('./data/runtime/test.txt', var_export($data1['waybillno'], true));
        $data1['ctime'] = time();            //运单创建

        //更改用户娃娃表的状态
        foreach ($getArr as $key => $value) {
            if (intval($value[1]) <= 0) break;
            $list = M('user_wawas')->field('id')->where('wawa_id=' . $value[0] . ' and status=0 and is_del=0 and user_id=' . $this->user_id)->limit($value[1])->select();
            if (!$list) $this->_return(-1, '没有可邮寄的娃娃');
            foreach ($list as $v) {
                // 添加运单
                $data1['status'] = 1;                    //运单状态
                $data1['user_id'] = $this->user_id;        //用户id
                $data1['addr_id'] = '';        //收货地址id
                $data1['user_wawas_id'] = $v['id'];                //我的娃娃表id
                $data1['wawa_nums'] = 1;                    //数量
                $data1['remark'] = '';            //备注
                $model->add($data1);
                // 更新娃娃表为待邮寄状态
                M('user_wawas')->where('id=' . $v['id'])->save(array('status' => 1));
            }
        }

        $data['billno'] = $data1['waybillno'];

        $data['remark'] = trim(I('remark'));

        $res = M('user_addr')->where(['addr_id' => $data['addr_id']])->find();
        $data['uname'] = $res['username'];
        $data['phone'] = $res['mobile'];
        $data['addr'] = $res['addr'] . $res['addr_info'];

        M('waybill')->where('waybillno="' . $data['billno'] . '"')->save($data);
        $this->_return(1, '邮寄娃娃操作成功', $data['waybillno']);
    }

    // 娃娃换取娃娃币 换取 当作充值
    public function set_coin()
    {
        $wawaList = I('w_list');

        if (empty($wawaList)) $this->_return(0, '要换币的娃娃不能为空');

        $getArr = array();
        $arr = explode(',', $wawaList);
        foreach ($arr as $k => $v) {
            $getArr[$k] = explode(':', $v);
        }
        unset($wawaList);
        $coins = 0;
	foreach ($getArr as $k => $v) {
            if (intval($v[1]) <= 0) break;
            $list = M('user_wawas')->alias('a')->field('a.id,a.wawa_id,b.needcoin')->join('cmf_gift as b on a.wawa_id=b.id')->where('a.wawa_id=' . $v[0] . ' and a.status=0 and a.is_del=0 and a.user_id=' . $this->user_id. ' and find_in_set(2,b.`convert`)')->limit($v[1])->select();

            foreach ($list as $val) {
	    	$coins += $val['needcoin'];
                M('users')->where('id=' . $this->user_id)->setInc('coin', $val['needcoin']);
		$cost = M('gift')->where(['id'=>$val['wawa_id']])->getField('cost');
                M('users')->where('id=' . $this->user_id)->setDec('total_get', $cost);
                $data['is_del'] = 1;
                M('user_wawas')->where('id=' . $val['id'])->save($data);
            }
        }
        //流水记录
        if ($coins) {
            $insert = array("type" => 'income', "action" => 'set_coin', "uid" => $this->user_id, "totalcoin" => $coins, "addtime" => time());
            M('users_coinrecord')->add($insert);
        }
        $this->_return(1, '娃娃换币成功', array());
    }

    // 游戏记录列表
    public function game_history()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;

        $strWhere = 'user_id=' . $this->user_id;
        $list = M('game_history')->field('id as hid, room_id, user_id, name, img, success, ctime, video')->where($strWhere)->order('ctime desc')->page($page, $size)->select();
        $this->_return(1, '获取游戏历史记录成功', $list);
    }

    // 游戏详情
    public function game_detail()
    {
        $id = intval(I('hid'));
        if (empty($id)) $this->_return(0, '记录的id不能为空');
        $arrInfo = M('game_history')->field('id as hid, room_id, user_id, name, video, img, success, ctime')->where('id=' . $id)->find();
        $this->_return(1, '获取游戏记录详情成功', $arrInfo);
    }

    // 用户添加申诉
    public function add_appeal()
    {
        $data['game_id'] = intval(I('hid')); // 游戏记录id
        $data['appeal_type'] = trim(I('appeal_type')); // 申诉理由

        if (empty($data['game_id'])) $this->_return(0, '申诉的游戏记录id不能为空');
        if (empty($data['appeal_type'])) $this->_return(0, '申诉的理由必须选择');
        // 查找是否已申诉过了
        $row = M('game_appeal')->where('user_id=' . $this->user_id . ' and game_id=' . $data['game_id'])->find();
        if ($row) $this->_return(0, '该记录你已申诉,请不要重复申诉');
        // 读取游戏记录
        $arrInfo = M('game_history')->where('id=' . $data['game_id'])->find();
        $data['user_id'] = $this->user_id;
        $data['video'] = $arrInfo['video'];
        $data['ctime'] = time();
        M('game_appeal')->add($data);
        $this->_return(1, '申诉提交成功');
    }

	public function roomwait_num($room_id){
        include_once THINK_PATH . '../../simplewind/Lib/Extend/TPRedis.php';
        $redis = new \TPRedis(array('host' => C('REDIS_HOST'), 'auth' => C('REDIS_AUTH'), 'prefix' => C('REDIS_PREFIX')));
        $now_game = $redis->get('roomgame_' . $room_id);
		$gamelock = $redis->get('gamelock_' . $room_id);
		if($gamelock && time() >= $gamelock['time']){
			$redis->delete('gamelock_' . $room_id);
			$gamelock = null;
		}
        //排队总人数//如果有人正在玩+1
        $count = $redis->lSize('roomwaits_' . $room_id);
		$count = $count + ($gamelock || $now_game ? 1 : 0);
		$redis->set('roomnums_'.$room_id, $count);
		return $count;
	}

    // 首页娃娃列表
    public function index()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;

        $list = M('game_room')
            ->alias('a')
            ->join('cmf_gift as b on a.type_id=b.id')
            ->join('cmf_gift_label as c on b.label_id = c.id', 'left')
            ->field('a.id as room_id, a.status,a.room_name, b.id as wawa_type_id,b.giftname, b.spendcoin, b.gifticon, ifnull(c.name, "") as label')
            ->where(['is_show' => 1])
            ->order('a.listorder')
            ->page($page, $size)
            ->select();

        foreach ($list as $k => $v) {
            $list[$k]['wait_nums'] = $this->roomwait_num($v['room_id']);
			$list[$k]['gifticon'] = $this->get_upload_path($v['gifticon']);
        }

        // 获取活动进行送币
        $this->send_coin();
	//写入此次登录信息
        $data = array(
            'last_login_time' => date("Y-m-d H:i:s"),
        );
        $users_model=D("Users");//实例化Common模块下的Users模型
        $users_model->where(array('id'=>$this->user_id))->save($data);

        $this->_return(1, '获取房间列表成功', $list);

    }

    // 获取活动进行送币
    public function send_coin()
    {
        $sdate = strtotime(date('Y-m-d 00:00:00'));
        $edate = strtotime(date('Y-m-d 23:59:59'));
        $list = array();
        $list = M('timing_activity')->field('*')->where('start_date <= ' . $sdate . ' and end_date >= ' . $edate . ' and status = 1')->select();

        foreach ($list as $k => $v) {
            // 单次活动
            if ($v['type'] == 1) {
                // 读取是否已送过
                $arrTemp = M('user_act')->field('id')->where('act_id=' . $v['id'] . ' and user_id=' . $this->user_id)->find();
                if (!$arrTemp) { // 没有送过才送
                    M('users')->where('id=' . $this->user_id)->setInc('coin', $v['coin']);
                    M('users')->where('id=' . $this->user_id)->setInc('free_coin', $v['coin']);
                    M('users')->where('id=' . $this->user_id)->setInc('active_coin', $v['coin']);
                    // 记录标识已送过币
                    $arr = array();
                    $arr['act_id'] = $v['id'];
                    $arr['user_id'] = $this->user_id;
                    $arr['coin'] = $v['coin'];
                    $arr['ctime'] = time();
                    M('user_act')->add($arr);
                    // 发送消息提醒
                    $arrNotice = array();
                    $arrNotice['user_id'] = $this->user_id;
                    $arrNotice['title'] = $v['title'];
                    $arrNotice['content'] = $v['about'];
                    $arrNotice['desc'] = $v['about'];
                    $arrNotice['ctime'] = time();
                    M('notice')->add($arrNotice);
                    /* 友盟推送 */
                    $androidtoken = M('users')->where(['id' => $this->user_id])->getField('androidtoken');
                    $iphonetoken = M('users')->where(['id' => $this->user_id])->getField('iphonetoken');
                    $this->umengpush($androidtoken, $iphonetoken, $arrNotice['title'], $arrNotice['content']);
                    //流水表
                    $insert = array("type" => 'income', "action" => 'active_dingshi', "uid" => $this->user_id, "touid" => 0, "giftid" => 0, "giftcount" => 0, "totalcoin" => $v['coin'], "givecoin" => 0, "giveclaw" => 0, "realmoney" => 0, "givemoney" => 0, "showid" => $v['id'], "addtime" => time());
                    M('users_coinrecord')->add($insert);


                }
            } else { // 周期活动

                if ($v['is_every_day'] == 1) { // 每天

                    // 读取今天是否已送过
                    $sday = strtotime(date('Y-m-d'));
                    $eday = strtotime(date('Y-m-d 23:59:59'));
                    $arrTemp = M('user_act')->field('id')->where('act_id=' . $v['id'] . ' and user_id=' . $this->user_id . ' and ctime >= ' . $sday . ' and ctime <= ' . $eday)->find();
                    if (!$arrTemp) { // 今天没有送过才送币
                        M('users')->where('id=' . $this->user_id)->setInc('coin', $v['coin']);
                        M('users')->where('id=' . $this->user_id)->setInc('free_coin', $v['coin']);
                        M('users')->where('id=' . $this->user_id)->setInc('active_coin', $v['coin']);
                        // 记录标识今天已送过币
                        $arr = array();
                        $arr['act_id'] = $v['id'];
                        $arr['user_id'] = $this->user_id;
                        $arr['coin'] = $v['coin'];
                        $arr['ctime'] = time();
                        M('user_act')->add($arr);

                        // 发送消息提醒
                        $arrNotice = array();
                        $arrNotice['user_id'] = $this->user_id;
                        $arrNotice['title'] = $v['title'];
                        $arrNotice['content'] = $v['about'];
                        $arrNotice['desc'] = $v['about'];
                        $arrNotice['ctime'] = time();
                        M('notice')->add($arrNotice);
                        /* 友盟推送 */
                        $androidtoken = M('users')->where(['id' => $this->user_id])->getField('androidtoken');
                        $iphonetoken = M('users')->where(['id' => $this->user_id])->getField('iphonetoken');
                        $this->umengpush($androidtoken, $iphonetoken, $arrNotice['title'], $arrNotice['content']);
                        //流水表
                        $insert = array("type" => 'income', "action" => 'active_dingshi', "uid" => $this->user_id, "touid" => 0, "giftid" => 0, "giftcount" => 0, "totalcoin" => $v['coin'], "givecoin" => 0, "giveclaw" => 0, "realmoney" => 0, "givemoney" => 0, "showid" => $v['id'], "addtime" => time());
                        M('users_coinrecord')->add($insert);

                    }

                } else {  // 周期的星期几送
                    // 获取今天是星期几
                    $week = date("w");
                    if ($week == 0) {
                        $week = 7;
                    }
                    if (strpos($v['str_week'], $week) !== false) {
                        // 读取今天是否已送过
                        $sday = strtotime(date('Y-m-d 00:00:00'));
                        $eday = strtotime(date('Y-m-d 23:59:59'));
                        $arrTemp = M('user_act')->field('id')->where('act_id=' . $v['id'] . ' and user_id=' . $this->user_id . ' and ctime >= ' . $sday . ' and ctime <= ' . $eday)->find();
                        if (!$arrTemp) { // 今天没有送过才送币
                            M('users')->where('id=' . $this->user_id)->setInc('free_coin', $v['coin']);
                            M('users')->where('id=' . $this->user_id)->setInc('coin', $v['coin']);
                            M('users')->where('id=' . $this->user_id)->setInc('active_coin', $v['coin']);
                            // 记录标识今天已送过币
                            $arr = array();
                            $arr['act_id'] = $v['id'];
                            $arr['user_id'] = $this->user_id;
                            $arr['coin'] = $v['coin'];
                            $arr['ctime'] = time();
                            M('user_act')->add($arr);

                            // 发送消息提醒
                            $arrNotice = array();
                            $arrNotice['user_id'] = $this->user_id;
                            $arrNotice['title'] = $v['title'];
                            $arrNotice['content'] = $v['about'];
                            $arrNotice['desc'] = $v['about'];
                            $arrNotice['ctime'] = time();
                            M('notice')->add($arrNotice);
                            /* 友盟推送 */
                            $androidtoken = M('users')->where(['id' => $this->user_id])->getField('androidtoken');
                            $iphonetoken = M('users')->where(['id' => $this->user_id])->getField('iphonetoken');
                            $this->umengpush($androidtoken, $iphonetoken, $arrNotice['title'], $arrNotice['content']);
                            //流水表
                            $insert = array("type" => 'income', "action" => 'active_dingshi', "uid" => $this->user_id, "touid" => 0, "giftid" => 0, "giftcount" => 0, "totalcoin" => $v['coin'], "givecoin" => 0, "giveclaw" => 0, "realmoney" => 0, "givemoney" => 0, "showid" => $v['id'], "addtime" => time());
                            M('users_coinrecord')->add($insert);
                        }
                    }
                }
            }
        }

    }

    // 首页房间观众列表
    public function game_audience()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;

        $room_id = intval(I('room_id'));
        if (empty($room_id)) $this->_return(0, '房间id不能为空');
        $list = M('game_audience')->alias('a')->join('cmf_users as b on a.user_id=b.id')->field('a.user_id, a.ctime, b.user_nicename, b.avatar')->where('room_id=' . $room_id)->order('a.id desc')->page($page, $size)->select();
        foreach ($list as $k => $v) {
            // 获取观众最后一次游戏的视频
            $arr = M('game_history')->field('video')->order('ctime desc')->where('user_id=' . $v['user_id'])->find();
            if ($arr) {
                $list[$k]['video'] = $arr['video'];
            } else {
                $list[$k]['video'] = '无';
            }
            $list[$k]['avatar'] = $this->get_upload_path($v['avatar']);
        }
//        $info['code'] = 1;
//        $info['msg'] = '获取房间观众列表成功';
//        $info['data'] = $list;
//        $info['total'] = M('game_audience')->alias('a')->join('cmf_users as b on a.user_id=b.id')->where('a.room_id=' . $room_id)->count();
//        $this->ajaxReturn($info);
        $total = M('game_audience')->alias('a')->join('cmf_users as b on a.user_id=b.id')->where('a.room_id=' . $room_id)->count();
        $this->_return(1, '获取房间观众列表成功', $list, array('total' => $total));
    }

    /**
     * 进入房间前检查人数是否超过上限
	 //api/room/api room_info判断
     */
    public function before_into_room(){
		/*
        $room_id = I('room_id',0,'intval');
        if( !M('game_room')->find($room_id) ) $this->_return(-1,'房间id不存在');
        // 房间人数 
        $waitNum = M('game_audience')->alias('a')->join('cmf_users as b on a.user_id=b.id')->where('a.room_id=' . $room_id)->count();
        // 房间上限人数 
        $roomMaxNum = M('game_room')->where(['id'=>$room_id])->getField('max_user');
        if( $waitNum >= $roomMaxNum )
            $this->_return(-1,'房间人数超过上限');
        $this->_return(1,'success');
		*/
		 $this->_return(1,'success');
    }

    // 观众个人信息
    public function audience_detail()
    {
        $user_id = intval(I('user_id'));
        if (empty($user_id)) $this->_return(0, '观众id不能为空');
        // 获取用户信息
        $userInfo = M('users')->field('id as user_id, user_nicename, avatar, sex')->where('id=' . $user_id)->find();
        // 总抓中洗漱
        $total = M('game_history')->field('count(*) as total')->where('user_id=' . $user_id . ' and success > 0')->find();
        // 转赠数量
        $ztotal = M('set_wawas')->field('count(*) as total')->where('user_id=' . $user_id)->find();
        // 获赠数量
        $gtotal = M('set_wawas')->field('count(*) as total')->where('to_user_id=' . $user_id)->find();
        $userInfo['total'] = $total['total'];
        $userInfo['ztotal'] = $ztotal['total'];
        $userInfo['gtotal'] = $gtotal['total'];
        $userInfo['avatar'] = $this->get_upload_path($userInfo['avatar']);
        $this->_return(1, '获取观众信息成功', $userInfo);
    }

    // 观众娃娃列表
    public function audience_wawa()
    {
        $user_id = intval(I('user_id'));
        if (empty($user_id)) $this->_return(0, '观众id不能为空');
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $model = M('user_wawas');
        $strWhere = 'a.status = 0 and a.is_del = 0 and a.user_id=' . $user_id;
        $list = $model->alias('a')->join('cmf_gift as b on a.wawa_id=b.id')->join('cmf_game_history as c on a.id=c.success')->field('a.id as w_id, a.is_receive, a.ctime, a.status, b.giftname, b.needcoin, b.gifticon, c.video')->where($strWhere)->order('a.ctime desc')->page($page, $size)->select();
        $this->_return(1, '获取观众娃娃记录成功', $list);
    }

    // 获取自己可转赠的娃娃
    public function my_donation_wawa()
    {
        $page = I('page',1,'intval');
        $size = I('size',1000,'intval');
        $strWhere = 'a.user_id=' . $this->user_id . ' and a.status=0 and is_del=0';
        $list = M('user_wawas')->alias('a')->join('cmf_gift as b on a.wawa_id=b.id')->field('a.wawa_id,count(*) as total, b.giftname,b.gifticon')->where($strWhere)->order('a.ctime desc')->group('a.wawa_id')->page($page, $size)->select();
        $this->_return(1, '获取可转赠娃娃成功', $list);
    }

    // 转赠娃娃
    public function donation_wawa()
    {
        $wawaList = I('w_list');
        if (empty($wawaList)) $this->_return(0, '要转赠的娃娃不能为空');
        // 转赠的目标用户
        $to_user_id = intval(I('to_user_id'));
        if ($to_user_id == $this->user_id) $this->_return(-1, '不能送给自己');
        if (empty($to_user_id)) $this->_return(0, '转赠的目标用户不能为空');
        $msg = trim(I('msg'));
        $getArr = array();
        $arr = explode(',', $wawaList);
        foreach ($arr as $k => $v) {
            $getArr[$k] = explode(':', $v);
        }
        unset($wawaList);
        //更改用户娃娃表的状态
        foreach ($getArr as $key => $value) {
            if (intval($value[1]) <= 0) break;
            $list = M('user_wawas')->field('id,wawa_id,ctime')->where('wawa_id=' . $value[0] . ' and status=0 and is_del=0 and user_id=' . $this->user_id)->limit($value[1])->select();
            foreach ($list as $v) {
                $data = array();
                // 添加一条转赠娃娃
                $data['wawa_id'] = $v['wawa_id'];
                $data['user_id'] = $to_user_id;
                $data['ctime'] = $v['ctime'];
                $data['is_receive'] = $this->user_id;
//                $data['old_id'] = $v['id'];
                $resultId = M('user_wawas')->add($data);
                // 添加转赠记录
                $data1 = array();
                $data1['user_wawas_id'] = $resultId; // 新生成的娃娃id
                $data1['old_wawas_id'] = $v['id']; // 原始娃娃id
                $data1['user_id'] = $this->user_id;
                $data1['to_user_id'] = $to_user_id;
                $data1['ctime'] = $v['ctime'];
                $data1['msg'] = $msg;
                M('set_wawas')->add($data1);
                // 更新娃娃表为转赠状态
                M('user_wawas')->where('id=' . $v['id'])->save(array('status' => 3));
            }
        }

        /* 计算娃娃个数 */
        $num = 0;
        foreach ($getArr as $v) {
            $num += $v[1];
        }
        $user = $this->user['user_nicename'];
        $title = "获赠娃娃";
        $desc = $user . " 送了" . $num . '个娃娃给你';
        $content = $user . " 送了" . $num . '个娃娃给你';
        $notice_data = $this->notice_add(3, $to_user_id, $title, $this->user_id, $desc, $content);
        $this->notice_gameover('0', json_encode(array("type" => 14, "new_notice" => $notice_data, "timestamp" => microtime())));//推送消息

        $this->_return(1, '转赠娃娃成功');
    }


    private function notice_gameover($room_id, $msg)
    {
        include_once THINK_PATH . '../../simplewind/Lib/Extend/TIMServerSdk/TimRestApi.php';
        $api = \createRestAPI();
        return $api->group_send_group_msg(false, $room_id, $msg);
    }

    // 读取申诉内容列表
    public function appeal_list()
    {
        $list = M('appeal_text')->select();
        $this->_return(1, '申诉文案列表获取成功', $list);
    }

}
