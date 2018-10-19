<?php
/**
 * User: mayuanli
 * Date: 2017/10/12
 * Time: 11:00
 */
namespace Api\Controller;
class RecordController extends BaseController
{
    //接口入口
    public function api()
    {
        $api_name = I('api_name');
        if ($api_name) {
            switch ($api_name) {
                case 'recharge':
                    $this->recharge();         //充值娃娃币
                    break;
                case 'gold':
                    $this->goldList();    //我的娃娃币
                    break;
                case 'recordList':
                    $this->recordList();    //娃娃币充值记录
                    break;
                case 'recordDetails':
                    $this->recordDetails(); //娃娃币充值记录详情
                    break;
                case 'bodyBill';
                    $this->bodyBillList();    //娃娃币帐单
                    break;
                case 'giftList':
                    $this->giftList();        //我的礼品列表
                    break;
                case 'convertNum':
                    $this->convertNum();    //我的礼品列表的兑换次数
                    break;
                case 'mailGift':
                    $this->mailGift();        //邮寄礼品
                    break;
                case 'giftConverList':
                    $this->giftConverList();    //礼品兑换列表
                    break;
                case 'bodyNum':
                    $this->bodyNum();            //兑换礼品的娃娃数量
                    break;
                case 'giftConvert':
                    $this->giftConvert();        //礼品兑换
                    break;
                case 'giftDetails':
                    $this->giftDetails();        //要兑换的礼品详情
                    break;
                case 'convertList':
                    $this->convertList();    //我的礼品兑换记录列表
                    break;
                case 'sameGfitList':
                    $this->sameGfitList();    //相同礼品的兑换记录列表
                    break;
                case 'get_paytype_list':
                    $this->get_paytype_list();  //获取充值方式列表
                    break;
                default:
                    $this->_return(404,'接口不存在');
                    break;
            }
        } else {
            $this->_return(404,'接口不能为空');
        }
    }

    //娃娃币充值
    private function recharge()
    {
        $coin_id = (int)I('coin_id');
        $type = (int)I('type');
        $room_id = I('room_id') ? I('room_id') : 0;
        if (empty($coin_id)) {
            $this->_return(-1,'充值金额类型的ID不能为空');
        }
        if (empty($type) || !$type) {
            $this->_return(-1, '支付类型不能为空');
        }
        $money = M('charge_rules')->where("id='{$coin_id}'")->find();
        $data['oid'] = 'WW' . date('YmdHis') . mt_rand(1000, 9999);   //充值订单号
        $data['money'] = $money['money'];                          //充值的金额
        $data['coin'] = $money['coin']+$money['give_coin'];        //充值获得娃娃币
        $data['coingive'] = $money['give_coin'];                    //充值赠送的娃娃币
        $data['free_coin'] = $money['give_coin'];
        $data['ctime'] = time();                                   //充值订单生成时间
        $data['user_id'] = $this->user_id;                          //用户ID
        $data['pay_id'] = $coin_id;                                //充值娃娃币的类型
        $data['room_id'] = $room_id;                                //充值的房间ID
        //首冲送的娃娃币
//        $recharge_record = M('pay_record')->where('user_id=' . $this->user_id . ' and coingive !=0 and status!=0')->select();
//        $count = count($recharge_record);
//        $data['count'] = $count;
//        if ($count == 0 && $money['firstgive'] != 0 ) {//&& $money['firstgive'] !=0
//            if( $room_id > 0 ){
//                $data['coin'] = $data['coin'] + $money['firstgive']+200+20;
//                $data['coingive'] += $money['firstgive']+200+20;
//            }else{
//                $data['coin'] = $data['coin'] + $money['firstgive']+20;
//                $data['coingive'] += $money['firstgive']+20;
//            }
//        }

        /* 判断本次充值是否为当前充值金额的首冲 */
//        $count = M('pay_record')->where(['user_id'=>$this->user_id,'status'=>1,'money'=>$money['money']])->count();
//        if( !$count ){ // 是首冲
//            $data['coin'] = $data['coin'] + $money['firstgive'];
//            $data['coingive'] += $money['firstgive'];
            /* 如果当前时间段满足活动，赠送首冲金额 */
            $conf = M('active_config')->find(1);
            if( time() >= $conf['sdate'] && time() <= $conf['edate'] ){ //当前时间在活动时间段内
                /* 判断该时间段内，用户是否充值过，如果没有则赠送首冲金币 */
                $count = M('pay_record')->where(['user_id'=>$this->user_id,'status'=>1,'money'=>['egt',$conf['first_pay']],'ctime'=>['between',$conf['sdate'].','.$conf['edate']]])->count();
                if( !$count && $money['money'] >= $conf['first_pay'] ){
                    $data['coin'] += $conf['first_pay_coin'];
                    $data['free_coin'] += $conf['first_pay_coin'];
                    $data['coingive'] += $conf['first_pay_coin'];
                }
            }
//        }
        if ($type == 1) {       //微信支付
            $data['type'] = 1;
            $result = M('pay_record')->add($data);
            if ($result) {
                R('Api/Wxpay/wx_gold_recharge', array($data['oid'], $data['money']));
            } else {
                $this->_return(-1, '微信充值失败');
            }
        } elseif ($type == 2) { //支付宝支付
            $data['type'] = 2;
            $result = M('pay_record')->add($data);
            if ($result) {
                R('Api/Alipay/gold_recharge_alipay', array($data['oid'], $data['money']));
            } else {
                $this->_return(-1, '支付宝充值失败');
            }

        } elseif ($type == 3) { //payPal支付
            $data['type'] = 3;
            $result = M('pay_record')->add($data);
            $data['money'] = $data['money'] / M('config')->where(['id'=>1])->getField('rate');
            $this->_return(1, '获取支付参数成功', array('oid' => $data['oid'], 'money' => $data['money'], 'notifyurl' => U('Api/Paypal/gold_notify', array('oid' => $data['oid'], 'success' => 'true'), true, true)));
			//R('Api/Paypal/payPayPal', array($data['oid'], $data['money']/*, I('address')*/));
        } else {                //ApplePay支付
            $data['type'] = 4;
            $result = M('pay_record')->add($data);
            $this->_return(1, '获取支付参数成功', array('oid' => $data['oid'], 'money' => $data['money'], 'notifyurl' => U('Api/Iospay/gold_notify', array('oid' => $data['oid']), true, true)));
        }

    }

