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
			<li class="active"><a >人工服务列表</a></li>
			<li ><a href="<?php echo U('Service/text');?>">人工服务文案列表</a></li>
			<li ><a href="<?php echo U('Service/game_service');?>">人工服务开关</a></li>
		</ul>

		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<!--<th align="center">ID</th>-->
					<th>房间名</th>
					<th>房间号</th>
					<th>设备号</th>
					<th>用户昵称</th>
					<th>用户ID</th>
					<th>内容</th>
					<th>状态</th>
					<th>时间</th>
					<th align="center"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php if(is_array($row)): foreach($row as $key=>$vo): ?><tr>
					<!--<td align="center"><?php echo ($vo["id"]); ?></td>-->
					<td><?php echo ($vo["room_name"]); ?></td>
					<td><?php echo ($vo["room_no"]); ?></td>
					<td><?php echo ($vo["device_no"]); ?></td>
					<td><?php echo ($vo["user_nicename"]); ?></td>
					<td><?php echo ($vo["user_id"]); ?></td>
					<td><?php echo ($vo["content"]); ?></td>
					<td <?php if(($vo["status"]) == "0"): ?>class="text-error"<?php else: ?>class="text-success"<?php endif; ?> ><strong><?php echo ($statusArr[$vo['status']]); ?></strong></td>
					<td><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>
					<td align="center">
						<?php if(($vo["status"]) == "0"): ?><a href="<?php echo U('Service/handle',array('id'=>$vo['id']));?>" class="btn btn-danger btn-small">标记处理</a>
						<?php else: ?>
							<a href="javascript:;" class="btn btn-success btn-small">已处理</a><?php endif; ?>
						<a href="<?php echo U('Service/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>
	</div>
	<script src="/public/js/common.js"></script>
</body>

<script>
	$(function(){
	    $("#handle").click(function(){
	        var id = $(this).attr('data');
	        $.ajax({
				url : "<?php echo U('Service/handle',array('id'=>"+id+"));?>",
				type : 'post',
				dataType : 'json',
				success : function (res) {
					if( res.status == 1 ){

					}

                }
			});
		});
	});
</script>

</html>