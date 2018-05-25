<?php

class Model_Charge extends Model_Common {
	/* 订单号 */
	public function getOrderId($changeid,$orderinfo) {
		
		$charge=DI()->notorm->charge_rules->select('*')->where('id=?',$changeid)->fetchOne();
		
		if(!$charge || ($charge['coin']!=$orderinfo['coin'] && $charge['coin_ios']!=$orderinfo['coin']) || ($charge['money']!=$orderinfo['money']  && $charge['money']!=$orderinfo['money_ios'] )){
			return 1003;
		}
		
		$orderinfo['coin_give']=$charge['give'];
		$orderinfo['coin_firstgive']=$charge['firstgive'];
		

		$result= DI()->notorm->users_charge->insert($orderinfo);

		return $result;
	}			

}
