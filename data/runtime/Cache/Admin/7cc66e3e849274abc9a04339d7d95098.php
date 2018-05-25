<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		.length_3{width: 180px;}
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "/",
    JS_ROOT: "public/js/",
    TOKEN: ""
};
</script>
<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/public/js/jquery.js"></script>
    <script src="/public/js/jquery.tablesort.min.js"></script>
    <script src="/public/js/wind.js"></script>
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <script src="/public/js/jquery.validate.js"></script>

<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>



</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('Users/index');?>">用户列表</a></li>
			<li class="active"><a>运单列表</a></li>
		</ul>
			<form method="post" class="js-ajax-form" action="<?php echo U('Users/express');?>">
				<input type="hidden" name="id" value="<?php echo ($id); ?>" style="width: 150px;">
			运单编号:<input type="text" name="name" value="<?php echo ($name); ?>" style="width: 150px;">&nbsp;&nbsp;&nbsp;
			手机号：<input type="text" name="mobile" value="<?php echo ($mobile); ?>" style="width: 150px;">&nbsp;&nbsp;
			提交时间：
			<input type="text" name="start_time" class="js-date date" value="<?php echo ($start_time); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date date" name="end_time" value="<?php echo ($end_time); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;&nbsp;
			快递状态:
			<select name="status" style="width: 90px;"> 
				<option value="">请选择</option> 
				<option value="1"<?php if(($status) == "1"): ?>selected<?php endif; ?>>待邮寄</option> 
				<option value="2"<?php if(($status) == "2"): ?>selected<?php endif; ?>>已发货</option> 
				<option value="3"<?php if(($status) == "3"): ?>selected<?php endif; ?>>已确认</option> 			
			</select> 
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>	 
		<form method="post" class="js-ajax-form" action="<?php echo U('Users/express');?>">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>用户名(ID)</th>
						<th>运单编号</th>
						<th>快递编号</th>
						<th>商品名</th>
						<th>商品分类</th>
						<th>邮寄备注</th>
						<th>快递状态</th>
						<th>快递公司</th>
						<th>寄件时间</th>
						<!-- <th align="center"><?php echo L('ACTIONS');?></th> -->
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
						<td><?php echo ($vo["waybill_id"]); ?> </td>
						<td align="center"><?php echo ($vo["user_nicename"]); ?> (<?php echo ($vo["user_id"]); ?>)</td>
						<td><?php echo ($vo["waybillno"]); ?></td>
						<td><?php echo ($vo["kdno"]); ?></td>
						<td><?php echo ($vo["giftname"]); ?> </td>
						<td>
							<?php if($vo["type"] == 1): ?>娃娃
							<?php else: ?>
								礼品<?php endif; ?>
						</td>
						<td><?php echo ($vo["remark"]); ?></td>
						<td>
							<?php if($vo["status"] == 1): ?>待邮寄
							<?php elseif($vo["status"] == 2): ?>
								已发货
							<?php else: ?>
								已确认<?php endif; ?>
						</td>
						<td><?php echo ($vo["kdname"]); ?></td>
						<td><?php echo (date('Y-m-d H:i:s',$vo["ctime"])); ?></td>
							<!-- <td align="center">	
						<a href="<?php echo U('Gift/look',array('id'=>$vo['id']));?>" >查看</a> | 
							<a href="<?php echo U('Gift/edit',array('id'=>$vo['id']));?>" >编辑</a> |  -->
							<!-- <?php if($vo["user_status"] == 1): ?><a href="<?php echo U('Users/delete',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要封号吗？" style="color: red;">封号</a> | 
							<?php else: ?>
								<a href="<?php echo U('Users/relieve',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要解封吗？" style="color: golden;">解封</a> |<?php endif; ?>
						    <a href="<?php echo U('Users/body',array('id'=>$vo['id']));?>" >娃娃</a> | 
							
						</td>-->
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?> 共<?php echo ($count); ?>条</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>