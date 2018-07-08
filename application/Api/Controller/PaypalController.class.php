<?php
// 微信支付JSAPI版本
// 基于版本 V3
// By App 2015-1-20
namespace Api\Controller;

use Think\Controller;

class PaypalController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function payPayPal($oid, $price/*, $address = array()*/)
    {
        require_once THINK_PATH . "../../simplewind/Lib/Extend/PayPal-PHP-SDK/autoload.php";
        $paypal = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'ASTHI1o8FwRkLok2fUm012L8YFeZExXKsqvNBub36Dsy3_D3iqE3ShLNFZkDABrduqaUczTalrkWhHpz',
                'EKymXzEdLrCn4K7HkOG8UoVORTiy5JZjz8zXeo6QmnhMxOu7VX06zAnCpj-kz1qoz-CrCumF7EUpptGk'
            )
        );
        $paypal->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => false,
                'log.FileName' => THINK_PATH . "../../data/runtime/log.txt",
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => false,
                //'http.CURLOPT_CONNECTTIMEOUT' => 30
                //'http.headers.PayPal-Partner-Attribution-Id' => $oid
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

        // ### Payer
        // A resource representing a Payer that funds a payment
        // For paypal account payments, set payment method
        // to 'paypal'.
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod("paypal");
        /*
        // ### Itemized information
        // (Optional) Lets you specify item wise
        // information
        $item1 = new \PayPal\Api\Item();
        $item1->setName('aizhua')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setSku("wawaji") // Similar to `item_number` in Classic API
            ->setPrice($price * 100);

        $itemList = new \PayPal\Api\ItemList();
        $itemList->setItems(array($item1));

        if(!is_array($address))$address = json_decode($address, true);
        if($address['name']){
            // 自定义用户收货地址，避免用户在paypal上账单的收货地址和销售方收货地址有出入
            // 这里定义了收货地址，用户在支付过程中就不能更改收货地址，否则，用户可以自己更改收货地址
            $shipAddress = new \PayPal\Api\ShippingAddress();
            $shipAddress->setRecipientName($address['name'])//什么名字
                    ->setLine1($address['line1'])//什么街什么路什么小区
                    ->setLine2($address['line2'])//什么单元什么号
                    ->setCity($address['city'])//城市名
                    ->setState($address['state'])//浙江省
                    ->setPhone($address['phone'])//电话
                    ->setPostalCode($address['postcode'])//邮编
                    ->setCountryCode($address['country']);//CN

            $itemList->setShippingAddress($shipAddress);
        }

        // ### Additional payment details
        // Use this optional field to set additional
        // payment information such as tax, shipping
        // charges etc.
        $details = new \PayPal\Api\Details();
        $details->setShipping(5)
            ->setTax(10)
            ->setSubtotal(70);
        */
        // ### Amount
        // Lets you specify a payment amount.
        // You can also specify additional details
        // such as shipping, tax.
        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency("USD")
            ->setTotal($price);

        // ### Transaction
        // A transaction defines the contract of a
        // payment - what is the payment for and who
        // is fulfilling it.
        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount);

        // ### Redirect urls
        // Set the urls that the buyer must be redirected to after
        // payment approval/ cancellation.
        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl(U('Api/Paypal/gold_notify', array('oid' => $oid, 'success' => 'true'), true, true))
            ->setCancelUrl(U('Api/Paypal/gold_notify', array('oid' => $oid, 'success' => 'false'), true, true));

        // ### Payment
        // A Payment Resource; create one using
        // the above types and intent set to 'sale'
        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));

        $payment->create($paypal);

        $approvalUrl = $payment->getApprovalLink();

        $this->ajaxReturn(array('code' => 1, 'msg' => '获取支付参数成功', 'data' => $approvalUrl));
    }

    /*
    array (
      'transaction_subject' => 'WW201712011325299575',
      'payment_date' => '21:26:40 Nov 30, 2017 PST',
      'txn_type' => 'express_checkout',
      'last_name' => 'Lo',
      'residence_country' => 'HK',
      'item_name' => 'WW201712011325299575',
      'payment_gross' => '0.01',
      'mc_currency' => 'USD',
      'business' => 'linxiaodong@aizhua.net',
      'payment_type' => 'instant',
      'protection_eligibility' => 'Ineligible',
      'verify_sign' => 'AQzo9Dng62RAzHAAta--mqT4iJ.eA46PUkd6QXUc0YxM4B.1C.MBqMai',
      'payer_status' => 'unverified',
      'payer_email' => '17088991@qq.com',
      'txn_id' => '0HU032821K793991S',
      'quantity' => '1',
      'receiver_email' => 'linxiaodong@aizhua.net',
      'first_name' => 'Yuen Shun',
      'payer_id' => '5WMZ3CW4KRR64',
      'receiver_id' => 'PENS9XNPVP6DQ',
      'item_number' => '',
      'payment_status' => 'Completed',
      'payment_fee' => '0.01',
      'mc_fee' => '0.01',
      'mc_gross' => '0.01',
      'custom' => 'WW201712011325299575',
      'charset' => 'gb2312',
      'notify_version' => '3.8',
      'ipn_track_id' => '9d8501c92e448',
    )
    */
    //充值娃娃币
    public function gold_notify()
    {
        $this->log_message($_GET);
        $this->log_message('$_POST');
        $this->log_message($_POST);
        $this->log_message($GLOBALS['HTTP_RAW_POST_DATA']);

        /*require_once THINK_PATH."../../simplewind/Lib/Extend/PayPal-PHP-SDK/autoload.php";
        $paypal = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'ASTHI1o8FwRkLok2fUm012L8YFeZExXKsqvNBub36Dsy3_D3iqE3ShLNFZkDABrduqaUczTalrkWhHpz',
                'EKymXzEdLrCn4K7HkOG8UoVORTiy5JZjz8zXeo6QmnhMxOu7VX06zAnCpj-kz1qoz-CrCumF7EUpptGk'
            )
        );
        $paypal->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => false,
                'log.FileName' => THINK_PATH."../../data/runtime/log.txt",
                'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => false,
                //'http.CURLOPT_CONNECTTIMEOUT' => 30
                //'http.headers.PayPal-Partner-Attribution-Id' => $oid
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );*/

        if ($_POST['payment_status'] == 'Completed') {

            /*
            // Get the payment Object by passing paymentId
            // payment id was previously stored in session in
            // CreatePaymentUsingPayPal.php
            $paymentId = $_POST['verify_sign'];
            $payment = \PayPal\Api\Payment::get($paymentId, $paypal);
            $this->log_message($payment);
            // ### Payment Execute
            // PaymentExecution object includes information necessary
            // to execute a PayPal account payment.
            // The payer_id is added to the request query parameters
            // when the user is redirected from paypal back to your site
            $execution = new \PayPal\Api\PaymentExecution();
            $execution->setPayerId($_POST['payer_id']);

            // ### Optional Changes to Amount
            // If you wish to update the amount that you wish to charge the customer,
            // based on the shipping address or any other reason, you could
            // do that by passing the transaction object with just `amount` field in it.
            // Here is the example on how we changed the shipping to $1 more than before.
            $transaction = new \PayPal\Api\Transaction();
            $amount = new \PayPal\Api\Amount();
            */
            /*$details = new \PayPal\Api\Details();

            $details->setShipping(5)
                ->setTax(10)
                ->setSubtotal(70);
            */
            /*
            $amount->setCurrency('USD');
            $amount->setTotal(85);
            //$amount->setDetails($details);
            $transaction->setAmount($amount);

            // Add the above transaction object inside our Execution object.
            $execution->addTransaction($transaction);

            $result = $payment->execute($execution, $paypal);
            */

            $result = trim(@file_get_contents('https://ipnpb.paypal.com/cgi-bin/webscr?cmd=_notify-validate&' . http_build_query($_POST)));

            if ($result == 'VERIFIED') {
                //获取订单号
                $out_trade_no = $_POST['custom'];

                $recharge = M('pay_record');//充值记录
                $olist = $recharge->where("oid = '" . $out_trade_no . "'")->find();
                $rule_list = M('charge_rules')->where('id=' . $olist['pay_id'] . '')->find();//充值规则
                // $this->ajaxReturn($rule_list);
                if ($olist['status'] == 0) {
                    //赠送礼品
                    $num = 0;
                    $name = '';
                    if ($rule_list['give'] !== 0) {
                        $name = M('give_gift')->where('id=' . $rule_list['give'] . '')->getField('name');
                        $num = $rule_list['number'];
                        for ($i = 1; $i <= $num; $i++) {
                            $gift['gift_id'] = $rule_list['give'];
                            $gift['type'] = 1;
                            $gift['user_id'] = $olist['user_id'];
                            $gift['ctime'] = time();
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
                    $claw = $rule_list['claw'];
                    $number = $olist['coin'] - $give_coin;
                    $first_coin = $olist['coin'];
                    if ($give_coin != 0 && $claw != 0) {
                        $data['log'] = '首冲赠送娃娃币' . $first_coin . '个，' . $claw . '次甩爪';
                    } elseif ($give_coin != 0 && $claw != 0 && $rule_list['give'] != 0) {
                        $data['log'] = '首冲赠送娃娃币' . $first_coin . '个，' . $claw . '次甩爪，"' . $name . '"' . $num . '个';
                    } elseif ($give_coin != 0 && $rule_list['give'] != 0) {//$give_coin != 0　&&　
                        $data['log'] = '首冲赠送娃娃币' . $first_coin . '个，"' . $name . '"' . $num . '个';
                    } elseif ($give_coin != 0) {
                        $data['log'] = '首冲赠送娃娃币' . $first_coin . '个';
                    } elseif ($claw != 0 && $rule_list['give'] != 0) {
                        $data['log'] = '赠送娃娃币' . $number . '个，' . $claw . '次甩爪' . '，"' . $name . '"' . $num . '个';
                    } elseif ($rule_list['give'] != 0) {
                        $data['log'] = '赠送娃娃币' . $number . '，"' . $name . '"' . $num . '个';
                    } elseif ($claw != 0) {
                        $data['log'] = '赠送娃娃币' . $number . '，' . $claw . '次甩爪';
                    } else {
                        $data['log'] = '赠送娃娃币' . $olist['coin'];
                    }


                    $data['paytime'] = time();
                    $data['status'] = 1;
                    $data['type'] = 3;
                    $re4 = $recharge->where('id = ' . $olist['id'])->save($data);
                    // $res = M('test')->add(array('content'=>$data['log'],'id'=>$number));

                    //流水表
                    $insert = array("type" => 'income', "action" => 'coin', "uid" => $olist['user_id'], "touid" => 0, "giftid" => intval($rule_list['give']), "giftcount" => intval($num), "totalcoin" => $olist['coin'], "givecoin" => $olist['coingive'], "giveclaw" => intval($claw), "realmoney" => $olist['money'], "givemoney" => $olist['coingive'] / 10, "showid" => 0, "addtime" => time());
                    //$this->log_message($insert);
                    M('users_coinrecord')->add($insert);

                }
                $this->ajaxReturn(array('code' => 1, 'msg' => '支付成功'));
            }
        }
        $this->ajaxReturn(array('code' => -1001, 'msg' => '支付验证失败'));
    }


    protected function log_message($msg = '')
    {
        $file = THINK_PATH . "../../data/logs/log.txt";
        file_put_contents($file, date('Y-m-d H:i:s') . "\r\n" . var_export($msg, true) . "\r\n\r\n", FILE_APPEND | LOCK_EX);
    }


}
