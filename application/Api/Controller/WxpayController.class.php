<?php
// 微信支付JSAPI版本
// 基于版本 V3
// By App 2015-1-20
namespace Api\Controller;

use Think\Controller;

class WxpayController extends Controller
{
    //App全局相关
    public static $_url; //动态刷新
    public static $_opt; //参数缓存
    public static $_logs = ''; //log地址
    //JOELCMS设置缓存
    protected static $SET;
    protected static $SHOP;

    //微信缓存
    protected static $_wx;
    protected static $_wxappid;
    protected static $_wxappsecret;

    protected $wx_openid;
    protected $user;


    public function __construct()
    {

        //App自定义全局
        parent::__construct();
        header("Content-type: text/html; charset=utf-8");
        //刷新全局地址
        self::$_url = "http://" . $_SERVER['HTTP_HOST'];
        //获取全局配置
        self::$SET = M('Set')->find();

        if (!self::$SET) {
            die('系统未配置！');
        }
        //全局缓存微信
        self::$_wxappid = self::$SET['wxappid'];
        self::$_wxappsecret = self::$SET['wxappsecret'];
        $options['appid'] = self::$_wxappid;
        $options['appsecret'] = self::$_wxappsecret;
        self::$_wx = new \Util\Wx\Wechat($options);

        $this->wx_openid  = $_SESSION['sqopenid'];
        //$this->wx_openid  = "09b165031e808bf45b037e9b27ff73bc";
        $this->user = M('users')->where("openid = '".$this->wx_openid ."'")->find();
    }

	//娃娃币充值
	public function wx_gold_recharge($oid, $payprice=0){
//		$payprice = 0.01;
        self::$_opt['oid'] = $oid;
		self::$_opt['openid'] = $openid = $_SESSION['WAP']['vip']['wx_openid'];//$this->wx_openid;
        if (!$oid) {
            !is_weixin() ? $this->ajaxReturn(array('status' => 0, 'msg' => '订单参数不完整！请重新尝试！')) : $this->diemsg(0, '订单参数不完整！请重新尝试！');
        }
        $cache = M('pay_record')->where(array('oid' => $oid))->find();
        if (!$cache) {
            $data['code'] = -1;
            $data['msg'] = '此订单不存在！'.$oid;
            $this->ajaxReturn($data);
        }
        if ($cache['status'] == 1) {
            $data['code']= -1;
            $data['msg']='此订单已支付！请勿重复支付！';
            $this->ajaxReturn($data);
        }
		
        $options['appid'] = self::$_wxappid;
        $options['appsecret'] = self::$_wxappsecret;
        $options['mchid'] = self::$SET['wxmchid'];
        $options['mchkey'] = self::$SET['wxmchkey'];
        $openid = $options['appid'];

        $paysdk = new \Util\Wx\Wxpaysdk($options);
        
        $paysdk->setParameter("body", "微信充值金币"); //商品描述
        //自定义订单号，此处仅作举例
        $timeStamp = time();
        $paysdk->setParameter("out_trade_no", $oid); //商户订单号
        $paysdk->setParameter("total_fee", intval($payprice * 100)); //总金额单位为分，不允许有小数
        $paysdk->setParameter("notify_url", 'http://' . $_SERVER['HTTP_HOST']  . '/Api/Wxpay/gold_notify'); //交易通知地址
        $paysdk->setParameter("trade_type", "APP"); //交易类型
        $prepayid = $paysdk->getPrepayId();
        $package = $paysdk->getAppPackAge($prepayid);
        if ($prepayid) {//
                $this->ajaxReturn(array('code' => 1, 'msg' => '获取支付参数成功','data' => $package));          //
        } else {
            $this->ajaxReturn(array('code' => -1, 'msg' => '未成功生成支付订单，请重新尝试！','data'=>$prepayid));
        }
	}

		
	//充值娃娃币
	public function gold_notify(){
		 $str = "";
        // foreach ($_POST as $k => $v) {
        //     $str = $str . $k . "=>" . $v . '  ';
        // }
        //echo 'AAA';
        //file_put_contents(self::$_logs . './Data/app_wxpaynd.txt', '响应参数:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
        
        import('Util.Wx.Wxpaysdk');
        //使用通用通知接口
        $notify = new \Util\Wx\Wxpayndsdk();
        

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        //$xml = file_get_contents('php://input');
       
       // echo '<pre>';
       // print_r($GLOBALS);
        $notify->saveData($xml);

      // 
      // 
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码
        }
        $returnXml = $notify->returnXml();

        //$this->ajaxReturn($notify);

        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======

