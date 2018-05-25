<?php
/**
 * 贡献榜
 */
namespace Appapi\Controller;
use Common\Controller\HomebaseController;
class GiftcardController extends HomebaseController {
	
	function index(){
		$list=M("users_coinrecord")->field("touid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage')")->group("touid")->order("total desc")->limit(20)->select();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['touid']);
		}
		$this->assign("list",$list);
		$this->display();		
	}
	/*
	public function order(){
		$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage')")->group("uid")->order("total desc")->limit(20)->select();
		foreach($list as $k=>$v){
			$list[$k]['userinfo']=getUserInfo($v['uid']);
		}
		$this->assign("list",$list);
		$this->display();			
	}
	*/
	public function order(){
		$type=I("type");
		
		if($type=='day'){
			
			$nowtime=time();
			//当天0点
			$today=date("Ymd",$nowtime);
			$today_start=strtotime($today);
			//当天 23:59:59
			$today_end=strtotime("{$today} + 1 day")-1;
			
			$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and addtime>{$today_start} and addtime<{$today_end}")->group("uid")->order("total desc")->limit(0,20)->select();
			
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}elseif($type=='week'){
			
			$nowtime=time();

			$w=date('w',$nowtime); 
			//获取本周开始日期，如果$w是0，则表示周日，减去 6 天 
			$first=1;
			//周一
			$week=date('Y-m-d H:i:s',strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days')); 
			$week_start=strtotime( date("Ymd")."-".($w ? $w - $first : 6).' days'); 

			//本周结束日期 
			//周天
			$week_end=strtotime("{$week} +1 week")-1;			
			
			$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and addtime>{$week_start} and addtime<{$week_end}")->group("uid")->order("total desc")->limit(0,20)->select();
			
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}elseif($type=='month'){
			
			$nowtime=time();
			
			$month_start = strtotime(date('Y-m-1', $nowtime));
			$month_end = $nowtime;
			
			$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage') and addtime>{$month_start} and addtime<{$month_end}")->group("uid")->order("total desc")->limit(0,20)->select();
			
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}else{
			$list=M("users_coinrecord")->field("uid,sum(totalcoin) as total")->where(" action in ('sendgift','sendbarrage')")->group("uid")->order("total desc")->limit(0,20)->select();
			foreach($list as $k=>$v){
				$list[$k]['userinfo']=getUserInfo($v['uid']);
			}
		}

		$this->assign("list",$list);
		
		
		
		
		


		$this->display();			
		
		
	}
}