    //娃娃币充值列表
    private function goldList()
    {
        //充值娃娃币列表
        $recharge = M('charge_rules')->field('id as charge_id,coin as bodyCoin,money,claw,give_coin')->where("give=0")->select();
        $info['user_id'] = $this->user_id;
        $info['coin'] = $this->user['coin'];

//        $result1 = M('charge_rules')->where("firstgive !=0")->order("id asc")->find();
        $result1 = M('active_config')->find(1);
        $result2 = M('charge_rules')->field('id as charge_id,coin,give,money,number,claw,give_coin')->where("give !=0")->select();
        //首冲赠送娃娃币活动信息
        if ($result1) {
            if( time() >= $result1['sdate'] && time() <= $result1['edate'] ){
                $arr['money'] = $result1['first_pay'];
                $arr['firstgive'] = $result1['first_pay_coin'];
                $info['firstgive'] = $arr;
            }
        }

        //充值娃娃币赠送礼品
        if ($result2) {
            $data = [];
            foreach ($result2 as $key => $value) {
                $data[$key]['charge_id'] = $value['charge_id'];
                $data[$key]['bodyCoin'] = $value['coin'];
                $data[$key]['money'] = $value['money'];
                $data[$key]['number'] = $value['number'];
                $data[$key]['claw'] = $value['claw'];
                $data[$key]['give_coin'] = $value['give_coin'];
                if ($value['give'] == 0) {
                    $data[$key]['img'] = '';
                    $data[$key]['name'] = '';
                } else {
                    $gift = M('give_gift')->where("id={$value['give']}")->find();
                    $data[$key]['img'] = $gift['img'] ?:'';
                    $data[$key]['name'] = $gift['name'] ?:'';
                }
            }
            $info['presenter'] = $data;
        }
        $info['charge'] = $recharge;
        $this->_return(1, '充值列表', $info);
    }

