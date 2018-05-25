<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GuildController extends AdminbaseController{
    protected $guild_model;
    protected $guild_member_model;
    
    function _initialize() {
        parent::_initialize();
        $this->guild_model = D("Common/Guild");
        $this->guild_member_model = D("Common/GuildMember");
    }

	function get_admin_role_id($uid) {
		$admin = M("RoleUser")->where(array("user_id"=>$uid))->find();
		if ($admin) {
			return $admin['role_id'];
		}
			
		return 0;
	}

    function index(){

        $is_limit = 0;
        $admin_id = $_SESSION['ADMIN_ID'];
		$role_id = $this->get_admin_role_id($admin_id);
        if ($role_id != 1) {
            $is_limit = 1;
        }

        $map = array();
        if ($_REQUEST['keyword'] != ''){
            $map['name'] = array("like","%".$_REQUEST['keyword']."%");
            $_GET['keyword']=$_REQUEST['keyword']; 
        }
		if ($is_limit) {
			$map['manager_id'] = $admin_id;
		}

        $count = $this->guild_model->get_guilds_count($map);
        $page = $this->page($count, 20);
        $guilds = $this->guild_model->list_guilds($map, $page->firstRow, $page->listRows);

        foreach($guilds as $k=>$v){
            $user_name = M("users")->field("user_login")->where("id='{$v['manager_id']}'")->find();
            $guilds[$k]['manager_name'] = $user_name['user_login'];
            $guilds[$k]['member_count'] = $this->guild_member_model->get_member_count($v['id']);
            $total_votes = $this->guild_member_model->get_all_members_total_votes($v['id']);
            $guild_percent = 100 - $v['anchor_percent'] - $v['manager_percent'];
            $guilds[$k]['manager_cash'] = round($total_votes * $v['manager_percent'] / 100) / 100.0; 
        }


        $this->assign("is_limit", $is_limit);
        $this->assign("page", $page->show('Admin'));
        $this->assign("guilds", $guilds);
        $this->assign('formget', $_GET);
        $this->display();
    }
    
    
    function add() {
        $this->display();
    }
    
    function add_post(){
        if(IS_POST){
			$admin_id = $_SESSION['ADMIN_ID'];
			$role_id = $this->get_admin_role_id($admin_id);
			if ($role_id != 1) {
				return $this->error('user is limit to create guild!');
			}

			$manager_name = I('post.manager_name');
			$user = M("users")->field(array('id', 'user_type'))->where("user_login='{$manager_name}'")->find();
			if (empty($user)) {
                return $this->error('会长用户名不存在!');
			}

			$guild = $this->guild_model->get_guild_by_manager_id($user['id']);
			if ($guild) {
				return $this->error('user is another guild admin');
			}

            /*
            if ($user['user_type'] != 1) {
                $this->error('没有添加工会权限');
            }*/

			$anchor_percent = intval(I('post.anchor_percent'));
			$manager_percent = intval(I('post.manager_percent'));
			if ($anchor_percent == 0 || $anchor_percent >= 100) {
				return $this->error('主播分成比例不合法!');
			}

			if ($manager_percent == 0 || $manager_percent >= 100) {
				return $this->error('会长分成比例不合法!');
			}

			if ($manager_percent + $anchor_percent >= 100) {
				return $this->error('分成比例不合法!');
			}

            $data = array();
            $data['name'] = I('post.name');
	   		$data['create_aid'] = $_SESSION['ADMIN_ID'];
            $data['manager_id'] = $user['id'];
            $data['anchor_percent'] = I('post.anchor_percent');
            $data['manager_percent'] = I('post.manager_percent');
            $data['desc'] = I('post.desc');
            $result = $this->guild_model->add_guild($data);
            if ($result !==  false) {
                $this->success('添加成功!', U('Guild/index'));
            } else {
                $this->error($this->guild_model->getError());
            }
        }
    }

    /**
     *  删除
     */
    function del(){
        $admin_id = intval($_SESSION['ADMIN_ID']);
		$user = M("users")->field(array('id', 'user_type'))->where("id='{$admin_id}'")->find();
		if ($user && $user['user_type'] != 1) {
			return $this->error('非超级管理员不能删除!');
		}

		$id = intval(I('get.id'));
        
        if ($this->guild_model->delete_guild($id) !== false) {
			$this->guild_member_model->del_all_member($id);
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

	function list_member() {
        $user_map = array();
        if ($_REQUEST['keyword']!=''){
            $user_map['user_login|user_nicename']=array("like","%".$_REQUEST['keyword']."%"); 
            $_GET['keyword']=$_REQUEST['keyword'];

        }

        $uid_list = array();
        if (!empty($user_map)) {
            $user_list = M("users")->field(array('id'))->where($user_map)->select();
            if ($user_list) {
                foreach ($user_list as $key => $value) {
                    array_push($uid_list, $value['id']);
                }
            }
        }
        
        $map = array();
        if (!empty($uid_list)) {
            $map['uid'] = array("in", $uid_list);
        }

		$id = intval(I('get.id'));
        $count=$this->guild_member_model->get_member_count($id, $map);

        $page = $this->page($count, 20);
        $members = $this->guild_member_model->list_member($id, $map, $page->firstRow, $page->listRows);

        $guild = $this->guild_model->get_guild($id);
        if (empty($guild)) {
            return $this->error('工会不存在');
        }

		$anchor_percent = intval($guild['anchor_percent']);
		$manager_percent = intval($guild['manager_percent']);

        $member_uid_list = array();
        foreach($members as $k=>$v){
            $user_name = M("users")->field(array("user_login", "user_nicename", "votes"))->where("id='{$v['uid']}'")->find();
            $members[$k]['user_name'] = $user_name['user_login'];
            $members[$k]['user_nickname'] = $user_name['user_nicename'];

            $members[$k]['votes'] = $user_name['votes'];
            //$members[$k]['anchor_cash'] = round($user_name['votes'] * $anchor_percent / 100) / 100.0;
            //$members[$k]['manager_cash'] = round($user_name['votes'] * $manager_percent / 100) / 100.0;
            array_push($member_uid_list, $v['uid']);
        }

        $live_record_map = array();
        if($_REQUEST['start_time'] != ''){
            $live_record_map['starttime']=array("gt", strtotime($_REQUEST['start_time']));
            $_GET['start_time']=$_REQUEST['start_time'];
        }
                 
        if ($_REQUEST['end_time'] != ''){
            $live_record_map['starttime']=array("lt",strtotime($_REQUEST['end_time']));
            $_GET['end_time']=$_REQUEST['end_time'];
        }
        if($_REQUEST['start_time']!='' && $_REQUEST['end_time']!='' ){
                         
            $live_record_map['starttime']=array("between",array(strtotime($_REQUEST['start_time']),strtotime($_REQUEST['end_time'])));
            $_GET['start_time']=$_REQUEST['start_time'];
            $_GET['end_time']=$_REQUEST['end_time'];
        }
        if (!empty($member_uid_list)) {
            $live_record_map['uid'] = array("in", $member_uid_list);
        }

        $member_live_records = array();
        $live_records = M("users_liverecord")->where($live_record_map)->select();
        foreach ($live_records as $key => $value) {
            $record_time = $value['endtime'] - $value['starttime'];
            $member_live_records[$value['uid']]['votes'] += $value['votes'];
            $member_live_records[$value['uid']]['record_time'] += $record_time;
        }

        foreach($members as $k=>$v){
            $members[$k]['record_votes'] = intval($member_live_records[$v['uid']]['votes']);
            $members[$k]['record_time'] = gmstrftime('%H:%M:%S', $member_live_records[$v['uid']]['record_time']);
            //$members[$k]['anchor_cash'] = round($members[$k]['votes'] * $anchor_percent / 100) / 100.0;
            //$members[$k]['manager_cash'] = round($members[$k]['votes'] * $manager_percent / 100) / 100.0;
            $members[$k]['anchor_cash'] = round($members[$k]['record_votes'] * $anchor_percent / 100) / 100.0;
            $members[$k]['manager_cash'] = round($members[$k]['record_votes'] * $manager_percent / 100) / 100.0;
        }
	
        $is_limit = 0;
        $admin_id = $_SESSION['ADMIN_ID'];
		$role_id = $this->get_admin_role_id($admin_id);
        if ($role_id != 1) {
            $is_limit = 1;
        }

        $this->assign("is_limit", $is_limit);
            
        $this->assign("gid", $id);
        $this->assign("page", $page->show('Admin'));
        $this->assign("members", $members);
        $this->assign('formget', $_GET);
        $this->display();
	}
    
    function add_guild_member() {
		$gid = intval(I('get.gid'));
		$this->assign("gid", $gid);
        $this->display();
    }

    function add_guild_member_post(){
        if(IS_POST){
			$user_name = I('post.name');
			$user = M("users")->field(array('id', 'user_type', 'user_status'))->where("user_login='{$user_name}'")->find();
			if (empty($user)) {
                return $this->error('用户名不存在!');
			}
			
			/*
			if ($user['user_status'] == 0) {
				return $this->error('the user is not a valid anchor!');
			}*/
			
			$gid = intval(I('post.gid'));

            //判断该主播是否已加入其他工会
            $guild_member = $this->guild_member_model->get_guild_member_by_uid($user['id']);
            if (!empty($guild_member)) {
                return $this->error('该主播已加入其他工会!');
            }

            $data = array();
            $data['uid'] = $user['id'];
            $data['gid'] = $gid;
            $result = $this->guild_member_model->add_guild_member($data);
            if ($result !==  false) {
                $this->success('添加成功!', U('Guild/list_member', array('id'=>$gid)));
            } else {
                $this->error($this->guild_member_model->getError());
            }
        }
    }

    function del_member(){
        $admin_id = intval($_SESSION['ADMIN_ID']);
		$user = M("users")->field(array('id', 'user_type'))->where("id='{$admin_id}'")->find();
		if ($user && $user['user_type'] != 1) {
			return $this->error('非超级管理员不能删除!');
		}
	
		$gid = intval(I('get.gid'));
		$id = intval(I('get.id'));
        
        if ($this->guild_member_model->del_member($gid, $id) !== false) {
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }

	/*
    
    function edit(){
        $id= intval(I("get.id"));
        $roles=$this->role_model->where("status=1")->order("id desc")->select();
        $this->assign("roles",$roles);
        $role_user_model=M("RoleUser");
        $role_ids=$role_user_model->where(array("user_id"=>$id))->getField("role_id",true);
        $this->assign("role_ids",$role_ids);
            
        $user=$this->users_model->where(array("id"=>$id))->find();
        $this->assign($user);
        $this->display();
    }
    
    function edit_post(){
        if (IS_POST) {
            if(!empty($_POST['role_id']) && is_array($_POST['role_id'])){
                if(empty($_POST['user_pass'])){
                    unset($_POST['user_pass']);
                }
                $role_ids=$_POST['role_id'];
                unset($_POST['role_id']);
                if ($this->users_model->create()) {
                    $result=$this->users_model->save();
                    if ($result!==false) {
                        $uid=intval($_POST['id']);
                        $role_user_model=M("RoleUser");
                        $role_user_model->where(array("user_id"=>$uid))->delete();
                        foreach ($role_ids as $role_id){
                            $role_user_model->add(array("role_id"=>$role_id,"user_id"=>$uid));
                        }
                        $this->success("保存成功！");
                    } else {
                        $this->error("保存失败！");
                    }
                } else {
                    $this->error($this->users_model->getError());
                }
            }else{
                $this->error("请为此用户指定角色！");
            }
            
        }
    }*/
    
    
    
    /*
    function userinfo(){
        $id=get_current_admin_id();
        $user=$this->users_model->where(array("id"=>$id))->find();
        $this->assign($user);
        $this->display();
    }
    
    function userinfo_post(){
        if (IS_POST) {
            $_POST['id']=get_current_admin_id();
            $create_result=$this->users_model
            ->field("user_login,user_email,last_login_ip,last_login_time,create_time,user_activation_key,user_status,role_id,score,user_type",true)//排除相关字段
            ->create();
            if ($create_result) {
                if ($this->users_model->save()!==false) {
                    $this->success("保存成功！");
                } else {
                    $this->error("保存失败！");
                }
            } else {
                $this->error($this->users_model->getError());
            }
        }
    }
    
        function ban(){
        $id=intval($_GET['id']);
        if ($id) {
            $rst = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','0');
            if ($rst) {
                $this->success("管理员停用成功！", U("user/index"));
            } else {
                $this->error('管理员停用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }
    
    function cancelban(){
        $id=intval($_GET['id']);
        if ($id) {
            $rst = $this->users_model->where(array("id"=>$id,"user_type"=>1))->setField('user_status','1');
            if ($rst) {
                $this->success("管理员启用成功！", U("user/index"));
            } else {
                $this->error('管理员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }
    */
    
    
}
