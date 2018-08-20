<?php

namespace Api\Controller;

use Think\Controller;

class BaseController extends Controller
{
    protected $base_url;
    protected $user;
    protected $user_id;
    protected $token;

    public function __construct()
    {
        $this->base_url = $_SERVER['HTTP_HOST'];
        //跳过验证
        if (ACTION_NAME != 'send' && I('api_name') != 'email_status' && I('api_name') != 'get_code_config' && I('api_name') != 'get_user_agreement' && I('api_name') != 'get_version' &&CONTROLLER_NAME!='Index' )
            $this->checkToken();
    }

    protected $uploadConfig = array(
        'img' => array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 52428800, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './data/upload/', //保存根路径
            'savePath' => 'img/', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => null, //存在同名是否覆盖
            'hash' => true, //是否生成hash编码
            'callback' => null, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver' => '', // 文件上传驱动
            'driverConfig' => array(), // 上传驱动配置
        ),
        'video' => array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 52428800, //上传的文件大小限制 (0-不做限制)
            'exts' => array('mp4'), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './data/upload/', //保存根路径
            'savePath' => 'video/', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => null, //存在同名是否覆盖
            'hash' => true, //是否生成hash编码
            'callback' => null, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver' => '', // 文件上传驱动
            'driverConfig' => array(), // 上传驱动配置
        ),
    );

    protected function log_message($msg = '')
    {
        log_message($msg);
    }

    protected function get_upload_path($file)
    {
        if (strpos($file, "http") === 0 || strpos($file, "https") === 0) {
            return $file;
        } else {
            return 'http://' . $this->base_url . "/" . ltrim($file, '/');
        }
    }

    //判断登录token
    protected function checkToken()
    {
        $token = $_GET['token'];
        if (empty($token)) $token = $_POST['token'];
        if (empty($token)) $token = $_SERVER['token'];
        if (empty($token)) $token = $_SERVER['HTTP_TOKEN'];

        $this->token = $token; // token
        if (empty($this->token))
            exit(json_encode(array('code' => 101, 'msg' => 'token不能为空')));

        $map['token'] = $this->token;
        $user = M('users')->where($map)->find();
        if (empty($user))
            exit(json_encode(array('code' => 101, 'msg' => 'token错误')));

        if( $user['user_status'] == 0 )
            exit(json_encode(array('code' => 101, 'msg' => '您的账号已被封')));

        $user['tim_uid'] = 'wawaji_' . $user['id'];
        $user['tim_pwd'] = 'wawaji_' . $user['id'];
        $this->user = $user;
        $this->user_id = $user['id'];
    }

    protected function umengpush($androidtoken, $iphonetoken, $title, $message)
    {
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidBroadcast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidFilecast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidGroupcast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidUnicast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/android/AndroidCustomizedcast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSBroadcast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSFilecast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSGroupcast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSUnicast.php');
        require_once(THINK_PATH . '../../simplewind/Lib/Extend/umeng_php_sdk_v1.5/php/src/notification/ios/IOSCustomizedcast.php');
        $id = '';
        if ($androidtoken) {
            $id = M('users')->where(['androidtoken' => $androidtoken])->getField('id');
        } else if ($iphonetoken) {
            $id = M('users')->where(['iphonetoken' => $iphonetoken])->getField('id');
        }

        if (!empty($androidtoken)) {
            try {
                $unicast = new \AndroidUnicast();
                $unicast->setAppMasterSecret('yftyxfo9mce09rhwt69l3t5uurku1kqc');
                $unicast->setPredefinedKeyValue("appkey", '5a1e663ef29d986cf9000160');
                $unicast->setPredefinedKeyValue("timestamp", time());
                // Set your device tokens here
                $unicast->setPredefinedKeyValue("device_tokens", $androidtoken);
                $unicast->setPredefinedKeyValue("ticker", $title);
                $unicast->setPredefinedKeyValue("title", $title);
                $unicast->setPredefinedKeyValue("text", $message);
                $unicast->setPredefinedKeyValue("after_open", "go_custom");
                $unicast->setPredefinedKeyValue("custom", "go_custom");
                $unicast->setPredefinedKeyValue('icon', 'small_app_logo');
                $unicast->setPredefinedKeyValue('largeIcon', 'app_logo');
                //$unicast-?setPredefinedKeyValue('img', 'http://');
                // Set 'production_mode' to 'false' if it's a test device.
                // For how to register a test device, please see the developer doc.
                $unicast->setPredefinedKeyValue("production_mode", "true");
                // Set extra fields
//                $unicast->setExtraField("user_id", $id);
                //$unicast->setExtraField("order_id", $order_id);
                $unicast->send();
            } catch (Exception $e) {
                //
            }
        }
        if (!empty($iphonetoken)) {
            try {
                $unicast = new \IOSUnicast();
                $unicast->setAppMasterSecret('1awu0wjgjxv5w0wfupbpjgivnbwpufrl');
                $unicast->setPredefinedKeyValue("appkey", '5a1e45a08f4a9d570c000142');
                $unicast->setPredefinedKeyValue("timestamp", time());
                // Set your device tokens here
                $unicast->setPredefinedKeyValue("device_tokens", $iphonetoken);
                $unicast->setPredefinedKeyValue("alert", $message);
                $unicast->setPredefinedKeyValue("badge", 0);
                $unicast->setPredefinedKeyValue("sound", "chime");
                // Set 'production_mode' to 'true' if your app is under production mode
                $unicast->setPredefinedKeyValue("production_mode", "false");
                // Set customized fields
//                $unicast->setCustomizedField("user_id", $id);
                //$unicast->setCustomizedField("order_id", $order_id);
                $unicast->send();
            } catch (Exception $e) {
                //
            }
        }
    }

    protected function sendmail($toname = '', $tomail = '', $subject = '', $body = '')
    {

        if (empty($tomail)) $this->_return(-1, '接收邮箱不能为空');

        //include_once THINK_PATH.'/../Public/Extend/PHPMailer_v5.1/class.phpmailer.php';
//        Vendor("PHPMailer.phpmailer");

        $mailer = new \PHPMailer();
        //var_dump($mailer);die();
        $mailer->IsSMTP(); // telling the class to use SMTP
        $mailer->SMTPDebug = false; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only

        //$smtp = session(C('WEBSITEID').'setting.smtp');
//        $smtp = C('THINK_EMAIL');
        //var_dump($smtp);die();
        $mailer->SMTPAuth = true; // enable SMTP authentication
        $mailer->SMTPSecure = 'ssl';//tsl//ssl
        $mailer->CharSet = 'UTF-8';
        $mailer->Host = 'smtp.exmail.qq.com'; // sets the SMTP server
        $mailer->Port = 465; // set the SMTP port for the GMAIL server
        $mailer->Username = 'noreply@aizhua.net'; // SMTP account username
        //$mailer->Password   = defilter($smtp['SMTP_PASS']); // SMTP account password
        $mailer->Password = 'tdAHTsbqgJJ6zgc7';

        // if($fromname === false)$fromname = $smtp['name'];
        // if($frommail === false)$frommail = $smtp['mail'];
        $from_email = 'noreply@aizhua.net';
        $from_name = '爱抓娃娃机';
        $mailer->SetFrom($from_email, $from_name);

        $mailer->FromName = $from_name;
        $mailer->From = $from_email;

        $mailer->AddAddress($tomail, $toname);
        $mailer->Subject = $subject;
        $mailer->AltBody = "";
        $mailer->MsgHTML($body);
        $info = $mailer->Send() ? true : $mailer->ErrorInfo;
        return $info;
    }


    /**
     * 直接ajax返回成功信息（返回类型仅适用于json格式）
     * @type : 1数组 2字符串 3对象
     */
    protected function _return($code = 1, $msg = '', $data = array(),$data2=array(), $type = 1)
    {
        $info['code'] = $code;
        $info['msg'] = $msg;
        if($data2){
            foreach($data2 as $k =>$v){
                $info[$k] = $v;
            }
        }
        if ($type == 1)
            $info['data'] = $data ?: array();
        else if ($type == 2)
            $info['data'] = $data ?: '';
        else if ($type == 3)
            $info['data'] = $data ?: (object)[];

        $conf = M('users')->where(['id'=>$this->user_id])->getField('user_setting');
        $conf = json_decode($conf);
        require_once './simplewind/Lib/Extend/Trans.php';
        $trans = new \Trans();
        $info = $trans->trans_arr($info,$conf->lan);
        file_put_contents('./data/runtime/test.txt',var_export($this->user,true).'------------');

        exit(json_encode($info));
    }

    /**
     * 建议用uploads取得所有上传图片。再从返回值取数据
     * 上传图片
     * restype 1返回ID
     * restype 2返回路径
     * @return string//如果是数组表单返回array
     */
    protected function uploadOne($field = '表单file name', $type = 'img', $restype = 2)
    {
        $res = $this->uploads($type, $restype, array('field' => $field));
        if ($res['error']) $this->_return(-1, $res['error']);
        return $res[$field];
    }

    /**
     * 上传图片
     * restype 1返回ID
     * restype 2返回路径
     * params 其它参数
     * @return array
     */
    protected function uploads($type = 'img', $restype = 2, $params = array())
    {
        $upload = new \Think\Upload($this->uploadConfig[$type]);
        if ($list = $upload->upload($params['field'] ? array($params['field'] => $_FILES[$params['field']]) : $_FILES)) {
            $arr = array();
            foreach ($list as $k => $v) {
                $pic = M('Upload_img');
                $data = array();
                $data['name'] = basename($v['name']);
                $data['ext'] = $v['ext'];
                $data['type'] = $type;
                $data['savename'] = $v['savename'];
                $data['savepath'] = $v['savepath'];
                $data['saveurl'] = $v['url'];
                $data['user_id'] = $this->user_id ? $this->user_id : 0;
                $re = $pic->add($data);

                if (isset($arr[$k]) && !is_array($arr[$k])) {
                    $arr[$k] = array($arr[$k], $restype == 1 ? $re : $v['url']);
                } else {
                    if (is_array($arr[$k])) {
                        $arr[$k][] = $restype == 1 ? $re : $v['url'];
                    } else {
                        $arr[$k] = $restype == 1 ? $re : $v['url'];
                    }
                }
            }
            $err = $upload->getError();
            if ($err) $arr['error'] = $err;
            return $arr;
        } else {
            $this->_return(-1, $upload->getError());
        }
    }


    /**
     * 增加娃娃币
     * @param $user_id 用户id
     * @return bool
     */
    protected function set_coin($user_id, $coin_num = '')
    {
        if (empty($coin_num)) $coin_num = M('config')->where(['id' => 1])->getField('code_wawabi');
        M('users')->where(['id' => $user_id])->setInc('coin', $coin_num);
        M('users')->where(['id' => $user_id])->setInc('free_coin', $coin_num);
        M('users')->where(['id' => $user_id])->setInc('invite_coin', $coin_num);
        $this->add_coinrecord($user_id, 'income', 'invite', $coin_num);
    }

    /**
     * 添加流水账单
     * @param $user_id 用户id
     * @param string $type 收支类型 收入(income)/支出(expend)
     * @param string $action 收支动作 zhuawawa=>'抓娃娃'，invite=>'邀请奖励'...
     * @param int $totalcoin
     * @param int $show_id
     */
    protected function add_coinrecord($user_id, $type = 'expend', $action = 'zhuawawa', $totalcoin = 0, $show_id = 0)
    {
        $insert = array("type" => $type, "action" => $action, "uid" => $user_id, "totalcoin" => $totalcoin, "showid" => $show_id, "addtime" => time());
        M('users_coinrecord')->add($insert);
    }


    /**
     * 添加邀请码兑换记录
     * @param $user_id 兑换者id
     * @param $owner_id 邀请码拥有者id
     * @param $code 邀请码
     * @param string $num 获赠娃娃币数量（默认为系统配置数量）
     */
    protected function to_code_record($user_id, $owner_id, $code, $num = -1)
    {
        if ($num == -1) $num = M('config')->where(['id' => 1])->getField('code_wawabi');
        M('code_record')->add(array('code' => $code, 'user_id' => $user_id, 'owner_id' => $owner_id, 'num' => $num, 'ctime' => time()));
    }


    /**
     * 正则判断手机号
     * @return mixed
     */
    protected function is_mobile($mobile)
    {
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[1,0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile);
    }

    /**
     * 验证邮箱格式
     * @param $email
     * @return int
     */
    protected function is_email($email)
    {
        return preg_match("/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i", $email);
    }

    protected function passtime($time)
    {
        $cha = time() - $time;
        $iz = floor($cha / 60);
        $hz = floor($iz / 60);
        $dz = floor($hz / 24);
        /* 秒 */
        $s = $cha % 60;
        /* 分 */
        $i = floor($iz % 60);
        /* 时 */
        $h = floor($hz / 24);
        /* 天 */

        if ($cha < 60) {
            return $cha . '秒前';
        } else if ($iz < 60) {
            return $iz . '分钟前';
        } else if ($hz < 24) {
            return $hz . '小时' . $i . '分钟前';
        } else if ($dz < 30) {
            return $dz . '天前';
        } else {
            return date("Y-m-d", $time);
        }
    }

    protected function notice_add($type = 0, $user_id, $title, $to_user_id = 0, $desc = '', $content = '')
    {
        $data = array(
            'type' => $type,
            'user_id' => $user_id,
            'to_user_id' => $to_user_id,
            'title' => $title,
            'desc' => $desc ? $desc : $title,
            'content' => $content ? $content : ($desc ? $desc : $title),
            'status' => 0,
            'ctime' => time(),
        );
        $newid = M('notice')->add($data);
        if ($to_user_id) {
            $data['avatar_thumb'] = $this->get_upload_path(M('users')->where('id=' . $to_user_id)->getField('avatar_thumb'));
            $data['user'] = M('users')->where(['id' => $to_user_id])->getField('user_nicename');
        } else {
            $data['avatar_thumb'] = $this->get_upload_path(M('users')->where('id=' . $user_id)->getField('avatar_thumb'));
            $data['user'] = M('users')->where(['id' => $user_id])->getField('user_nicename');
        }

        $data['notice_id'] = $newid;

        $data['avatar_thumb2'] = '';
        $data['user2'] = '';
        if ($to_user_id) {
            $data['avatar_thumb2'] = $this->get_upload_path(M('users')->where('id=' . $user_id)->getField('avatar_thumb'));
            $data['user2'] = M('users')->where(['id' => $user_id])->getField('user_nicename');
        }

		if($type != 1){
        	/* 友盟推送 */
        	$androidtoken = M('users')->where(['id' => $user_id])->getField('androidtoken');
        	$iphonetoken = M('users')->where(['id' => $user_id])->getField('iphonetoken');
        	$this->umengpush($androidtoken, $iphonetoken, $title, $content);
		}
        return $data;
    }

    protected function post_url($url, $post = '', $host = '', $referrer = '', $cookie = '', $proxy = -1, $sock = false, $useragent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1)')
    {//'192.3.25.99:7808'
        if (empty($post) && empty($host) && empty($referrer) && empty($cookie) && ($proxy == -1 || empty($proxy)) && empty($useragent)) return @file_get_contents($url);
        $method = empty($post) ? 'GET' : 'POST';

        if (function_exists('curl_init') && empty($cookie)) {
            $ch = @curl_init();
            @curl_setopt($ch, CURLOPT_URL, $url);
            if ($proxy != -1 && !empty($proxy)) @curl_setopt($ch, CURLOPT_PROXY, 'http://' . $proxy);
            @curl_setopt($ch, CURLOPT_REFERER, $referrer);
            @curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
            @curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_FILE_PATH);
            @curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_FILE_PATH);
            @curl_setopt($ch, CURLOPT_HEADER, 0);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            @curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            @curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            if ($method == 'POST') {
                @curl_setopt($ch, CURLOPT_POST, 1);
                @curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }

            $result = @curl_exec($ch);
            @curl_close($ch);
        }

        if ($result === false && function_exists('file_get_contents')) {
            $urls = parse_url($url);
            if (empty($host)) $host = $urls['host'];
            $httpheader = $method . " " . $url . " HTTP/1.1\r\n";
            $httpheader .= "Accept: */*\r\n";
            $httpheader .= "Accept-Language: zh-cn\r\n";
            $httpheader .= "Referer: " . $referrer . "\r\n";
            if ($method == 'POST') $httpheader .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $httpheader .= "User-Agent: " . $useragent . "\r\n";
            $httpheader .= "Host: " . $host . "\r\n";
            if ($method == 'POST') $httpheader .= "Content-Length: " . strlen($post) . "\r\n";
            $httpheader .= "Connection: Keep-Alive\r\n";
            $httpheader .= "Cookie: " . $cookie . "\r\n";

            $opts = array(
                'http' => array(
                    'method' => $method,
                    'header' => $httpheader,
                    'timeout' => 60,
                    'content' => ($method == 'POST' ? $post : '')
                )
            );
            if ($proxy != -1 && !empty($proxy)) {
                $opts['http']['proxy'] = 'tcp://' . $proxy;
                $opts['http']['request_fulluri'] = true;
            }
            $context = @stream_context_create($opts);
            $result = @file_get_contents($url, 'r', $context);
        }

        if ($sock && $result === false && function_exists('fsockopen')) {
            $urls = parse_url($url);
            if (empty($host)) $host = $urls['host'];
            $port = empty($urls['port']) ? 80 : $urls['port'];

            $httpheader = $method . " " . $url . " HTTP/1.1\r\n";
            $httpheader .= "Accept: */*\r\n";
            $httpheader .= "Accept-Language: zh-cn\r\n";
            $httpheader .= "Referer: " . $referrer . "\r\n";
            if ($method == 'POST') $httpheader .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $httpheader .= "User-Agent: " . $useragent . "\r\n";
            $httpheader .= "Host: " . $host . "\r\n";
            if ($method == 'POST') $httpheader .= "Content-Length: " . strlen($post) . "\r\n";
            $httpheader .= "Connection: Keep-Alive\r\n";
            $httpheader .= "Cookie: " . $cookie . "\r\n";
            $httpheader .= "\r\n";
            if ($method == 'POST') $httpheader .= $post;
            $fd = false;
            if ($proxy != -1 && !empty($proxy)) {
                $proxys = explode(':', $proxy);
                $fd = @fsockopen($proxys[0], $proxys[1]);
            } else {
                $fd = @fsockopen($host, $port);
            }
            @fwrite($fd, $httpheader);
            @stream_set_timeout($fd, 60);
            $result = '';
            while (!@feof($fd)) {
                $result .= @fread($fd, 8192);
            }
            @fclose($fd);
        }

        return $result;
    }

    /**
     *   生成订单号
     */
    protected function get_orderno(){
        $ors = time();
        $ors = str_shuffle($ors);
        if( M('waybill')->where(['waybillno'=>$ors])->find() )
            $ors = $this->get_orderno();
        return $ors;
    }

}