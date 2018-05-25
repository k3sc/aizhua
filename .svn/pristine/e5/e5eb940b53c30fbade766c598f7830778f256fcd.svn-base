<?php
namespace Common\Model;
use Common\Model\CommonModel;
class GuildModel extends CommonModel
{
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('guild_name', 'require', '工会名称不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT  ),
        array('create_aid', 'require', '创建人不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT ),
        array('manager_id', 'require', '会长不能为空', 1, 'regex', CommonModel:: MODEL_INSERT ),
        array('anchor_percent', 'require', '主播分摊比例不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT ),
        array('manager_percent','require', '会长分摊比例不能为空！', 1, 'regex', CommonModel:: MODEL_INSERT ) 
    );
    
    protected $_auto = array(
        array('create_tm','mGetDate',CommonModel:: MODEL_INSERT,'callback'),
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

    public function add_guild($data) {
		$create_tm = date('Y-m-d H:i:s');

        return $this->add(array(
            "name"=>$data['name'],
            "create_aid"=>$data['create_aid'],
            "manager_id"=>$data['manager_id'],
            "anchor_percent"=>$data['anchor_percent'],
            "manager_percent"=>$data['manager_percent'],
			"desc"=>$data['desc'],
			"create_tm"=>$create_tm,
			"state"=>1)
        );
    }

    public function list_guilds($map, $start, $num) {
        $map['state'] = 1;
        return $this->where($map)->order('create_tm desc')->limit($start . ',' . $num)->select();
    }
    
    public function get_guilds_count($map) {
        $map['state'] = 1;
        return $this->where($map)->count();
    }

	public function delete_guild($id) {
		$update_tm = date('Y-m-d H:i:s');
		return $this->where(array('id'=>$id))->save(array('state'=>3, 'update_tm'=>$update_tm));
	}

    public function get_guild($id) {
        return $this->where(array('id'=>$id))->find();
    }

    public function get_guild_by_manager_id($id) {
        return $this->where(array('manager_id'=>$id, 'state'=>1))->find();
    }
}
