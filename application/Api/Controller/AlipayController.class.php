<?php
// 支付宝支付
// 基于版本 V3
// By App 2015-1-20
namespace Api\Controller;
use Think\Controller;

class AlipayController extends Controller
{
    //App全局相关
    public static $_url; //动态刷新
    public static $_opt; //参数缓存
    public static $_logs = ''; //log地址
    //JOELCMS设置缓存
    protected static $SET;
    protected static $SHOP;
	
	//支付宝配置
	protected  $gatewayUrl = "https://openapi.alipay.com/gateway.do";
	protected  $appId = "2017101509313781";
	protected  $rsaPrivateKey = "MIIEowIBAAKCAQEAwsONA5PkCRvUpLTS036I+y0hxjEIZfI5sMpxc+CcktGz0xCVjqe9TEBnhyuEYZUVaAm8CS635lHBG0/f9r5avk6dn4UDz4tZlFydM5eyzFWEZJ+bVLinSznov0ZS2YelQ5fH0E8gHIzxQ387hU1W9hcXnvoh/SIiPWHDOaLVSGTvnxfAIIiOqgU+hkMIAox9vUr6b55KLRd9xcZ+HSHB/A94ckqE9EvtTirENzTUuv17/uodTdoHcFdeXo6x8+xq62iSt7kIlO1NxEOjntzy4mxnRbK/Vf2v+xuxPBsXtfoyQ+H9a8EbkHFAe1VqCCxvFz22+byKoArUjvWIw5SbFwIDAQABAoIBAAk06UOUCCGOGT039wdcYelNtt0BkF4RVzzONRK/OWePVirSC9/UehRSrxIqsnVScNKcMzFT7gmLL8+0tOebE/sPCFB0Hzv+YFutDRlVQHR8TmfbS6JzoCTkNeZk0qHJ0bwiPqXQN7phxz3jk9K3VzcG3Gz9cgHfTTMNngbpdZ+YYMSAB1kuocMa9jyI+2sGr4VVAuv3pVd8wHUxL+KCB6yUwcoyF/Vp5Ly5v7C2gGMEILeLJsMiq3Td5C/ilp38HJ6cC6Jz9eAYTmymV59dx1L5Bq/sjvjtECErY3w8ZDMcAvvFy/9NX7wsycTWfArPs9MLM6ct797XGP/0nz57zvECgYEA5j4wR788FqqdeFlL5JWU/8t8+3h1XqO/rF5cJPuKjaTjrnR8VrXzQ0Sox3tubApLfoCCIfAFoIgp+UxpoEpqBomNOdq15wZZNEoCEve+kpVtt9FzQlYbXTCQp2JA9NKrCL5agts0EJG9KcNXzUWirzysiFFjez7/h9P/fvaKQI0CgYEA2I1Lopauj1J5rW1RtYWVRxL6wzkOCdomvOpWQwKxTT7XTnUjcjWZb8/KAnrPE8hJO38UACK18ZrixZsWe+HOMzzyduahmIuWs22h5MxaegU9WZn9zYcR/6Rn8pRI1G1KXJtyA1DPOsttW9TkQO3cABTSZ88N8RIhn8XmZ2qeezMCgYAP3ndraoXUthu2YMk3Twv++WJ63pmQVU7vrW1Ca0fobVX3/zVWsKG8oC3V2e8JaUg8xtSxTB7HKrth4F9jWd2m57IhK67f5nMdhIBSGFs9NSljwv7jspWePauqrb32YdLB/oS0gjE7dyAHkdwqXMNSuqbVmm513yizjuwMLyfmPQKBgQCGql91rYTCRgS677eXTYoGV+wvSSTMxae/ZW7dXyhJJCIafUfcty5C1RIHtr1dzPiVkjIq32sL40jQn+A8i7CCNo2FmWi2h1/hFvVNZjOBnA6J0PR/QlbUBjZKKSKdT5Wlv1kIbNFh+613JQ4IvqJIPqqVubIuzxqv1A6zEx+Y9wKBgH88V+JuKnDP3RKgLGyqs+BH2qAd5++mMP9X6hU/7SkkrQuOg8mvsHb1JB7h9PWZUW3w/0LYtLTkegR5s1NTEPo4f5xvp9oeukfKqmAPuDHJDtzhW98IdF5SrpmQvMI1MuqEOqacE/Mqs6YQOW7hfNvO9fE/tOVjKOkn+FQdLryh";
	protected  $format = "json";
	protected  $charset = "UTF-8";
	protected  $signType = "RSA";
	protected  $alipayrsaPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwsONA5PkCRvUpLTS036I+y0hxjEIZfI5sMpxc+CcktGz0xCVjqe9TEBnhyuEYZUVaAm8CS635lHBG0/f9r5avk6dn4UDz4tZlFydM5eyzFWEZJ+bVLinSznov0ZS2YelQ5fH0E8gHIzxQ387hU1W9hcXnvoh/SIiPWHDOaLVSGTvnxfAIIiOqgU+hkMIAox9vUr6b55KLRd9xcZ+HSHB/A94ckqE9EvtTirENzTUuv17/uodTdoHcFdeXo6x8+xq62iSt7kIlO1NxEOjntzy4mxnRbK/Vf2v+xuxPBsXtfoyQ+H9a8EbkHFAe1VqCCxvFz22+byKoArUjvWIw5SbFwIDAQAB";
	
	
    public function __construct()
    {

        //App自定义全局
        parent::__construct();
        header("Content-type: text/html; charset=utf-8");
        Vendor('Alipay.AopSdk');
    }
	
