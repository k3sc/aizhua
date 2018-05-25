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
	
    //首页
	public function index() {
		die();
		$prefix= C("DB_PREFIX");
		$this->assign("current",'index');	
		$uid=session("uid");
		/* 轮播 */
		$slide=M("slide")->where("slide_status='1' and slide_cid='1'")->order("listorder asc")->select();
		$this->assign("slide",$slide);	
		/* 右侧广告 */
		$ads=M("ads")->where("sid='1'")->order("orderno asc")->limit(7)->select();
		$this->assign('ads',$ads);
		$redis =connectionRedis();	
		/* 推荐 */
		$recommend=M("users_live l")
					->field("l.user_nicename,l.avatar,l.thumb,l.uid,l.stream")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where("l.islive='1' and u.isrecommend='1'")
					->order("u.ishot desc,u.votestotal desc")
					->limit(5)
					->select();
		foreach($recommend as $k=>$v){
	 		if($v['thumb']=="")
			{
				$recommend[$k]['thumb']=$v['avatar'];
			} 
			$nums=$redis->hget("livenums",$v['stream']);
			if(!$nums || $nums<0){
				$nums=0;
			}
			$recommend[$k]['nums']=$nums;
		}
		$redis->close();
		$this->assign("recommend",$recommend);			 
			 
		/* 热门 */
		$hot=M("users_live l")
					->field("l.user_nicename,l.avatar,l.uid,l.thumb,l.stream,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where("l.islive='1' and u.ishot='1'")
					->order("u.isrecommend desc,l.starttime desc")
					->limit(10)
					->select();
		 foreach($hot as $k=>$vi){
			if($vi['thumb']=="")
			{
				$hot[$k]['thumb']=$vi['avatar'];
			}
		} 
		$this->assign("hot",$hot);			 
		/* 正在直播 */ 
		$live=M("users_live")->field("uid,avatar,user_nicename,thumb,stream,title,city,islive")->where("islive='1'")->order("showid desc")->limit(15)->select();
		foreach($live as $k=>$vo){
			if($vo['thumb']=="")
			{
				$live[$k]['thumb']=$vo['avatar'];
			}
		} 
		$this->assign("live",$live);
		/* 主播排行榜 */
	  $anchorlist=M("users_liverecord")->field("uid,sum(nums) as light")->order("light desc")->group("uid")->limit(10)->select();
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
	
	public function ceshi(){
		$this->display();
	}
	public function translate()
	{
		$prefix= C("DB_PREFIX");	
		
		if($_REQUEST['keyword']!='')
		{
			$where="user_type='2'";
			$keyword=$_REQUEST['keyword'];
			$where.=" and (id='{$keyword}' OR user_nicename like '%{$keyword}%')";
			$_GET['keyword']=$_REQUEST['keyword'];
		}
		else
		{
			$where="u.user_type='2' and l.islive='1' ";
		}
		$auth=M("users");
		$pagesize = 18; 
		if($_REQUEST['keyword']=="")
		{
			$count= M("users_live l")
					->field("l.user_nicename,l.avatar,l.uid,l.stream,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where($where)
					->order("l.starttime desc")
					->count();
			$Page= new \Page2($count,$pagesize);
			$show= $Page->show();
			$lists=M("users_live l")
					->field("l.user_nicename,l.avatar,l.uid,l.stream,l.title,l.city,l.islive")
					->join("left join {$prefix}users u on u.id=l.uid")
					->where($where)
					->order("l.starttime desc")
					->limit($Page->firstRow.','.$Page->listRows)
					->select();
			$msg["info"]='抱歉,没有找到关于"';
			$msg["name"]='';
			$msg["result"]='"的搜索结果';
			$msg["type"]='0';
		}else{
			$count= $auth->where($where)->count();
			$Page= new \Page2($count,$pagesize);
			$show= $Page->show();
			$lists=$auth->where($where)->order("consumption desc")->limit($Page->firstRow.','.$Page->listRows)->select();
			$msg["info"]='共找到'.$count.'个关于"';
			$msg["name"]=$_REQUEST['keyword'];
			$msg["result"]='"的搜索结果';
			$msg["type"]='1';
		}
		$this->assign('lists',$lists);
		$this->assign('msg',$msg);
		$this->assign('page',$show);
		$this->assign('formget', $_GET);
		$this->display();
	}	
	
	public function test(){
		$configpri=getConfigPri();
		$this->assign('configpri',$configpri);
		
		$showid='19967';
		$info=array();
		$sign= mt_rand(1000,9999);
		$info['id'] = '-'.$sign;
		$info['user_nicename'] = '游客'.$sign;
		$info['avatar'] = '';
		$info['avatar_thumb'] = '';
		$info['sex'] = '0';
		$info['signature'] = '0';
		$info['consumption'] = '0';
		$info['votestotal'] = '0';
		$info['province'] = '';
		$info['city'] = '';
		$info['level'] = '0';
		$info['sign'] = md5($showid.'_'.$sign);
		$info['token']=$info['sign'];
		$info['liveuid']=$showid;
		$info['userType']=0;
		$token =$info['sign'];
		
		$redis = connectionRedis();
		$redis  -> set($token,json_encode($info));
		$redis -> close();	
		
		$this->assign('info',$info);
		$this->display();
	}

}