    //娃娃币充值记录
    private function recordList()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $record = M('pay_record')->where("user_id={$this->user_id}")->order('ctime desc')->page($page, $size)->select();
        if ($record) {
            $this->_return(1, '充值记录', $record);
        }
        $this->_return(1, '还没有充值记录', []);
    }

    //娃娃币充值记录详情
    private function recordDetails()
    {
        $oid = I('o_id');
        if (empty($oid)) {
            $this->_return(-1,'记录ID不能为空');
        }
        $record = M('pay_record')->where("user_id={$this->user_id} and oid='{$oid}' ")->find();
        if ($record) {
            $this->_return(1, '充值记录详情', $record);
        }
        $this->_return(-1, '充值记录详情错误', (object)array());
    }

    //娃娃币账单
    private function bodyBillList()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $bill = M('users_coinrecord')->where("uid={$this->user_id}")->order('addtime desc')->page($page, $size)->select();
        $data['coin'] = $this->user['coin'];
        if ($bill) {
			$action=array(
                "loginbonus"     =>"登录奖励",
                "zhuawawa"       =>"抓娃娃游戏",
                "service"        =>"人工服务",
                "coin"           =>"充值记录",
                "claw"           =>"游戏甩爪",
                "active_shangbi" =>"上币活动",
                "active_dingshi" =>"定时活动",
                "regcoin"        =>"注册送币",
                "invite"         =>"邀请奖励",
                "set_coin"       =>"娃娃换币",
                "retreat"       =>"保夹退币"
            );
			foreach($bill as &$v){
				$v['actiontitle'] = trim($action[$v['action']]);
                if($v['action']== 'retreat'){
                    $v['actiontitle'] = "抓{$v['wawa_name']} 保夹退币";
                }
			}
			unset($v);
            $data['list'] = $bill;
            $this->_return(1, '娃娃币帐单', $data);
        }
        $data['list'] = [];
        $this->_return(1, '娃娃币帐单', $data);
    }

    //我的礼品列表
    private function giftList()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $type = I('type') ? (int)I('type') : '';
        if (!empty($type)){
             $map['a.type'] = ['eq', $type];
        }
        // if (!empty($type) && $type != 4) {
        //     $map['a.type'] = ['eq', $type];
        // } else {
        //     $map['a.type'] = ['neq', 4];
        // }
        $map['a.user_id'] = ['eq', $this->user_id];
        $list = M('users_gift')
            ->alias('a')
            ->join("cmf_give_gift as b on b.id=a.gift_id", left)
            ->field('a.id,a.user_id,a.gift_id,a.ctime,a.type,b.name,b.img')
            ->where($map)
            ->order("a.ctime desc")
            ->page($page, $size)
            ->select();
        if ($list) {
            foreach ($list as $key => $value) {
                $arr = explode(',', $value['img']);
                $list[$key]['img'] = $this->get_upload_path($arr[0]);
            }
            $this->_return(1, '我的礼品列表', $list);
        }
        $this->_return(1, '还没有我的礼品列表', []);
    }

    //我的礼品列表的兑换次数
    private function convertNum()
    {
        $list = M('users_convert')->where("user_id={$this->user_id}")->select();
        $data['id'] = $this->user_id;
        $data['name'] = $this->user['user_nicename'];
        $data['sex'] = $this->user['sex'];
        $string = preg_match("/^(http:\/\/|https:\/\/)/", $this->user['avatar']);
        if ($string == 1) {
            $data['avatar'] = $this->user['avatar'];
        } else {
            $data['avatar'] = 'http://' . $_SERVER['HTTP_HOST'] . $this->user['avatar'];//'http://aizhua.net'
        }
        $data['number'] = !empty($list) ? count($list) : 0;
        $data['body_num'] = 0;
        foreach ($list as $key => $value) {
            $bodynum = explode(',', $value['number']);
            foreach ($bodynum as $k => $v) {
                $data['body_num'] += $v;
            }
        }
        $this->_return(1, '用户礼品兑换次数', $data);
    }

    //我的礼品兑换记录列表
    private function convertList()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $list = M('users_convert')->alias('a')
            ->join('cmf_give_gift as b on b.id=a.gift_id')
            ->field('a.id,a.user_id,a.gift_id,b.name,b.img,a.ctime,a.body_id,a.number')
            ->where("user_id={$this->user_id}")->order('a.ctime desc')
            ->page($page, $size)->select();
        if ($list) {
            $data = [];
            foreach ($list as $key => $value) {
                $arr = explode(',', $value['img']);
                $bodyid = explode(',', $value['body_id']);
                $bodynum = explode(',', $value['number']);
                $data[$key]['id'] = $value['id'];
                $data[$key]['user_id'] = $value['user_id'];
                $data[$key]['gift_id'] = $value['gift_id'];
                $data[$key]['name'] = $value['name'];
                $data[$key]['img'] = $arr[0];
                $data[$key]['number'] = 1;
                $data[$key]['ctime'] = $value['ctime'];
                foreach ($bodyid as $k => $v) {
                    //获取娃娃的名称和ID
                    $body = M('gift')->where("id={$v}")->find();
                    $data[$key]['body'][$k]['body_id'] = $body['id'];
                    $data[$key]['body'][$k]['body_name'] = $body['giftname'];
                    $data[$key]['body'][$k]['body_num'] = $bodynum[$k];
                }
            }
            $this->_return(1, '礼品兑换记录', $data);
        }
        $this->_return(1, '还没有礼品兑换记录', []);
    }

    //邮寄礼品
    private function mailGift()
    {
        $gift_id = I('gift_id');
        $address_id = I('address_id');
        $remark = I('remark');
        $model = M('waybill');
        if (empty($gift_id)) {
            $this->_return(-1,'邮寄礼品的ID不能为空');
        }
        if (empty($address_id)) {
            $this->_return(-1,'邮寄礼品的地址ID不能为空');
        }
        $data['waybillno'] = $this->get_orderno();//运单号
        $data['ctime'] = time();                       //运单创建
        $arr = explode(',', $gift_id);
        $re1 = true;
        $re2 = '';
        $address = M('user_addr')->where('addr_id=' . $address_id . '')->find();

        $model->startTrans();//开启事务
        //更改用户礼品表的状态
        foreach ($arr as $key => $value) {
             $r = M('users_gift')->where("id='{$value}' and type = 1")->save(array('type' => 2));
             if( !$r )$re1 = false;
            //$re1 = M('users_gift')->where("gift_id='{$value}' and user_id=".$this->user_id)->save(array('type' => 2));
            //var_dump($re1);exit;
        }
        foreach ($arr as $key => $value) {
            $data['status'] = 1;                         //运单状态
            $data['user_id'] = $this->user_id;         //用户id
            $data['addr_id'] = $address_id;             //收货地址id
            $data['user_gift_id'] = $value;                     //我的礼品表id
            $data['gift_nums'] = 1;                         //邮寄礼品数量
            $data['remark'] = $remark;                //备注
            $data['uname'] = $address['username']; //联系人名称
            $data['phone'] = $address['mobile'];   //联系人电话
            $data['addr'] = $address['addr'] . $address['addr_info'];//联系人地址详细
            $re2 = $model->add($data);
        }

        if ($re1 != false && $re2 != false) {
            $model->commit();//提交事务
            $this->_return(1, '礼品邮寄成功', $re2);
        }
        $model->rollback();//事务回滚
        $this->_return(-1,'礼品邮寄失败');
    }

    //要兑换礼品的列表
    private function giftConverList()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $list = M("give_gift")->order('ctime desc')->where('is_del=0 and is_show=1')->page($page, $size)->select();
        $data = [];
        if ($list) {
            foreach ($list as $key => $value) {
                $arr = explode(',', $value['img']);
                $data[$key]['id'] = $value['id'];
                $data[$key]['name'] = $value['name'];
                $data[$key]['img'] = $arr[0];
                $data[$key]['quantity'] = $value['quantity'];
                $data[$key]['convert_num'] = $value['convert_num'];
                $data[$key]['ctime'] = $value['ctime'];
            }
            $this->_return(1, '要兑换礼品的列表', $data);
        }
        $this->_return(1, '还没有要兑换礼品的列表');
    }

    //兑换礼品娃娃数量
    private function bodyNum()
    {
        $g_id = (int)I('gift_id');
        $u_id = $this->user_id;
        if (empty($g_id)) {
            $this->_return(-1,'兑换礼品的ID不能为空');
        }
        //查看有没有这个礼品
        $gift = M("give_gift")->where("id={$g_id}")->find();
        if (!$gift) {
            $this->_return(-1,'还没有这个礼品');
        }
        if (!$gift['body_id']) $this->_return(-1, '该礼品不可兑换');
        /* 可兑换当前礼品的娃娃 */
        $body_id             = explode(',', $gift['body_id']);
        $arr                 = explode(',', $gift['img']);
        $data['user_id']     = $this->user_id;
        $data['gfit_id']     = $gift['id'];
        $data['gfit_name']   = $gift['name'];
        $data['gfit_img']    = $arr[0];
        $data['convert_num'] = $gift['convert_num'];

        if (!empty($body_id)) {
            $num = count($body_id);
            $body = [];
            for ($i = 0; $i < $num; $i++) {
		if(empty($body_id[$i]))
                {
                    continue;
                }
                $count = M('user_wawas as a')
                    ->join('cmf_gift as b on a.wawa_id = b.id', 'left')
                    ->where("a.user_id={$u_id} and a.wawa_id={$body_id[$i]} and a.status=0 and a.is_del=0 and find_in_set($g_id,b.`convert_lipin`) and find_in_set('3',b.`convert`)")
                    ->select();
                $count = count($count);
                //计算别人赠我的娃娃的数量
                if ($count !== 0) {
                    $arr                   = M('gift')->where("id={$body_id[$i]} and find_in_set($g_id,`convert_lipin`) and find_in_set('3',`convert`)")->find();
                    $body[$i]['body_id']   = $body_id[$i];
                    $body[$i]['body_name'] = $arr['giftname'];
                    $body[$i]['count'] = $count;
                    $body[$i]['body_img'] = $arr['gifticon'];
                }
            }
            $data['body'] = array_values($body);
            $this->_return(1, '可兑换的娃娃数量', $data);
        }
        $this->_return(1, '还没有可兑换的娃娃数量', $data);
    }

    //礼品兑换
    private function giftConvert()
    {
        $g_id = (int)I('gift_id');    //要兑换礼品的ID
        $u_id = $this->user_id;        //用户的ID
        $wawaList = I('w_list');
        if (empty($g_id)) {
            $this->_return(-1,'兑换礼品的ID不能为空');
        }
        if (empty($wawaList)) {
            $this->_return(-1,'兑换礼品娃娃的ID和数量不能为空');
        }
        //查看有没有这个礼品
        $gift = M("give_gift")->where("id={$g_id}")->find();
        if (!$gift) {
            $this->_return(-1,'还没有这个礼品');
        }
        if($gift['quantity'] <=0){
             $this->_return(-1,'礼品库存不足，不能兑换了');
        }
        $count = 0;   //兑换礼品的娃娃总数量
        $getArr = array();
        $model = M('user_wawas');
        $status = [];  //状态
        $b_id = '';  //娃娃的ID;
        $num = '';  //娃娃对应ID的数量
        //分割娃娃ID和数量
        $arr = explode(',', $wawaList);
        foreach ($arr as $k => $v) {
            $getArr[$k] = explode(':', $v);
            $count += $getArr[$k][1];
            $b_id .= $getArr[$k][0] . ',';
            $num .= $getArr[$k][1] . ',';
        }
        //判断娃娃的数量是满足兑换条件
        if ($count < $gift['convert_num']) {
            $this->_return(-1,'您的公仔数量不足以兑换该礼品');
        }
        $model->startTrans();//开启事务
        //修改兑换礼品的出货量
        //$result3 = M('give_gift')->where('id=' . $g_id . '')->setInc('shipment_num');
        /* 修改礼品库存 */
        M('give_gift')->where('id=' . $g_id . '')->setDec('quantity');
        //新增一条兑换礼品的记录
        $data['user_id'] = $this->user_id;      //用户ID
        $data['gift_id'] = $g_id;                  //兑换礼品的ID
        $data['body_id'] = substr($b_id, 0, -1);     //兑换礼品的娃娃ID
        $data['number'] = substr($num, 0, -1);     //兑换礼品的娃娃数量
        $data['ctime'] = time();                  //兑换礼品的时间
        $result2 = M("users_convert")->add($data);

        //新增一条我的礼品
        $gift_data['user_id'] = $this->user_id;  //用户ID
        $gift_data['gift_id'] = $g_id;           //兑换礼品的ID
        $gift_data['type'] = 1;               //状态
        $gift_data['ctime'] = time();          //获得时间
        $gift_data['covert_id'] = $result2;
        $result4 = M('users_gift')->add($gift_data);

        //修改娃娃兑换状态
        foreach ($getArr as $key => $value) {
            $update['is_del'] = 2;
            $result1 = $model->where("user_id = $this->user_id and wawa_id='{$value[0]}' and status=0 and is_del=0")->limit($value[1])->save($update);
        }
        if (!($result2) || !($result4)) {//(in_array('false',$status))
            $model->rollback();//事务回滚
            $this->_return(-1,'娃娃兑换失败');
        } else {
            $model->commit();//事物提交
            $this->_return(1,'娃娃兑换成功');
        }
    }

    //要兑换的礼品详情
    private function giftDetails()
    {
        $g_id = (int)I('gift_id');
        if (empty($g_id)) {
            $this->_return(-1,'兑换礼品的ID不能为空');
        }
        $list = M("give_gift")->where("id='{$g_id}'")->find();
        if (!$list) $this->_return(-1, '礼品不存在');
        $arr  = explode(',', $list['body_id']);
        $arr  = array_filter($arr);
        $name = '';
        foreach ($arr as $key => $value) {
            if( !$value )continue;
            $body = M('gift')->where("id={$value}")->getField('giftname');
            if ($body) {
                $name .= $body . '、';
            }
            //$name[$key]['name'] = $body;
        }
        //$this->_return($name);
        $data['id'] = $list['id'];
        $data['name'] = $list['name'];
        $data['gift_img'] = array_map(array($this, 'get_upload_path'), explode(',', $list['img']));
        $data['convert_num'] = $list['convert_num'];
        $list['content'] = '<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no"><style type="text/css">img{max-width:100%;}</style>'.$list['content'];
        $data['content'] = htmlspecialchars_decode($list['content']);
        $data['body_name'] = mb_substr($name, 0, -1, 'utf-8');

        if ($list) {
            $this->_return(1, '要兑换的礼品详情', $data);
        }
        $this->_return(-1, '没有这个要兑换的礼品详情');
    }

    //同一种礼品兑换的记录
    private function sameGfitList()
    {
        $page = I('page') ? I('page') : 1;
        $size = I('size') ? I('size') : 10;
        $g_id = (int)I('gift_id');
        $data = [];
        if (empty($g_id)) {
            $this->_return(-1,'礼品兑换的ID不能为空');
        }
        $list = M('users_convert')->alias('a')
            ->join("cmf_users as b on b.id=a.user_id", left)
            ->field("a.user_id,b.user_nicename,b.avatar,a.ctime")
            ->where("a.gift_id={$g_id}")->order('a.ctime desc')
            ->page($page, $size)->select();
        foreach ($list as $key => $value) {
            $data[$key]['user_id'] = $value['user_id'];
            $data[$key]['user_nicename'] = $value['user_nicename'];
            $string = preg_match("/^(http:\/\/|https:\/\/)/", $value['avatar']);
            if ($string == 1) {
                $data[$key]['avatar'] = $value['avatar'];
            } else {
                $data[$key]['avatar'] = 'http://' . $_SERVER['HTTP_HOST'] . $value['avatar'];//'http://aizhua.net'
            }
            $data[$key]['ctime'] = $value['ctime'];
        }
        if ($list) {
            $this->_return(1, '同一种礼品兑换的记录', $data);
        }
        $this->_return(1, '还没有同一种礼品兑换的记录', $data);
    }


    /**
     * 获取充值方式列表
     */
    public function get_paytype_list(){
        $paytypeArr = [1=>'微信',2=>'支付宝',3=>'PayPal',4=>'Apple Pay'];
        $row = M('config')->field('paytype')->find(1);
        $paytype = explode(',',$row['paytype']);
        $result = [];
        foreach ($paytype as $k => $v) {
            if( $v ){
                $result[$k]['id'] = $v;
                $result[$k]['paytype'] = $paytypeArr[$v];
            }
        }

        $this->_return(1,'获取成功',$result);
    }

}

