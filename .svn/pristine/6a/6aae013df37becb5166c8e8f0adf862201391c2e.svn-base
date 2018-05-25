<?php
namespace Common\Model;
use Common\Model\CommonModel;
class GuildMemberModel extends CommonModel
{
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('gid', 'require', '工会ID不能为空', 1, 'regex', CommonModel:: MODEL_INSERT  ),
        array('uid', 'require', 'UID不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT ),
        array('type', 'require', '成员类型不能为空', 1, 'regex', CommonModel:: MODEL_INSERT ),
    );
    
    protected $_auto = array(
        array('add_tm','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
        array('update_tm','mGetDate',self::MODEL_BOTH,'callback'),
        array('state', 1)
    );
    
    //用于获取时间，格式为2012-02-03 12:12:12,注意,方法不能为private
    function mGetDate() {
        return date('Y-m-d H:i:s');
    }
    
    protected function _before_write(&$data) {
        parent::_before_write($data);
    }

    public function add_guild_member($data) {
		$add_tm = date('Y-m-d H:i:s');
        $this->add(array(
            "gid"=>$data['gid'],
            "uid"=>$data['uid'],
            //"type"=>$data['type'],
			"add_tm"=>$add_tm,
			"state"=>1)
        );
    }

    public function list_member($gid, $map, $start, $num) {
        $map['gid'] = $gid;
        $map['state'] = 1;
        return $this->where($map)->order('add_tm desc')->limit($start . ',' . $num)->select();
    }
    
    public function get_member_count($gid) {
        $map['gid'] = $gid;
        $map['state'] = 1;
        return $this->where($map)->count();
    }

	public function del_member($gid, $id) {
		 $update_tm = date('Y-m-d H:i:s');
		 return $this->where(array('gid'=>$gid, 'id'=>$id))->save(array('state'=>3, 'update_tm'=>$update_tm));
	}

    public function get_all_members_total_votes($gid) {
        $members = $this->where(array('gid'=>$gid, 'state'=>1))->select();
        $total_votes = 0;
        foreach ($members as $k=>$v) {
            $user = M("users")->field("votes")->where("id='{$v['uid']}'")->find();
            $total_votes += $user['votes'];
        }

        return $total_votes;
    }

    public function get_guild_member($gid, $uid) {
        return $this->where(array('gid'=>$gid, 'uid'=>$uid))->find();
    }

    public function get_guild_member_by_uid($uid) {
        return $this->where(array('uid'=>$uid, 'state'=>1))->find();
    }

	public function del_all_member($gid) {
		 $update_tm = date('Y-m-d H:i:s');
		 return $this->where(array('gid'=>$gid))->save(array('state'=>3, 'update_tm'=>$update_tm));
	}
}
