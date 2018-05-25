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

class PersonalController extends HomebaseController {
  /**个人中心-首页方法**/
	public function index() {
		$uid=session("uid");
		LogIn();
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$getgif=getgif(session("uid"));
   	$this->assign("getgif",$getgif[0]);
		//我的等级consumption
		$experience=M("users")->where("id='$uid'")->getField("consumption");//累计经验
		$level=getLevel($experience);//当前等级
		$level_up=$level['level_up']+1;
		$experlevel= M("experlevel")->field("level_up")->where("levelid=".$level_up)->find();
		$cha=$experlevel['level_up']-$experience; 
		$this->assign("experience",$experience);
		$this->assign("level",$level);
		$this->assign("cha",$cha);
    $this->display();
    }
	/**个人中心-头部修改昵称**/
	public function edit_name()
	{
		$User=M("users");
		$uid=session("uid");
		$name=urldecode($_GET["name"]);
		$userinfo= $User->where("id=$uid")->setField("user_nicename", $name);
		$_SESSION['user']['user_nicename']=  $name;
		if($userinfo)
		{
			echo '{"state":"0"}';
		}
		else
		{
			echo '{"state":"1"}';
		}
	}
	 /**
	个人中心-基本资料展示
	**/
	public function modify() 
	{
		LogIn();
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$this->assign("personal",'Set');
    $this->display();
    }
	 /**
	个人中心-基本资料修改
	**/
	public function edit_modify()
   {
	  $User=M("users");
	  $uid=session("uid");
	  $token=session("token");
		$checkToken=checkToken($uid,$token);
		if($checkToken==700)
		{
			echo '{"state":"0","msg":"登录失效,请重新登录"}';
			exit;
		}
		 $data=array(
			"id"=>$uid,
			"birthday"=>$_GET['birthday'],
			"user_nicename"=>urldecode($_GET['nickName']),
			"sex"=> $_GET['sex'],
			"signature"=>$_GET['signature']
		 );
		$result=$User->save($data);
		if($result)
		{
			$_SESSION['user']['user_nicename']= urldecode($_GET['nickName']);
			$_SESSION['user']['sex']= $_GET['sex'];
			$_SESSION['user']['signature']= $_GET['signature'];
			echo '{"state":"0","msg":"修改成功"}';
			exit;
		}
		else
		{
			 echo '{"state":"1","msg":"修改失败"}';
		}
   }
    /**
	个人中心-头像展示
	**/
	public function photo()
	{
		LogIn();
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$this->assign("personal",'Set');
		$this->display();
	}
	/**个人中心-修改头像**/
	public function edit_photo()
	{
		$user=M("users");
		$uid=session("uid");
		$token=session("token");
		$checkToken=checkToken($uid,$token);
		if($checkToken==700)
		{
			$callback = array(
				'error' => 0,
				'type'  => "登录失效,请重新登录"
				);
			echo json_encode($callback);
			exit;
		}
		$url=urldecode($_GET['avatar']);
		if (!empty($url)) {
			$avatar=  $url.'?imageView2/2/w/600/h/600'; //600 X 600
			$avatar_thumb=  $url.'?imageView2/2/w/200/h/200'; // 200 X 200
			$data=array(
					"id"=>$uid,
					"avatar"=>$avatar,
					"avatar_thumb"=>$avatar_thumb,
				);
			$result=$user->save($data); 
			$_SESSION['user']['avatar']=urldecode($data['avatar']);
			$_SESSION['user']['avatar_thumb']=urldecode($data['avatar_thumb']);
			if($result)
			{
				$callback = array(
				'error' => 1,
				'type'  => "头像修改成功"
				);
			}
			else{
				$callback = array(
				'error' => 0,
				'type'  => "头像修改失败"
				);
			}		
		}
		else
		{
			$callback = array(
				'error' => 0,
				'type'  => "图片处理失败"
			);
		}
		echo json_encode($callback);			
	}
	/**个人中心-我的认证**/
	public function card()
	{
		LogIn();
		$uid=session("uid");
		$this->assign("uid",$uid);
		$auth=auth($uid);
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$this->assign("auth",$auth);
		$this->assign("personal",'card');
		$this->display();
	}
	/**
	个人中心-我的认证-身份证上传
	$info判断上传状态
	**/
	function upload(){
		  $saveName=I('saveName')."_".time(); 
    	$config=array(
			    'replace' => true,
    			'rootPath' => './'.C("UPLOADPATH"),
    			'savePath' => './rz/',
    			'maxSize' => 0,//500K
    			'saveName'   =>    $saveName,
    			//'exts'       =>    array('jpg', 'png', 'jpeg'),
    			'autoSub'    =>    false,
    	);
    	$upload = new \Think\Upload($config);//
    	$info=$upload->upload();
     	//开始上传
    	if ($info) {
				//上传成功
				//写入附件数据库信息
    		$first=array_shift($info);

				$url=C("TMPL_PARSE_STRING.__UPLOAD__").'rz/'.$first['savename'];

        $url='http://'.$_SERVER['HTTP_HOST'].$url;
				
    		 echo json_encode(array("ret"=>200,'data'=>array("url"=>$url),'msg'=>$saveName));
    		//$this->ajaxReturn(sp_ajax_return(array("file"=>$file),"上传成功！",1),"AJAX_UPLOAD");
    	} else {
    		//上传失败，返回错误
    		//$this->ajaxReturn(sp_ajax_return(array(),$upload->getError(),0),"AJAX_UPLOAD");
				  echo json_encode(array("ret"=>0,'file'=>'','msg'=>$upload->getError()));
    	}	

	}
	/**
	个人中心-我的认证-认证信息写入数据库
	**/
	function authsave()
	{ 
		$data['uid']=session("uid");
		$data['real_name']=I("real_name");
		$data['mobile']=I("mobile");
		$data['card_no']=I("card_no");
		$data['bank_name']=I("bank_name");
		$data['accounts_province']=I("accounts_province");
		$data['accounts_city']=I("accounts_city");
		$data['sub_branch']=I("sub_branch");
		$data['cer_type']=I("cer_type");
		$data['cer_no']=I("cer_no");
		$data['front_view']=I("front_view");
		$data['back_view']=I("back_view");
		$data['handset_view']=I("handset_view");
		$data['status']=0;
		$data['addtime']=time();
		$authid=M("users_auth")->where("uid='{$data['uid']}'")->find();
		if($authid)
		{
			$result=M("users_auth")->where("id='{$authid}'")->save($data);
		}
		else
		{
			$result=M("users_auth")->add($data);
		}
	  if($result!==false)
		{		
			echo json_encode(array("ret"=>200,'data'=>array(),'msg'=>''));
		}
		else
		{		
			echo json_encode(array("ret"=>0,'data'=>array(),'msg'=>'提交失败，请重新提交'));
		}	   
	}	
	/**
	个人中心-我关注的
	**/
  public function follow()
	{
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$live=M("users_attention");
		$attention=$live->where("uid=$uid")->select();
		foreach($attention as $k=>$v)
		{
			$users=getUserInfo($v['touid']);
			$attention[$k]['users']=$users;
      $attention[$k]['follow']=getFollownums($v['touid']);
      $attention[$k]['fans']=getFansnums($v['touid']);
		}
		$this->assign("attention",$attention);
		$this->assign("personal",'follow');
		$this->display();
	}
	/**
	个人中心-我关注的-取消关注
	**/
	public function follow_dal()
	{
		$live=M("users_attention");
		$touid=$_GET['followID'];
		$uid=session("uid");
		$del_follow=$live->where("touid=$touid and uid=$uid")->delete();
		if($del_follow!==false)
		{
			echo '{"state":"0","msg":"取消关注"}';
		}
		else
		{
			echo '{"state":"1","msg":"取消失败"}';
		}
	}
	public function follow_add()
	{
		$touid=$_GET['touid'];
		$uid=session("uid");
		$data=array(
			"uid"=>$uid,
			"touid"=>$touid
		);
		$result=M("users_attention")->add($data);
		if($result!==false)
		{
			M('users_black')->where("touid=$touid and uid=$uid")->delete();
			echo '{"state":"0","msg":"关注成功"}';
		}
		else
		{
			echo '{"state":"1","msg":"关注失败"}';
		}
	}
	/**
	个人中心-我的粉丝
	**/
	public function fans()
	{
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$live=M("users_attention");
		$attention=$live->where("touid=$uid")->select();
		foreach($attention as $k=>$v)
		{
			$users=getUserInfo($v['uid']);
			$attention[$k]['users']=$users;
      $attention[$k]['follow']=getFollownums($v['uid']);
      $attention[$k]['fans']=getFansnums($v['uid']);
			$isAttention=isAttention($uid,$v['uid']);
			$attention[$k]['attention']=$isAttention;
			
		}
		$this->assign("attention",$attention);
		$this->assign("personal",'follow');
		$this->display();
	}
	/*黑名单*/
	public function namelist()
	{
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$live=M("users_black");
		$attention=$live->where("uid=$uid")->select();
		foreach($attention as $k=>$v)
		{
			$users=getUserInfo($v['touid']);
			$attention[$k]['users']=$users;
      $attention[$k]['follow']=getFollownums($v['touid']);
      $attention[$k]['fans']=getFansnums($v['touid']);
			$isAttention=isAttention($uid,$v['touid']);
			$attention[$k]['attention']=$isAttention;
		}
		$this->assign("attention",$attention);
		$this->assign("personal",'follow');
		$this->display();
	}
	/*删除黑名单*/
	public function list_del()
	{
		$uid=session("uid");
		$touid=$_GET['touid'];
		$isBlack=isBlack($uid,$touid);
		if($isBlack==0)
		{
			echo '{"state":"1000","msg":"该用户不在你的黑名单内"}';
			exit;
		}
		else
		{
			$attention=M('users_black')->where("touid=$touid and uid=$uid")->delete();
			if($attention)
			{
				echo '{"state":"0","msg":"移除成功"}';
				exit;
			}
			else
			{
				echo '{"state":"1001","msg":"移除失败"}';
				exit;
			}
		}
	}
	/*拉黑操作 如果我已经关注这个主播 同时会删除关注状态但是不会清除粉丝*/
	public function blacklist()
	{
		$uid=session("uid");
		$touid=$_GET['touid'];
		$isBlack=isBlack($uid,$touid);
		if($isBlack==1)
		{
			echo '{"state":"1000","msg":"你已经将该用户拉黑"}';
			exit;
		}
		else
		{
			$isAttention=isAttention($uid,$touid);
			if($isAttention)
			{
				M('users_attention')->where("touid=$touid and uid=$uid")->delete();
			}
			$data=array(
				"uid"=>session("uid"),
				"touid"=>$_GET['touid']
			);
			$result=M('users_black')->add($data);
			if($result)
			{
				echo '{"state":"0","msg":"拉黑成功"}';
				exit;
			}
			else
			{
				echo '{"state":"1001","msg":"拉黑失败"}';
				exit;
			}
		}	
	}
	/**
	个人中心-管理员管理中心
	**/
	public function admin()
	{
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$live=M("users_livemanager");
		$admin=$live->where("liveuid=$uid")->select();
		foreach($admin as $k=>$v)
		{
			$users=getUserInfo($v['uid']);
			$admin[$k]['users']=$users;
      $admin[$k]['follow']=getFollownums($v['uid']);
      $admin[$k]['fans']=getFansnums($v['uid']);
			$isAttention=isAttention($uid,$v['uid']);
			$admin[$k]['attention']=$isAttention;
		}
		$this->assign("admin",$admin);
		$this->assign("personal",'follow');
		$this->display();
	}
	/**
	个人中心-管理员管理中心-取消管理员
	users_livemanager管理员记录表
	**/
	function admin_del()
	{ 
		$uid=session("uid");
		$touid=$_GET['touid'];
    if($touid) 
		{
    	$rst = M("users_livemanager")->where("uid=".$touid." and liveuid=".$uid)->delete();
    	if ($rst) 
			{
    		echo '{"state":"0","msg":"管理取消成功"}';
				exit;
    	} 
			else
			{
    		echo '{"state":"1000","msg":"管理取消失败"}';
				exit;
    	}
    } 
		else 
		{
    		echo '{"state":"1001","msg":"数据传入失败"}';
				exit;
    }
  }
	/**
	个人中心-提现中心
	
	**/
	public function exchange()
	{
		LogIn();
		$uid=session("uid");
		$info=getUserPrivateInfo($uid);	
		$level=getLevel($info['consumption']);		
		//等级限制金额
		$limitcash=getLevelSection($level);
		$config=getConfigPri();
		//提现比例
		$cash_rate=$config['cash_rate'];
		//剩余票数
		$votes=$info['votes'];
			
		//总可提现数
		$total=floor($votes/$cash_rate);
		$nowtime=time();
		//当天0点
		$today=date("Ymd",$nowtime);
		$today_start=strtotime($today)-1;
		//当天 23:59:59
		$today_end=strtotime("{$today} + 1 day");
				//已提现
		$users_cashrecord=M("users_cashrecord")->query('select uid,sum(money) as hascash from cmf_users_cashrecord where uid='.$uid.' and addtime>'.$today_start.' and addtime<'.$today_end.' and status!=2');
		
		$hascash=$users_cashrecord[0]['hascash'];
		
		if(!$hascash){
			$hascash=0;
		}		
		//今天可体现等级提现区间 - 今日提过的
		$todaycancash=(string)$limitcash - $hascash;
	
		//今天能提
		if($todaycancash<$total){
			$todaycash=$todaycancash;
		}else{
			$todaycash=$total;
		}
		
		$rs=array(
			"votes"=>$votes,
			"todaycash"=>$todaycash,
			"total"=>$total,
		);
	 	$this->assign("info",$info);
	 	$this->assign("rs",$rs);
		$this->assign('exchange_rate',$exchange_rate);
		$this->assign("personal",'card');
		$this->display();
	}
	/**
	个人中心-提现中心开始提现
	**/
	public function edit_exchange()
	{
		$uid=session("uid");
		$token=session("token");
		$checkToken=checkToken($uid,$token);
		if($checkToken==700)
		{
			echo '{"state":"1003","msg":"登录失效，请重新登录"}';
			exit;
		}
		$isrz=M("users_auth")->field("status")->where("uid=".$uid)->find();
		if(!$isrz || $isrz['status']!=1){
			echo '{"state":"1003","msg":"请先进行身份认证"}';
			exit;
		}
		$info=getUserPrivateInfo($uid);	
		$level=getLevel($info['consumption']);		
		//等级限制金额
		$limitcash=getLevelSection($level);	
		$config=getConfigPri();
		
		//提现比例
		$cash_rate=$config['cash_rate'];
		//剩余票数
		$votes=$info['votes'];
		//总可提现数
		$total=floor($votes/$cash_rate);
		
		//已提现
		$nowtime=time();
		//当天0点
		$today=date("Ymd",$nowtime);
		$today_start=strtotime($today)-1;
		//当天 23:59:59
		$today_end=strtotime("{$today} + 1 day");
		$users_cashrecord=M("users_cashrecord")->query('select uid,sum(money) as hascash from cmf_users_cashrecord where uid='.$uid.' and addtime>'.$today_start.' and addtime<'.$today_end.' and status!=2');
		$hascash=$users_cashrecord[0]['hascash'];
		if(!$hascash){
			$hascash=0;
		}		
		//今天可体现
		$todaycancash=$limitcash - $hascash;
		
		//今天能提
		if($todaycancash<$total){
			$todaycash=$todaycancash;
		}else{
			$todaycash=$total;
		}
		
		if($todaycash<=0){
			echo '{"state":"1001","msg":"今日提现已达上限"}';
			exit;
		}
		
		$cashvotes=$todaycash*$cash_rate;
		
		$nowtime=time();
		
		$data=array(
			"uid"=>$uid,
			"money"=>$todaycash,
			"votes"=>$cashvotes,
			"orderno"=>$uid.'_'.$nowtime.rand(100,999),
			"status"=>0,
			"addtime"=>$nowtime,
			"uptime"=>$nowtime,
		);
		$rs=M("users_cashrecord")->add($data);
		if($rs){
			M()->execute("update __PREFIX__users set votes=votes-{$cashvotes},consumption=consumption+{$total} where id='{$uid}'");
			echo '{"state":"0","msg":"提现成功"}';
			exit;
		}else{
			echo '{"state":"1002","msg":"提现失败，请重试"}';
			exit;
		}				
	}
	/*修改密码*/
	public function updatepass(){
		LogIn();
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
		$this->assign("personal",'Set');
		$this->display();
	}
	/* 执行密码修改 */
	public function savepass() {
		$uid=session("uid");
		//旧密码
		$oldpass = I('oldpass');
		//新密码
		$newpass = I('newpass');
		//确认密码
		$repass = I('repass');
		$rs=array();
    if($newpass !== $repass)
		{
      $rs['code'] = 800;
      $rs['msg'] = '两次密码不一致';
			echo json_encode($rs);
			exit;
		}
		$authcode='rCt52pF2cnnKNB3Hkp';
		$oldpass = "###".md5(md5($authcode.$oldpass));
		$pwd = "###".md5(md5($authcode.$newpass));
		$check =$this->passcheck($newpass); 
		if($check==0)
		{
			$rs['code'] = 1001;
      $rs['msg'] = '密码6-12位数字与字母';
			echo json_encode($rs);
			exit;			
    }	
		if($check==2)
		{
			$rs['code'] = 1002;
      $rs['msg'] = '密码6-12位数字与字母';
			echo json_encode($rs);
			exit;			
    }			
		/* 密码判定 */
		$rt=M("users")->where("id='$uid' and user_pass='$oldpass' and user_type='2'")->find();
		if(empty($rt)){
			$rs['code'] = 103;
      $rs['msg'] = '旧密码错误';
			echo json_encode($rs);
			exit;	
		}
		$data=array();
		$User = M("users"); 
		//要修改的数据对象属性赋值
		$data['user_pass'] = $pwd;
		$map['id'] =$uid;
		//保存昵称到数据库
		$result=$User->where($map)->save($data);
		if($result!==false){
			$rs['code'] = 0;
			$rs['msg'] = '修改成功';
			echo json_encode($rs);
			exit;
		}else{
			$rs['code'] = 0;
			$rs['msg'] = '修改失败';
			echo json_encode($rs);
			exit;
		}
		
		
  }
	/**
	个人中心-直播记录
	**/
	public function live()
	{
		$uid=session("uid");
		LogIn();
	 	$where=array();
		$where['uid']=$uid;
		if($_REQUEST['start_time']!='')
		{
			$where['starttime']=array("gt",strtotime($_REQUEST['start_time']));
			$_GET['start_time']=$_REQUEST['start_time'];
		}
		if($_REQUEST['end_time']!='')
		{ 
			$where['starttime']=array("lt",strtotime($_REQUEST['end_time']));
			$_GET['end_time']=$_REQUEST['end_time'];
		}
		if($_REQUEST['start_time']!=''&& $_REQUEST['end_time']!='' )
		{	 
			$where['starttime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
			$_GET['start_time']=$_REQUEST['start_time'];
			$_GET['end_time']=$_REQUEST['end_time'];
		} 
		$this->assign('formget', $_GET);
		$info=getUserPrivateInfo(session("uid"));	
		$this->assign("info",$info);
	  $pagesize = 20; 
		$User = M('users_liverecord');
		$Live = M('users');
		$coin=$Live->where("id=$uid")->getField("coin");
		$this->assign('coin',$coin);
		$count= $User->where($where)->count();
		$Page= new \Page2($count,$pagesize);
		$show= $Page->show();
		$lists = $User->field($this->field)->where($where)->order("showid desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('lists',$lists);
		$this->assign('page',$show);
		$this->assign('uid',$uid);
		$this->assign("personal",'follow');
		$this->display();
	}
  /* 密码检查 */
  public function passcheck($user_pass) 
	{
		$num = ereg("^[a-zA-Z]{6,12}$",$user_pass);
		$word = ereg("^[0-9]{6,12}$",$user_pass);
		$check = ereg("^[a-zA-Z0-9]{6,12}$",$user_pass);
		if($num || $word )
		{
			return 2;
		}
		if(!$check)
		{
			return 0;
		}		
		return 1;

  }		
}


