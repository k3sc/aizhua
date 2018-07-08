<?php
// 微信支付JSAPI版本
// 基于版本 V3
// By App 2015-1-20
namespace Api\Controller;

use Think\Controller;

class IospayController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * 验证AppStore内付
	 * @param  string $receipt_data 付款后凭证
	 * @return array                验证是否成功
	 */
	private function gold_validate($receipt_data){
		/**
		 * 21000 App Store不能读取你提供的JSON对象
		 * 21002 receipt-data域的数据有问题
		 * 21003 receipt无法通过验证
		 * 21004 提供的shared secret不匹配你账号中的shared secret
		 * 21005 receipt服务器当前不可用
		 * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
		 * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
		 * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
		 */
		function acurl($receipt_data, $sandbox=0){
			//小票信息
			$POSTFIELDS = array("receipt-data" => $receipt_data);
			$POSTFIELDS = json_encode($POSTFIELDS);
	 
			//正式购买地址 沙盒购买地址
			$url_buy     = "https://buy.itunes.apple.com/verifyReceipt";
			$url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
			$url = $sandbox ? $url_sandbox : $url_buy;
	 
			//简单的curl
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $POSTFIELDS);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		}
		// 验证参数
		if (strlen($receipt_data)<20){
			$result=array(
				'status'=>false,
				'message'=>'非法参数'
				);
			return $result;
		}
		// 请求验证
		$html = acurl($receipt_data);
		$data = json_decode($html,true);
	 
		// 如果是沙盒数据 则验证沙盒模式
		if($data['status']=='21007'){
			// 请求验证
			$html = acurl($receipt_data, 1);
			$data = json_decode($html,true);
			$data['sandbox'] = '1';
		}
	 
		if (isset($_GET['debug'])) {
			exit(json_encode($data));
		}
		 
		// 判断是否购买成功
		if(intval($data['status'])===0){
			$result=array(
				'status'=>true,
				'message'=>'购买成功'
				);
		}else{
			$result=array(
				'status'=>false,
				'message'=>'购买失败 status:'.$data['status']
				);
		}
		return $result;
	}
		
	//充值娃娃币
	public function gold_notify(){
        //苹果内购的验证收据
        $receipt_data = I('post.apple_receipt'); 
        // 验证支付状态
        $result=$this->gold_validate($receipt_data);
		
        if ($result['status']) {
            //获取订单号
            $out_trade_no = I('get.oid');
                
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
                $update['free_coin'] = array('exp', 'free_coin+'.$olist['coingive']);
                 $update['total_payed'] = array('exp', 'total_payed+'.$olist['coin']);
                $update['coin_sys_give'] = array('exp', 'coin_sys_give+'.$olist['coingive']);
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
					$data['log'] = '首冲赠送娃娃币'.$first_coin.'个，'.$claw.'次甩爪';
				}elseif($give_coin != 0 && $claw != 0 && $rule_list['give'] != 0){
					$data['log'] = '首冲赠送娃娃币'.$first_coin.'个，'.$claw.'次甩爪，"'.$name.'"'. $num.'个';
				}elseif($give_coin != 0 && $rule_list['give'] != 0 ){//$give_coin != 0　&&　
					$data['log'] = '首冲赠送娃娃币'.$first_coin.'个，"'.$name.'"'. $num.'个';
				}elseif( $give_coin != 0){
					 $data['log'] = '首冲赠送娃娃币'.$first_coin.'个';
				}elseif($claw != 0 && $rule_list['give'] != 0){
					$data['log'] = '赠送娃娃币'. $number.'个，'.$claw.'次甩爪'.'，"'.$name.'"'. $num.'个';
				}elseif($rule_list['give'] != 0){
					$data['log'] = '赠送娃娃币'.$number.'，"'.$name.'"'. $num.'个';
				}elseif($claw != 0){
					$data['log'] = '赠送娃娃币'.$number.'，'.$claw.'次甩爪';
				}else{
					 $data['log'] ='赠送娃娃币'.$olist['coin'];
				} 

			   

				$data['paytime']= time();
				$data['status'] = 1;
				$data['type']   = 4;
				$re4 = $recharge->where('id = '.$olist['id'])->save($data);
			   // $res = M('test')->add(array('content'=>$data['log'],'id'=>$number));
			   
				//流水表
				$insert=array("type"=>'income',"action"=>'coin',"uid"=>$olist['user_id'],"touid"=>0,"giftid"=>intval($rule_list['give']),"giftcount"=>intval($num),"totalcoin"=>$olist['coin'],"givecoin"=>$olist['coingive'],"giveclaw"=>intval($claw),"realmoney"=>$olist['money'],"givemoney"=>$olist['coingive']/10,"showid"=>0,"addtime"=>time() );
				//$this->log_message($insert);
				M('users_coinrecord')->add($insert);		

			 }
			 $this->_return(array('code' => 1, 'msg' => '支付成功'));
        }
		$this->ajaxReturn(array('code' => -1001, 'msg' => '支付验证失败'));
	}
	

	
	protected function log_message($msg = ''){
		$file=THINK_PATH."../../data/runtime/Logs/log.txt";
		file_put_contents($file, date('Y-m-d H:i:s')."\r\n".var_export($msg, true)."\r\n\r\n",FILE_APPEND | LOCK_EX);
	}



}
