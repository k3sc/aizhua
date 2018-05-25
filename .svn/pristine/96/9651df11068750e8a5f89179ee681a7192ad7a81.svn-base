<?php

class Domain_Login {

    public function userLogin($user_login,$user_pass,$sys,$androidtoken,$iphonetoken) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->userLogin($user_login,$user_pass,$sys,$androidtoken,$iphonetoken);

        return $rs;
    }

    public function userReg($user_login,$user_pass) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userReg($user_login,$user_pass);

        return $rs;
    }	
	
    public function userFindPass($user_login,$user_pass) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userFindPass($user_login,$user_pass);

        return $rs;
    }	

    public function userLoginByThird($openid,$type,$nickname,$avatar,$sex,$sys,$androidtoken,$iphonetoken) {
        $rs = array();

        $model = new Model_Login();
        $rs = $model->userLoginByThird($openid,$type,$nickname,$avatar,$sex,$sys,$androidtoken,$iphonetoken);

        return $rs;
    }			
    public function userMobileReg($user_login,$user_pass) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userMobileReg($user_login,$user_pass);

        return $rs;
    }	

    public function userMobileFindPass($user_login,$user_pass) {
        $rs = array();
        $model = new Model_Login();
        $rs = $model->userMobileFindPass($user_login,$user_pass);

        return $rs;
    }

}
