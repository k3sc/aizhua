<?php

/**
 * 流量包激活
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class TrafficController extends AdminbaseController {
    function index(){
			
    	$live=M("traffic");
    	$count=$live->count();
    	$page = $this->page($count, 20);
    	$lists = $live
    	->where()
    	->order("id DESC")
    	->limit($page->firstRow . ',' . $page->listRows)
    	->select();
			
			foreach($lists as $k=>$v){
				 if($v['touid']>0){
					 $user_nicename=M("user")->where("id='$v[touid]'")->getField("user_nicename");
					 $lists[$k]['user_nicename']=$user_nicename;					 
				 }

			}
			
    	$this->assign('lists', $lists);
    	$this->assign("page", $page->show('Admin'));
    	
    	$this->display();
    }
		
		function del(){
			 	$id=intval($_GET['id']);
					if($id){
						$result=M("traffic")->delete($id);				
							if($result){
									$this->success('删除成功');
							 }else{
									$this->error('删除失败');
							 }			
					}else{				
						$this->error('数据传入失败！');
					}								  		
		}		
		
		function active(){
			 	$id=intval($_GET['id']);
			 	$mobile=$_GET['mobile'];
				
					if($id && $mobile){
						$user=M("users")->field("id")->where("mobile='$mobile'")->find();
						if($user){
							
							$result=M("traffic")->where("id='$id'")->save(array("isactive"=>1,"activetime"=>time(),"uid"=>$user['id']));				
							if($result){
									$this->success('激活成功');
							 }else{
									$this->error('激活失败');
							 }								
						}else{
							 	$this->error('无会员使用此手机号');
						}
	
					}else{				
						$this->error('数据传入失败！');
					}								  	
		}				
		
		function add(){					  
					$this->display();				
		}				
		
		function add_post(){
			
				if(IS_POST){			
					 $traffic=M("traffic");
					 $mobile=$_POST['mobile'];
					 if($mobile==''){
						 $this->error('号码不能为空');
					 }
					 $ifexist=$traffic->where("mobile='$mobile'")->find();
					 if( $ifexist){
						 $this->error('此号码已存在');
					 }
					 
					 $data['mobile']=$mobile;
					 $data['addtime']=time();
					 $data['updatetime']=time();
					 
					 $result=$traffic->add($data); 
						 if($result){
								$this->success('添加成功');
						 }else{
								$this->error('添加失败');
						 }						

				}										  
					$this->display();				
		}	
				
   /* 导入提交 */
    public function excel_import() { 
		       
				  $file=$_FILES['file'];
 
        if (IS_POST) {
						$savepath=date('Ymd').'/';
									//上传处理类
						$config=array(
								'rootPath' => './'.C("UPLOADPATH"),
								'savePath' => $savepath,
								'maxSize' => 11048576,
								'saveName'   =>    array('uniqid',''),
								'exts'       =>    array('xls', 'xlsx'),
								'autoSub'    =>    false,
						);
						$upload = new \Think\Upload($config);// 
						$info=$upload->upload();
									//开始上传
									if ($info) {
											//上传成功
											//写入附件数据库信息
											$first=array_shift($info);
											if(!empty($first['url'])){
												$url=$first['url'];
											}else{
												$url=C("TMPL_PARSE_STRING.__UPLOAD__").$savepath.$first['savename'];
											}

										Vendor("PHPExcel.PHPExcel.IOFactory");
					
										if(substr($url,-5)==".xlsx"){
											$objReader = \PHPExcel_IOFactory::createReader('Excel2007');
										}else{
											$objReader = \PHPExcel_IOFactory::createReader('Excel5');

										}

										//$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
										$objPHPExcel = $objReader->load('./'.C("UPLOADPATH").$savepath.$first['savename']); //$filename可以是上传的文件，或者是指定的文件
										$sheet = $objPHPExcel->getSheet(0);
										$highestRow = $sheet->getHighestRow(); // 取得总行数
										$highestColumn = $sheet->getHighestColumn(); // 取得总列数
											 

										$data=array();
										//循环读取excel文件,读取一条,插入一条
										for($num=2,$isi=0,$isn=0;$num<=$highestRow;$num++)
										{
											$mobile = $objPHPExcel->getActiveSheet()->getCell("A".$num)->getValue();//获取A列的值
											$time=time();
											 $isexist=M("traffic")->where("mobile='$mobile'")->find();
											 
											 if(!$isexist){
												   	$data[$isi]['mobile']=$mobile;
														$data[$isi]['addtime']=$time;
														$data[$isi]['updatetime']=$time;
												 $isi++;
											 }else{
												 $isn++;
											 }	 
										}					
                     
										$result=	M("traffic")->addAll($data);			
										if ($result) {
												$this->success("导入成功！有{$isn}条重复信息未导入");
										} else {
												$this->error("导入失败！所有手机号已存在");
										}                  										

									} else {
											//上传失败，返回错误
											$this->error($upload->getError());
									}
        } 		 
				
    }	
   
}
