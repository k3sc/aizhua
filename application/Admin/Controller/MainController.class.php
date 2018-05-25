<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MainController extends AdminbaseController
{


    public  $pay_arr = array(1=>'微信支付', 2=>'支付宝支付', 3=>'PayPal支付', 4=>'苹果支付');

    public function index(){

        $date = I('date');
        if(!empty($date)){
            $sdate = strtotime($date);
            $edate = strtotime($date.' 23:59:59');
        }else{
            $sdate = strtotime(date('Y-m-d'));
            $edate = strtotime(date('Y-m-d 23:59:59'));
        }

        // 结果数组
        $result = array();

        // 充值总额
        $arrRecharge = M('pay_record')->where('paytime >= '.$sdate.' and paytime <= '.$edate.' and status=1')->field('ifnull(sum(money), 0) as money')->find();
        $result['recharge_money'] = $arrRecharge['money'];
        // 各个支付平台支付情况
        $arrRechargeInfo = M('pay_record')->where('paytime >= '.$sdate.' and paytime <= '.$edate.' and status=1')->field('type, ifnull(sum(money), 0) as money')->group('type')->select();
        $result['recharge_money_info'] = $arrRechargeInfo;

        // 今日消费总额
        $arrConsume = $arrConsume = M('users_coinrecord')->field('ifnull(sum(totalcoin), 0) as money')->where('addtime >= '.$sdate.' and addtime <= '.$edate .' and type="expend"')->find();
        $result['total_cost'] = $arrConsume['money'];

        // 消费增币
        $arrTotal = M('users_coinrecord')->field('ifnull(sum(givemoney), 0) as money')->where('addtime >= '.$sdate.' and addtime <= '.$edate)->find();
        $result['total_givemoney'] = $arrTotal['money'] * 10;

        // 夹娃娃次数
        $arrTotal = M('game_history')->where('ctime >= '.$sdate.' and ctime <= '.$edate)->count();
        $result['game_total'] = $arrTotal;

        // 设备在线数量
        $arrOnline = M('device')->field('count(*) as total')->where('beat_time > '.(time()-30))->find();
        $result['online_total'] = $arrOnline['total'];

        // 故障设备报告
        $arrFault = M('fault')->field('count(*) as total')->where('ctime >= '.$sdate.' and ctime <= '.$edate)->find();
        $result['fault_total'] = $arrFault['total'];

        // 待发货
//    $arrdWaybill = M('waybill')->field('count(*) as total')->where('ctime >= '.$sdate.' and ctime <= '.$edate.' and status=1')->find();
        $arrdWaybill = M('waybill')->field('count(distinct waybillno) as total')->where('status=1')->find();
        $result['waydbill_total'] = $arrdWaybill['total'];

        // 发货情况
        $arrWaybill = M('waybill')->field('count(*) as total')->where('fhtime >= '.$sdate.' and fhtime <= '.$edate.' and status=2')->find();
        $result['waybill_total'] = $arrWaybill['total'];


        $this->assign('result',$result);
        $this->assign('pay_arr',$this->pay_arr);
        $this->display();
    }

}
