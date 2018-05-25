<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Home\Controller;
use Common\Controller\HomebaseController; 
/**
 * 首页
 */
class IndexController extends HomebaseController {
	
	public $field='id,user_nicename,avatar,sex,signature,experience,consumption,votestotal,province,city,isrecommend,islive';
    //首页
	public function index() {
		  $this->assign("current",'index');	
		  $uid=session("uid");
		  /* 轮播 */
			$slide=M("slide")->where("slide_status='1' and slide_cid='1'")->order("listorder asc")->select();
			$this->assign("slide",$slide);	
			
			/* 右侧广告 */
			$ads=M("ads")->where("sid='1'")->order("orderno asc")->limit(7)->select();
		  $this->assign('ads',$ads);
       
			 /* 推荐 */
			$recommend=M("users")->field($this->field)->where("islive='1'")->order("isrecommend desc")->limit(5)->select();
			foreach($recommend as $k=>$v){
				$liveinfo=M("users_liverecord")->where("showid ='{$v['showid']}'")->find();
				$recommend[$k]['liveinfo']=$liveinfo;
			}
	
			$this->assign("recommend",$recommend);			 
			 
			 /* 热门 */
			$hot=M("users_liverecord")->where("islive='1'")->order("light desc")->limit(10)->select();
			foreach($hot as $k=>$v){
				$hot[$k]['userinfo']=getUserInfo($v['uid']);
			}
	
			$this->assign("hot",$hot);			 
			 
			 
			 /* 正在直播 */
			 
			$live=M("users_liverecord")->where("islive='1'")->order("showid desc")->limit(15)->select();
				foreach($live as $k=>$v){
				$live[$k]['userinfo']=getUserInfo($v['uid']);
			}
			$this->assign("live",$live);
			
			/* 主播排行榜 */
	
	    $anchorlist=M("users_liverecord")->field("uid,light,nums")->order("light desc")->group("uid")->limit(10)->select();
			foreach($anchorlist as $k=>$v){
				$anchorlist[$k]['userinfo']=getUserInfo($v['uid']);
				
				/* 判断 当前用户是否关注 */
				if($uid>0){
					 $isAttention=isAttention($uid,$v['uid']);
					 $anchorlist[$k]['isAttention']=$isAttention;
				}else{
					
					$anchorlist[$k]['isAttention']=0;
				}
				
			}
			$this->assign("anchorlist",$anchorlist);
    	$this->display();
    }	


}


