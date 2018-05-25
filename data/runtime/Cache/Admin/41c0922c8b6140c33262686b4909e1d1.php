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
			<li class="active"><a href="">兑换礼品列表</a></li>
			<li><a href="<?php echo U('Gift/add');?>">兑换礼品添加</a></li>
		</ul>
		<form method="post" class="well form-search" action="<?php echo U('Gift/index');?>">
			<input type="text" name="keyword" value="<?php echo ($keyword); ?>"  placeholder="请输入礼品名称或ID">
			<select name="type_id" placeholder="礼品分类" style="width: 155px;">
				<option value="">-- 选择礼品分类 --</option>
				<?php echo ($type); ?>
			</select>
			礼品库存：
			<input type="text" name="quantity1" value="<?php echo ($quantity1); ?>" style="width: 60px;">
			至
			<input type="text" name="quantity2" value="<?php echo ($quantity2); ?>" style="width: 60px;">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>
		<form method="post" class="js-ajax-form" action="<?php echo U('Gift/listorders');?>">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>礼品分类</th>
						<th>礼品名称</th>
						<th>可兑换礼品的娃娃数量</th>
						<th>礼品库存</th>
						<th>成本</th>
						<th>礼品图片</th>
						<th>礼品出货量</th>
						<th>发布时间</th>

						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo str_pad($vo['id'],4,"0",STR_PAD_LEFT); ?></td>
						<td><?php echo ($vo["class_name"]); ?></td>
						<td><?php echo ($vo["name"]); ?></td>
						<td><?php echo ($vo["convert_num"]); ?> 个</td>
						<td><?php echo ($vo["quantity"]); ?>个</td>
						<td><?php echo ($vo["cost"]); ?>元</td>
						<td><img width="25" height="25" src="<?php echo ($vo["img"]); ?>" /></td>
						<td><?php echo ($vo["shipment_num"]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>
						<td align="center">	
							<a href="<?php echo U('Gift/look',array('id'=>$vo['id']));?>" >查看</a> | 
							<a href="<?php echo U('Gift/edit',array('id'=>$vo['id']));?>" >编辑</a> |
							<?php if(($vo["is_show"]) == "1"): ?><a href="<?php echo U('Gift/isshow',array('id'=>$vo['id'],'action'=>0));?>" >下架</a> |
							<?php else: ?>
								<a href="<?php echo U('Gift/isshow',array('id'=>$vo['id'],'action'=>1));?>" >上架</a> |<?php endif; ?>
							<a href="<?php echo U('Gift/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
							
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>