<?php

date_default_timezone_set("UTC");
class Model_Alicdnrecord extends Model_User {


//阿里的配置信息
const access_key_id='LTAIGg4oVflf5Dkp';
const access_key_secret='o2f0AM4iMljhvh8sIioodgvEhD4ihf';
//录播的流地址和应用名配置信息    
const DomainName = 'testnanjingcdn.yunbaozhibo.com';
const AppName = '5showcam';

function setParameter($specialParameter){
   $time = date('Y-m-d H:i:s', time());
   $var = strtr($time, ' ', 'T');
   $Timestamp = $var . 'Z';
   $signature_nonce = '';
   for($i =0 ; $i < 14; $i++){
		 $signature_nonce .= mt_rand(0,9);
	 }
   $publicParameter = array(
      'Format'         => 'JSON',
      'Version'        => '2014-11-11',
      'SignatureMethod'   => 'HMAC-SHA1',
      'TimeStamp'         => $Timestamp,
      'SignatureVersion'  => '1.0',
      'SignatureNonce'    => $signature_nonce,
   );

   $parameter = array_merge($publicParameter, $specialParameter);
   return $parameter;
}

function getStringToSign($parameter,$access_key_secret){
   ksort($parameter); 
   foreach($parameter as $key => $value){
      $str[] = rawurlencode($key). "=" .rawurlencode($value);
   }
   $ss = "";
   if(!empty($str)){
      for($i=0; $i<count($str); $i++){
         if(!isset($str[$i+1])){
            $ss .= $str[$i];
         }
         else
            $ss .= $str[$i]."&";
      }
   }

   $StringToSign = "GET" . "&" . rawurlencode("/") . "&" . rawurlencode($ss);


   $signature = base64_encode(hash_hmac("sha1", $StringToSign, $access_key_secret."&", true));
	 
   $url = "https://cdn.aliyuncs.com/?" . $ss . "&Signature=" . rawurlencode($signature);
   return $url;
}

function curl_get($url){
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
   curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $output = curl_exec($ch);
   curl_close($ch);
   return $output;
}

function getAliCdnRecord($id,$access_key_id = self::access_key_id, $access_key_secret = self::access_key_secret,$DomainName=self::DomainName,$AppName=self::AppName){

   if(empty($access_key_id) || empty($access_key_secret)){
      $message = array(
         'status'   => 'failed',
         'reason'   => 'Access key Id or access key secret is invalid',
      );
      return 1001;
   }
   $liverecord=DI()->notorm->users_liverecord
   				   ->select("*")
   				   ->where("id='{$id}'")
   				   ->fetchOne();

	if(!$liverecord){
		return 1002;
	}
   $live_starttime = $liverecord['starttime']-100;
   $live_endtime   = $liverecord['endtime'] + 100;
	$StartTime=gmdate("Y-m-d\TH:i:s\Z",$live_starttime);
	$EndTime=gmdate("Y-m-d\TH:i:s\Z",$live_endtime);

	$StreamName=$liverecord['stream'];
   $action = 'DescribeLiveStreamRecordIndexFiles';

   
   $specialParameter = array(
      'AccessKeyId'     => $access_key_id,
      'Action'         => $action,
      'DomainName'         => $DomainName,
      'AppName'         => $AppName,
      'StreamName'         => $StreamName,
      'StartTime'         => $StartTime,
      'EndTime'         => $EndTime,
   );

   $parameter = $this->setParameter($specialParameter);
   $url = $this->getStringToSign($parameter,$access_key_secret);
   $ret = $this->curl_get($url);

   $res_arr = json_decode($ret,true);

   if(!$res_arr['RecordIndexInfoList']['RecordIndexInfo']){
   		return 1002;
   }

   $RecordUrl = $res_arr['RecordIndexInfoList']['RecordIndexInfo'][0]['RecordUrl'];

   return $RecordUrl;
}

}
