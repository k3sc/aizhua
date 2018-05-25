<?php
/* 
   扩展配置
 */

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ConfigprivateController extends AdminbaseController{
	
	protected $attribute;
	
	function _initialize() {
		parent::_initialize();
		$this->attribute = D("Common/Attributeprivate");
	}
	
	function index(){
		$config=M("config_private")->find(1);
/* 		$attribute2=M("attributeprivate")->field("name,title,remark,type,extra,state")->order("orderno asc")->select();
		foreach($attribute2 as $key=>$vals){
			 if($vals['extra']){	
            $string=$vals['extra'];	

						if(0 === strpos($string,':')){
								// 采用函数定义
								$attribute2[$key]['list'] =  eval(substr($string,1).';');
						}else{
								$array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
								if(strpos($string,':')){
										$value  =   array();
										foreach ($array as $val) {
												list($k, $v) = explode(':', $val);
												$value[$k]   = $v;
										}
								}else{
										$value  =   $array;
								}									
							  $attribute2[$key]['list'] = $value;
						}
			 }
			 
				 switch($vals['type']){
					 
						case 'checkbox':
						  $config[$vals['name']]=explode(",",$config[$vals['name']]);
						  break;
						default:
						
						break;
					 
				 }			 
			 
		} */

		$this->assign('config',$config);
		/* $this->assign('attribute',$attribute2); */
	/* 	var_dump($attribute2); */
		$this->display();
	}

    //排序
    public function listorders() { 
		
        $ids = $_POST['listorders'];
        foreach ($ids as $key => $r) {
            $data['orderno'] = $r;
            M("attributeprivate")->where(array('id' => $key))->save($data);
        }
				
        $status = true;
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }		
	
	function set_post(){
		if(IS_POST){
			
			 $config=I("post.post");
			
			/* $attribute2=M("attributeprivate")->field("name,type")->where("type='editor' or type='checkbox'")->select();
			foreach( $attribute2 as $vo){
				 switch($vo['type']){
					case 'editor':
						$config[$vo['name']]=htmlspecialchars_decode($config[$vo['name']]);
						break;
					case 'checkbox':
						$config[$vo['name']]=implode(",",$config[$vo['name']]);
						break;
					default:
							
						break;
					 
				 }
				
			} */
				
				if (M("config_private")->where("id='1'")->save($config)!==false) {
					$this->success("保存成功！");
				} else {
					$this->error("保存失败！");
				}
		
		}
	}
	
	function lists(){			
    	$attribute2=M("attributeprivate");
    	$count=$attribute2->count();
    	$page = $this->page($count, 20);
    	$lists = $attribute2
    	->where()
    	->order("orderno asc")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
	}
	
    /**
     * 新增页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function add(){

        $this->meta_title = '新增属性';
        $this->display('edit');
    }

    /**
     * 编辑页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function edit(){
        $id = I('get.id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }

        /*获取一条记录的详细数据*/
        $Model = M('attributeprivate');
        $data = $Model->field(true)->find($id);
        if(!$data){
            $this->error($Model->getError());
        }
   
        $this->assign('info', $data);
        $this->meta_title = '编辑属性';
        $this->display();
    }

    /**
     * 更新一条数据
     * @author huajie <banhuajie@163.com>
     */
    public function update(){
        $res = D('attributeprivate')->update();
        if(!$res){
            $this->error(D('attributeprivate')->getError());
        }else{
            $this->success($res['id']?'更新成功':'新增成功',U('Configprivate/lists'));
        }
    }
    /**
     * 删除一条数据
     * @author huajie <banhuajie@163.com>
     */
    public function delete(){
        $id = I('id');
        empty($id) && $this->error('参数错误！');

        $Model = D('attributeprivate');

        $info = $Model->getById($id);
        empty($info) && $this->error('该字段不存在！');

        //删除属性数据
        $res = $Model->delete($id);

        //删除表字段
        $Model->deleteField($info);
        if(!$res){
            $this->error($Model->getError());
        }else{
            $this->success('删除成功');
        }
    }	
		
		/* 生成sig */
		public function getsig(){
			$sig='';
			$config=M("config_private")->where("id='1'")->find();

			if(!$config['im_sdkappid'] || !$config['im_admin']){
				$sig='请先配置云通信SdkAppId 和 云通信账号管理员';
			}else if(trim($config['im_user_sig'])!=''){
					$sig=$config['im_user_sig'];
			}else{
				$appid=(int)$config['im_sdkappid'];
				$id=$config['im_admin'];
				//return $sig;	
				try{
					require_once("./api/public/TLSSig.php");
					$api = new \TLSSigAPI();
					$api->SetAppid($appid);
					$private = file_get_contents('./api/public/keys/private_key.pem');
					$api->SetPrivateKey($private);
					$public = file_get_contents('./api/public/keys/public_key.pem');
					$api->SetPublicKey($public);
					$sig = $api->genSig($id);
					var_dump($sig);
					$result = $api->verifySig($sig, 'yunbao', $init_time, $expire_time, $error_msg);
					var_dump($result);
					var_dump($init_time);
					var_dump($expire_time);
					var_dump($error_msg);
				}catch(Exception $e){
						file_put_contents('./setSig.txt',date('y-m-d H:i:s').'提交参数信息 :'.$e->getMessage()."\r\n",FILE_APPEND);
				}					
			}
	
			$this->assign("sig",$sig);
			$this->display();
		}
		
		public function state_hide()
		{
			$id = I('id');
			$res=M("attributeprivate")->where("id=".$id)->setField("state", 1);
			if(!$res)
			{
				 $this->error(D('Attribute')->getError());
			}
			else
			{
				 $this->success('隐藏成功');
			}
		}
		public function state_display()
		{
			$id = I('id');
			$res=M("attributeprivate")->where("id=".$id)->setField("state", 0);
			if(!$res)
			{
				 $this->error(D('Attribute')->getError());
			}
			else
			{
				 $this->success('开启显示');
			}
		}
}