        if ($notify->checkSign() == TRUE) {
            //获取订单号
            $out_trade_no = $notify->data["out_trade_no"];
             // $out_trade_no = 'WW201710261011409213';
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                //$log_->log_result($log_name,"【通信出错】:\n".$xml."\n");
                file_put_contents(self::$_logs . './Data/app_wxpayerr.txt', '通讯出错:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . '订单号:' . $out_trade_no . PHP_EOL . '交易结果:通讯出错' . PHP_EOL . PHP_EOL, FILE_APPEND);
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                //$log_->log_result($log_name,"【业务出错】:\n".$xml."\n");
                file_put_contents(self::$_logs . './Data/app_wxpayerr.txt', '业务出错:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . '订单号:' . $out_trade_no . PHP_EOL . '交易结果:业务出错' . PHP_EOL . PHP_EOL, FILE_APPEND);
            } else {
                //此处应该更新一下订单状态，商户自行增删操作
                
                 $recharge = M('pay_record');//充值记录
                 $olist = $recharge->where("oid = '".$out_trade_no."'")->find();
                 $rule_list = M('charge_rules')->where('id='.$olist['pay_id'].'')->find();//充值规则
                 // $this->ajaxReturn($rule_list);
                 if($olist['status'] == 0){
                    //赠送礼品
                    $num = 0;
                    $name = '';
                    if($rule_list['give'] !== 0){
                        $name = M('give_gift')->where('id='.$rule_list['give'].'')->getField('name');
                        $num = $rule_list['number'];
                        for($i = 1; $i<=$num; $i++){
                            $gift['gift_id'] = $rule_list['give'];
                            $gift['type']    = 1;
                            $gift['user_id'] = $olist['user_id'];
                            $gift['ctime']   = time();
                            $re1 = M('users_gift')->add($gift);
                        }
                    }

                    $update = array();
                    $update['claw'] = array('exp', 'claw+'.$rule_list['claw']);
                    $update['coin'] = array('exp', 'coin+'.$olist['coin']);
                    $update['coin_sys_give'] = array('exp', 'coin_sys_give+'.$olist['coingive']);
                    $update['free_coin']= array('exp', 'free_coin+'.$olist['coingive']);
                    $update['total_payed'] = array('exp', 'total_payed+'.$olist['money']);
                    $re2 = M('users')->where("id={$olist['user_id']}")->save($update);


                     /* 读取用户贩卖模式规则， 送强抓力 */
                     $config = M('sellmodel')->order('money desc')->select();
                     foreach ($config as $v) {
                         if( $olist['money'] >= $v['money'] ){
                             M('users')->where("id={$olist['user_id']}")->setInc('strong',$v['zj_count']);
                             break;
                         }
                     }
                    
                    //修改用户的充值订单
                    $give_coin = $olist['coingive'];
                    $claw       = $rule_list['claw'];
                    $number = $olist['coin']-$give_coin;
                    $first_coin = $olist['coin'];
                    if($give_coin != 0 && $claw != 0){
                        $data['log'] = '赠送娃娃币X'.$first_coin.'个，'.$claw.'次甩爪';
                    }elseif($give_coin != 0 && $claw != 0 && $rule_list['give'] != 0){
                        $data['log'] = '赠送娃娃币X'.$first_coin.'个，'.$claw.'次甩爪，"'.$name.'"'. $num.'个';
                    }elseif($give_coin != 0 && $rule_list['give'] != 0 ){//$give_coin != 0　&&　
                        $data['log'] = '赠送娃娃币X'.$first_coin.'个，"'.$name.'"'. $num.'个';
                    }elseif( $give_coin != 0){
                         $data['log'] = '赠送娃娃币X'.$first_coin.'个';
                    }elseif($claw != 0 && $rule_list['give'] != 0){
                        $data['log'] = '赠送娃娃币X'. $number.'个，'.$claw.'次甩爪'.'，"'.$name.'"'. $num.'个';
                    }elseif($rule_list['give'] != 0){
                        $data['log'] = '赠送娃娃币X'.$number.'，"'.$name.'"'. $num.'个';
                    }elseif($claw != 0){
                        $data['log'] = '赠送娃娃币X'.$number.'，'.$claw.'次甩爪';
                    }else{
                         $data['log'] ='赠送娃娃币X'.$olist['coin'];
                    } 

                   

                    $data['paytime']= time();
                    $data['status'] = 1;
                    $data['type']   = 1;
                    $re4 = $recharge->where('id = '.$olist['id'])->save($data);
                   // $res = M('test')->add(array('content'=>$data['log'],'id'=>$number));
				   
					//流水表
					$insert=array("type"=>'income',"action"=>'coin',"uid"=>$olist['user_id'],"touid"=>0,"giftid"=>intval($rule_list['give']),"giftcount"=>intval($num),"totalcoin"=>$olist['coin'],"givecoin"=>$olist['coingive'],"giveclaw"=>intval($claw),"realmoney"=>$olist['money'],"givemoney"=>$olist['coingive']/10,"showid"=>0,"addtime"=>time() );
					//$this->log_message($insert);
					M('users_coinrecord')->add($insert);		

                }
            }
			echo 'success';
        }
	}
	

	
	protected function log_message($msg = ''){
		$file=THINK_PATH."../../data/runtime/Logs/log.txt";
		file_put_contents($file, date('Y-m-d H:i:s')."\r\n".var_export($msg, true)."\r\n\r\n",FILE_APPEND | LOCK_EX);
	}



}
