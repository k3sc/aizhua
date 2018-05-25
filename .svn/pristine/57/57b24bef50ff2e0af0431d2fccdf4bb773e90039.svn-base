<?php
/**
 * 会员等级
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class LevelController extends HomebaseController {
	
	function index(){       
		$uid=I("uid");
		$Experlevel=M("experlevel");  
		$experience=M("users")->where("id='$uid'")->getField("consumption");
		$level=$Experlevel->where("level_up>='$experience'")->order("levelid asc")->find();
		$level2=$Experlevel->where()->order("levelid desc")->find();
		$cha=$level['level_up']+1-$experience;
		if($level)
		{
			$baifen=($experience/($cha+$experience))*100;
			$type="1";
		}else{
			$baifen=0;
			$type="0";
			$level=$level2;
		}
		$info=M("users")->where("id=".$uid)->find();
		$this->assign("experience",$experience);
		$this->assign("baifen",$baifen);
		$this->assign("level",$level);
		$this->assign("info",$info);
		$this->assign("cha",$cha);
		$this->assign("type",$type);
		$this->display();
	    
	}


}