<?php
session_start();

class Api_Login extends Api_Common
{
    public function getRules()
    {
        return array(
            'userLogin' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '5', 'max' => '30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '1', 'max' => '30', 'desc' => '密码'),
                'sys' => array('name' => 'sys', 'type' => 'int', 'require' => false, 'desc' => '系统平台(安卓/ios)'),
                'androidtoken' => array('name' => 'androidtoken', 'type' => 'string', 'require' => false, 'desc' => '安卓'),
                'iphonetoken' => array('name' => 'iphonetoken', 'type' => 'string', 'require' => false, 'desc' => '苹果'),
                //'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true,   'desc' => '验证码'),
            ),
            'userReg' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '6', 'max' => '30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '1', 'max' => '30', 'desc' => '密码'),
//				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '验证码'),
            ),
            'userFindPass' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '6', 'max' => '30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '1', 'max' => '30', 'desc' => '密码'),
//				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '验证码'),
            ),
            'userLoginByThird' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '第三方openid'),
                'type' => array('name' => 'type', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '第三方标识'),
                'nicename' => array('name' => 'nicename', 'type' => 'string', 'default' => '', 'desc' => '第三方昵称'),
                'avatar' => array('name' => 'avatar', 'type' => 'string', 'default' => '', 'desc' => '第三方头像'),
                'sex' => array('name' => 'sex', 'type' => 'int', 'default' => 2, 'desc' => '第三方性别'),
                'sys' => array('name' => 'sys', 'type' => 'int','require'=>false, 'desc' => '系统平台(安卓/ios)'),
                'androidtoken' => array('name' => 'androidtoken', 'type' => 'string', 'require' => false, 'desc' => '安卓'),
                'iphonetoken' => array('name' => 'iphonetoken', 'type' => 'string', 'require' => false, 'desc' => '苹果'),
            ),
            'userMobileReg' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '6', 'max' => '30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '1', 'max' => '30', 'desc' => '密码'),
