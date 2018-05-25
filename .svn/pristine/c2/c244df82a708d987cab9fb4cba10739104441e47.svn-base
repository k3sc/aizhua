<?php

class Domain_User {

	public function getBaseInfo($userId) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBaseInfo($userId);

			return $rs;
	}
	
	public function checkName($uid,$name) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->checkName($uid,$name);

			return $rs;
	}
	
	public function userUpdate($uid,$fields) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->userUpdate($uid,$fields);

			return $rs;
	}
	
	public function updatePass($uid,$oldpass,$pass) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->updatePass($uid,$oldpass,$pass);

			return $rs;
	}

	public function getBalance($uid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBalance($uid);

			return $rs;
	}
	
	public function getChargeRules() {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getChargeRules();

			return $rs;
	}
	
	public function getProfit($uid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getProfit($uid);

			return $rs;
	}

	public function setCash($uid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setCash($uid);

			return $rs;
	}
	
	public function setAttent($uid,$touid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setAttent($uid,$touid);

			return $rs;
	}
	
	public function setBlack($uid,$touid) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->setBlack($uid,$touid);

			return $rs;
	}
	
	public function getFollowsList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getFollowsList($uid,$touid,$p);

			return $rs;
	}
	
	public function getFansList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getFansList($uid,$touid,$p);

			return $rs;
	}

	public function getBlackList($uid,$touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getBlackList($uid,$touid,$p);

			return $rs;
	}

	public function getLiverecord($touid,$p) {
			$rs = array();

			$model = new Model_User();
			$rs = $model->getLiverecord($touid,$p);

			return $rs;
	}
	
	public function getUserHome($uid,$touid) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->getUserHome($uid,$touid);
		return $rs;
	}	
	
	public function getContributeList($touid,$p) {
		$rs = array();

		$model = new Model_User();
		$rs = $model->getContributeList($touid,$p);
		return $rs;
	}	
	
	public function getAliCdnRecord($id) {
        $rs = array();
                
        $model = new Model_Alicdnrecord();
        $rs = $model->getAliCdnRecord($id);

        return $rs;
    }	
	
	public function buyVip($uid,$giftcount) {
        $rs = array();
                
        $model = new Model_User();
        $rs = $model->buyVip($uid,$giftcount);

        return $rs;
    }	
}
