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
			<li ><a href="<?php echo U('Gift/index');?>">兑换礼品列表</a></li>

			<li><a href="<?php echo U('Gift/add');?>">兑换礼品添加</a></li>
			<li class="active" ><a href="">礼品兑换记录</a></li>

		</ul>
		<form method="post" class="well form-search" action="<?php echo U('Gift/record');?>">
			<select name="gift_id" placeholder="礼品列表" style="width: 155px;">
				<option value="">-- 选择礼品 --</option>
				<?php echo ($gift); ?>
			</select>
			<select name="status" placeholder="选择状态" style="width: 155px;">
				<option value="">-- 选择状态 --</option>
				<?php echo ($statuslist); ?>
			</select>
			<input type="text" name="userId" value="<?php echo ($userId); ?>"  placeholder="用户ID">
			<input type="text" name="nickname" value="<?php echo ($nickname); ?>"  placeholder="用户昵称">

			<input type="submit" class="btn btn-primary" value="搜索">
		</form>
		<form method="post" class="js-ajax-form" action="<?php echo U('Gift/record_list');?>">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>用户id</th>
						<th>用户昵称</th>
						<th>礼品id</th>
						<th>礼品名称</th>
						<th>礼品图片</th>
						<th>兑换礼数量</th>
						<th>状态</th>
						<th>兑换礼时间</th>
						<!--<th>兑换的娃娃</th> -->
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
						<td align="center"><?php echo str_pad($vo['id'],4,"0",STR_PAD_LEFT); ?></td>
						<td><?php echo ($vo["user_id"]); ?></td>
						<td><?php echo ($vo["user_nickname"]); ?></td>
						<td><?php echo ($vo["gift_id"]); ?></td>
						<td><?php echo ($vo["name"]); ?> 个</td>
						<td><img width="25" height="25" src="<?php echo ($vo["img"]); ?>" /></td>
						<td><?php echo ($vo["number"]); ?> 个</td>

						<td><?php echo ($vo["statusTxt"]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>
						<!-- <td><?php echo ($vo["str"]); ?>
							<?php if(is_array($data['option'])): foreach($data['option'] as $k=>$vovo): echo ($vovo["name"]); ?> <?php echo ($vovo["num"]); ?><br><?php endforeach; endif; ?>
						</td> -->

					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>