    //娃娃币充值支付宝支付接口
    public function gold_recharge_alipay($oid, $payprice=0)
    {   
        $money = $payprice;
        $o_id   = $oid;
        $aop = new \AopClient;
		$aop->gatewayUrl = $this->gatewayUrl;
        $aop->appId = $this->appId;
        $aop->rsaPrivateKey = $this->rsaPrivateKey;
        $aop->format = $this->format;
        $aop->charset = $this->charset;
        $aop->signType = $this->signType;
        $aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
		
        $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $recharge = M('pay_record');
        $olist = $recharge->where("oid = '".$o_id."'")->find();
        
        if($olist){
			$arr['body'] = '娃娃币充值';
			$arr['subject'] = '支付宝娃娃币充值';
			$arr['out_trade_no'] = $olist['oid'];
			$arr['timeout_express'] = '30m';
			$arr['total_amount'] = $money;
			$arr['product_code'] = 'QUICK_MSECURITY_PAY';
			$bizcontent = json_encode($arr);
			$request->setNotifyUrl('http://'.$_SERVER['HTTP_HOST'].'/Api/Alipay/notify_pay_recharge');
			$request->setBizContent($bizcontent);
			//这里和普通的接口调用不同，使用的是sdkExecute
			$response = $aop->sdkExecute($request);
			$info['code'] = 1;
			$info['msg'] = '请求成功';
			$info['data']['paycode'] = $response;
			$this->ajaxReturn($info);
		 }else{
			$info['code'] = -1;
			$info['msg'] = '请求失败';
			$this->ajaxReturn($info);
		}
        
    }


	//支付宝充值娃娃币回调
	public function notify_pay_recharge(){
		$post = I('post.');
		//$aop = new \AopClient;
		//$aop->rsaPrivateKey = $this->rsaPrivateKey;
		$result = true;
		//$this->ajaxReturn($aop);
		//$result = $aop->rsaCheckV1($post, NULL, $config['signType']);  //检验密匙
		if ($result && ($post['trade_status'] == 'TRADE_FINISHED' || $post['trade_status'] == 'TRADE_SUCCESS')) {
			 $out_trade_no = $post['out_trade_no'];

			 $recharge = M('pay_record');//充值记录
             $olist = $recharge->where("oid = '".$out_trade_no."'")->find();
             $rule_list = M('charge_rules')->where('id='.$olist['pay_id'].'')->find();//充值规则
             //$this->ajaxReturn($rule_list);
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
				$data['type']   = 2;

				$re4 = $recharge->where('id = '.$olist['id'])->save($data);
               //$this->ajaxReturn($data);

				//流水表
				$insert=array("type"=>'income',"action"=>'coin',"uid"=>$olist['user_id'],"touid"=>0,"giftid"=>intval($rule_list['give']),"giftcount"=>intval($num),"totalcoin"=>$olist['coin'],"givecoin"=>$olist['coingive'],"giveclaw"=>intval($claw),"realmoney"=>$olist['money'],"givemoney"=>$olist['coingive']/10,"showid"=>0,"addtime"=>time() );
				M('users_coinrecord')->add($insert);		

			}
		
		}
		echo 'success';
	}
	

	
} 