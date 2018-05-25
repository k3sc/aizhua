<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class IpController extends AdminbaseController{
	
	function _initialize() {
		parent::_initialize();
		$myfile = fopen("api/Config/iplimit.php", "r") or die("Unable to open file!");
		$a =  fread($myfile,filesize("api/Config/iplimit.php"));
		fclose($myfile);
		$this->data = json_decode($a,true);		
	}
	public $data;
	function index(){
		
		
		$this->assign("data",$this->data);
		$this->display();
	}	
	
	function p_add(){
		$ip = filter_var(ltrim( $_POST['ip']), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)? ltrim( $_POST['ip']) : 0;			
		if($ip){
			$data = $this->data;
			$key = count($data);
			$data[$key] = $ip;
			$a = file_put_contents("api/Config/iplimit.php",json_encode($data));
			$this->success('修改成功');
		}else{
			$this->error('修改失败');
		}

	}
	function del(){
		$ip = $_GET['ip'];
		$data = $this->data;
		foreach($data as $k =>$t){
			if($ip = $t){
				unset($data[$k]);
			}
		}
		$a = file_put_contents("api/Config/iplimit.php",json_encode($data));
		$this->success('修改成功');
	}	 
}