//				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '验证码'),
            ),
            'userMobileFindPass' => array(
                'user_login' => array('name' => 'user_login', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '6', 'max' => '30', 'desc' => '账号'),
                'user_pass' => array('name' => 'user_pass', 'type' => 'string', 'min' => 1, 'require' => true, 'min' => '1', 'max' => '30', 'desc' => '密码'),
//				'user_pass2' => array('name' => 'user_pass2', 'type' => 'string', 'min' => 1, 'require' => true,  'min' => '1',  'max'=>'30', 'desc' => '确认密码'),
                'code' => array('name' => 'code', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '验证码'),
            ),
            'getCode' => array(
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '手机号'),
            ),

            'getForgetCode' => array(
                'mobile' => array('name' => 'mobile', 'type' => 'string', 'min' => 1, 'require' => true, 'desc' => '手机号'),
            ),
        );
    }

    /**
     * 会员登陆 需要密码
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userLogin()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $user_login = $this->checkNull($this->user_login);
        $user_pass = $this->checkNull($this->user_pass);
        $sys = $this->checkNull($this->sys);

        $androidtoken = $this->checkNull($this->androidtoken);
        $iphonetoken = $this->checkNull($this->iphonetoken);

        $domain = new Domain_Login();
        $info = $domain->userLogin($user_login, $user_pass,$sys,$androidtoken,$iphonetoken);

        if ($info == 1001) {
            $rs['code'] = 1001;
            $rs['msg'] = '账号或密码错误';
            return $rs;
        } else if ($info == 1002) {
            $rs['code'] = 1002;
            $rs['msg'] = '您的账号已被封';
            return $rs;
        }

        $rs['info'] = $info;

        $this->LoginBonus($info['id'], $info['token']);

        return $rs;
    }

    /**
     * 会员注册
     * @desc 用于用户注册信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userReg()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $user_login = $this->checkNull($this->user_login);
        if (!$this->is_email($user_login)) {
            $rs['code'] = 1001;
            $rs['msg'] = '邮箱错误';
            return $rs;
        }
        $user_pass = $this->checkNull($this->user_pass);
//		$user_pass2=$this->checkNull($this->user_pass2);
        $code = $this->checkNull($this->code);

//		if($user_login!=$_SESSION['reg_mobile']){
//            $rs['code'] = 1001;
//            $rs['msg'] = '手机号码不一致';
//            return $rs;
//		}

//		if($code!=$_SESSION['reg_mobile_code']){
//            $rs['code'] = 1002;
//            $rs['msg'] = '验证码错误';
//            return $rs;
//		}

        $true_code = DI()->notorm->email_code->select()->where('email=?', $user_login)->order('id desc')->fetchOne();

        //有效期
        if (time() - $true_code['ctime'] > 10 * 60) {
            $rs['code'] = 1009;
            $rs['msg'] = '验证码已过期';
            return $rs;
        }
        if ($code != $true_code['code']) {
            $rs['code'] = 1002;
            $rs['msg'] = '验证码错误';
            return $rs;
        }


//		if($user_pass!=$user_pass2){
//            $rs['code'] = 1003;
//            $rs['msg'] = '两次输入的密码不一致';
//            return $rs;
//		}

        $check = $this->passcheck($user_pass);

        if ($check == 0) {
            $rs['code'] = 1004;
            $rs['msg'] = '密码6-12位数字与字母';
            return $rs;
        } else if ($check == 2) {
            $rs['code'] = 1005;
            $rs['msg'] = '密码不能纯数字或纯字母';
            return $rs;
        }
        $domain = new Domain_Login();
        $info = $domain->userReg($user_login, $user_pass);

        if ($info == 1006) {
            $rs['code'] = 1006;
//            $rs['msg'] = '该手机号已被注册！';
            $rs['msg'] = '该邮箱已被注册！';
            return $rs;
        }

        $rs['info'] = $info;

        $_SESSION['reg_mobile'] = '';
        $_SESSION['reg_mobile_code'] = '';
        $_SESSION['reg_mobile_expiretime'] = '';

        return $rs;
    }

    /**
     * 会员找回密码
     * @desc 用于会员找回密码
     * @return int code 操作码，0表示成功，1表示验证码错误，2表示用户密码不一致,3短信手机和登录手机不一致 4、用户不存在 801 密码6-12位数字与字母
     * @return array info
     * @return string msg 提示信息
     */
    public function userFindPass()
    {

        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $user_login = $this->checkNull($this->user_login);
        if (!$this->is_email($user_login)) {
            $rs['code'] = 1001;
            $rs['msg'] = '邮箱错误';
            return $rs;
        }
        $user_pass = $this->checkNull($this->user_pass);
//		$user_pass2=$this->checkNull($this->user_pass2);
        $code = $this->checkNull($this->code);


//	 	if($user_login!=$_SESSION['forget_mobile']){
//            $rs['code'] = 1001;
//            $rs['msg'] = '手机号码不一致';
//            return $rs;
//		}

        $true_code = DI()->notorm->email_code->select()->where('email=?', $user_login)->order('id desc')->fetchOne();
        //有效期
        if (time() - $true_code['ctime'] > 10 * 60) {
            $rs['code'] = 1009;
            $rs['msg'] = '验证码已过期';
            return $rs;
        }
        if ($code != $true_code['code']) {
            $rs['code'] = 1002;
            $rs['msg'] = '验证码错误';
            return $rs;
        }


//		if($user_pass!=$user_pass2){
//            $rs['code'] = 1003;
//            $rs['msg'] = '两次输入的密码不一致';
//            return $rs;
//		}

//		$check = $this->passcheck($user_pass);
//		if($check== 0 ){
//            $rs['code'] = 1004;
//            $rs['msg'] = '密码6-12位数字与字母';
//            return $rs;
//        }else if($check== 2){
//            $rs['code'] = 1005;
//            $rs['msg'] = '密码不能纯数字或纯字母';
//            return $rs;
//        }

        $domain = new Domain_Login();
        $info = $domain->userFindPass($user_login, $user_pass);

        if ($info == 1006) {
            $rs['code'] = 1006;
            $rs['msg'] = '该帐号不存在';
            return $rs;
        } else if ($info === false) {
            $rs['code'] = 1007;
            $rs['msg'] = '重置失败，请重试';
            return $rs;
        }

        $_SESSION['forget_mobile'] = '';
        $_SESSION['forget_mobile_code'] = '';
        $_SESSION['forget_mobile_expiretime'] = '';

        return $rs;
    }

    /**
     * 第三方登录
     * @desc 用于用户登陆信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userLoginByThird()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());
        $openid = $this->checkNull($this->openid);
        $type = $this->checkNull($this->type);
        $nicename = $this->checkNull($this->nicename);
        $avatar = $this->checkNull($this->avatar);
        $sex = $this->checkNull($this->sex);
        $sys = $this->checkNull($this->sys);
        $androidtoken = $this->checkNull($this->androidtoken);
        $iphonetoken = $this->checkNull($this->iphonetoken);

        $domain = new Domain_Login();
        $info = $domain->userLoginByThird($openid, $type, $nicename, $avatar, $sex,$sys,$androidtoken,$iphonetoken);

        if ($info == 1001) {
            $rs['code'] = 1001;
            $rs['msg'] = '您的账号已被封';
            return $rs;
        }

        $rs['info'] = $info;

        $this->LoginBonus($info['id'], $info['token']);

        return $rs;
    }

    /**
     * 会员注册
     * @desc 用于用户注册信息
     * @return int code 操作码，0表示成功
     * @return array info 用户信息
     * @return string info[0].id 用户ID
     * @return string info[0].user_nicename 昵称
     * @return string info[0].avatar 头像
     * @return string info[0].avatar_thumb 头像缩略图
     * @return string info[0].sex 性别
     * @return string info[0].signature 签名
     * @return string info[0].coin 用户余额
     * @return string info[0].login_type 注册类型
     * @return string info[0].level 等级
     * @return string info[0].province 省份
     * @return string info[0].city 城市
     * @return string info[0].birthday 生日
     * @return string info[0].token 用户Token
     * @return string msg 提示信息
     */
    public function userMobileReg()
    {

        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $user_login = $this->checkNull($this->user_login);
        $user_pass = $this->checkNull($this->user_pass);
        //$user_pass2=$this->checkNull($this->user_pass2);
        $code = $this->checkNull($this->code);

        /*if($user_login!=$_SESSION['reg_mobile']){
            $rs['code'] = 1001;
            $rs['msg'] = '手机号码不一致';
            return $rs;					
        }

        if($code!=$_SESSION['reg_mobile_code']){
            $rs['code'] = 1002;
            $rs['msg'] = '验证码错误';
            return $rs;
        }*/

        /*if($user_pass!=$user_pass2){
            $rs['code'] = 1003;
            $rs['msg'] = '两次输入的密码不一致';
            return $rs;					
        }*/

        $check = $this->passcheck($user_pass);

        if ($check == 0) {
            $rs['code'] = 1004;
            $rs['msg'] = '密码6-12位数字与字母';
            return $rs;
        } else if ($check == 2) {
            $rs['code'] = 1005;
            $rs['msg'] = '密码不能纯数字或纯字母';
            return $rs;
        }
        $domain = new Domain_Login();
        $info = $domain->userMobileReg($user_login, $user_pass);

        if ($info == 1006) {
            $rs['code'] = 1006;
            $rs['msg'] = '该手机号已被注册！';
            return $rs;
        }

        $rs['info'] = $info;

        $_SESSION['reg_mobile'] = '';
        $_SESSION['reg_mobile_code'] = '';
        $_SESSION['reg_mobile_expiretime'] = '';

        return $rs;
    }

    /**
     * 会员找回密码
     * @desc 用于会员找回密码
     * @return int code 操作码，0表示成功，1表示验证码错误，2表示用户密码不一致,3短信手机和登录手机不一致 4、用户不存在 801 密码6-12位数字与字母
     * @return array info
     * @return string msg 提示信息
     */
    public function userMobileFindPass()
    {

        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $user_login = $this->checkNull($this->user_login);
        $user_pass = $this->checkNull($this->user_pass);
        //$user_pass2=$this->checkNull($this->user_pass2);
        $code = $this->checkNull($this->code);


        /*if($user_login!=$_SESSION['forget_mobile']){
           $rs['code'] = 1001;
           $rs['msg'] = '手机号码不一致';
           return $rs;
       }

       if($code!=$_SESSION['forget_mobile_code']){
           $rs['code'] = 1002;
           $rs['msg'] = '验证码错误';
           return $rs;
       }


       if($user_pass!=$user_pass2){
           $rs['code'] = 1003;
           $rs['msg'] = '两次输入的密码不一致';
           return $rs;
       }*/

        $check = $this->passcheck($user_pass);
        if ($check == 0) {
            $rs['code'] = 1004;
            $rs['msg'] = '密码6-12位数字与字母';
            return $rs;
        } else if ($check == 2) {
            $rs['code'] = 1005;
            $rs['msg'] = '密码不能纯数字或纯字母';
            return $rs;
        }

        $domain = new Domain_Login();
        $info = $domain->userMobileFindPass($user_login, $user_pass);

        if ($info == 1006) {
            $rs['code'] = 1006;
            $rs['msg'] = '该帐号不存在';
            return $rs;
        } else if ($info === false) {
            $rs['code'] = 1007;
            $rs['msg'] = '重置失败，请重试';
            return $rs;
        }

        $_SESSION['forget_mobile'] = '';
        $_SESSION['forget_mobile_code'] = '';
        $_SESSION['forget_mobile_expiretime'] = '';

        return $rs;
    }

    /**
     * 获取注册短信验证码
     * @desc 用于注册获取短信验证码
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string msg 提示信息
     */

    public function getCode()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $mobile = $this->mobile;

        $ismobile = $this->checkMobile($mobile);
        if (!$ismobile) {
            $rs['code'] = 1001;
            $rs['msg'] = '请输入正确的手机号';
            return $rs;
        }

        if ($_SESSION['reg_mobile'] == $mobile && $_SESSION['reg_mobile_expiretime'] > time()) {
            $rs['code'] = 1002;
            $rs['msg'] = '验证码1分钟有效，请勿多次发送';
            return $rs;
        }

        $mobile_code = $this->random(6, 1);

        /* 发送验证码 */
        $result = $this->sendCode($mobile, $mobile_code);
        if ($result['code'] === 0) {
            $_SESSION['reg_mobile'] = $mobile;
            $_SESSION['reg_mobile_code'] = $mobile_code;
            $_SESSION['reg_mobile_expiretime'] = time() + 60 * 5;
        } else {
            $rs['code'] = 1002;
            $rs['msg'] = $result['msg'];
        }

        /*	$_SESSION['reg_mobile'] = $mobile;
            $_SESSION['reg_mobile_code'] = '123456';
            $_SESSION['reg_mobile_expiretime'] = time() +60*1;
            */
        return $rs;
    }

    /**
     * 获取找回密码短信验证码
     * @desc 用于找回密码获取短信验证码
     * @return int code 操作码，0表示成功,2发送失败
     * @return array info
     * @return string msg 提示信息
     */

    public function getForgetCode()
    {
        $rs = array('code' => 0, 'msg' => '', 'info' => array());

        $mobile = $this->mobile;

        $ismobile = $this->checkMobile($mobile);
        if (!$ismobile) {
            $rs['code'] = 1001;
            $rs['msg'] = '请输入正确的手机号';
            return $rs;
        }

        if ($_SESSION['forget_mobile'] == $mobile && $_SESSION['forget_mobile_expiretime'] > time()) {
            $rs['code'] = 1002;
            $rs['msg'] = '验证码1分钟有效，请勿多次发送';
            return $rs;
        }

        $mobile_code = $this->random(6, 1);

        /* 发送验证码 */
        $result = $this->sendCode($mobile, $mobile_code);
        if ($result['code'] === 0) {
            $_SESSION['forget_mobile'] = $mobile;
            $_SESSION['forget_mobile_code'] = $mobile_code;
            $_SESSION['forget_mobile_expiretime'] = time() + 60 * 5;
        } else {
            $rs['code'] = 1002;
            $rs['msg'] = $result['msg'];
        }

        /*  $_SESSION['forget_mobile'] = $mobile;
            $_SESSION['forget_mobile_code'] = '123456';
            $_SESSION['forget_mobile_expiretime'] = time() +60*1;
            */
        return $rs;
    }

}
