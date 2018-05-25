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
			<li class="active"><a href="">娃娃列表</a></li>
			<li><a href="<?php echo U('Capturewawa/add');?>">娃娃添加</a></li>
		</ul>
		<form method="post" class="well form-search" action="<?php echo U('Product/index');?>">
			<input type="text" name="keyword" value="<?php echo ($keyword); ?>"  placeholder="请输入娃娃姓名或ID">

			<select name="type">
		        <option value="">-- 选择娃娃类别 --</option>
				<?php if(is_array($wawa_type)): foreach($wawa_type as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($vo["id"]) == $type): ?>selected<?php endif; ?> ><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
	       	</select>

			<input type="submit" class="btn btn-primary" value="搜索">
		</form>
		<form method="post" class="js-ajax-form" action="<?php echo U('Product/listorders');?>">
			<!--<div class="table-actions">-->
				<!--<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>-->
			<!--</div>-->
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
					    <!--<th>排序</th>-->
						<th align="center">ID</th>
						<th>类别</th>
						<th>娃娃名称</th>
						<th>兑币数</th>
						<th>娃娃图片</th>
						<th>成本</th>
						<th>游戏价格</th>
						<th>库存</th>
						<th>寄存</th>
						<th>待发货</th>
						<th>已发货</th>
						<th>已转赠</th>
						<th>已兑币</th>
						<th>已兑礼品</th>
						<th>出货量</th>
						<th>抓中总次数</th>
						<th>抓取总次数</th>
						<th>兑换设置</th>
						<th>发布时间</th>

						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php $type=array("1"=>"普通娃娃","2"=>"高级娃娃"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
					    <!--<td><input name="listorders[<?php echo ($vo['id']); ?>]" type="text" size="3" value="<?php echo ($vo['orderno']); ?>" class="input input-order"></td>-->
						<td class="text-error"><?php echo str_pad($vo['id'],4,"0",STR_PAD_LEFT); ?></td>
						<td><?php echo ($vo['name']); ?></td>
						<td><?php echo ($vo['giftname']); ?></td>
						<td><?php echo ($vo['needcoin']); ?></td>
						<td><img width="25" height="25" src="<?php echo ($vo['gifticon']); ?>" /></td>
						<td><?php echo ($vo["cost"]); ?></td>
						<td><?php echo ($vo["spendcoin"]); ?></td>
						<td><?php echo ($vo["stock"]); ?></td>
						<td><?php echo ($vo["totalcount"]); ?></td>
						<td><?php echo ($vo["applysendcount"]); ?></td>
						<td><?php echo ($vo["hassendcount"]); ?></td>
						<td><?php echo ($vo["giveoutcount"]); ?></td>
						<td><?php echo ($vo["convertcoin"]); ?></td>
						<td><?php echo ($vo["convertgift"]); ?></td>
						<td><?php echo ($vo["chuhuo"]); ?></td>
						<td><?php echo ($vo["count_success"]); ?></td>
						<td><?php echo ($vo["count"]); ?></td>
						<td><?php echo ($vo["convert"]); ?></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>

						<td align="center">	
						<!-- 	<a href="<?php echo U('Gift/look',array('id'=>$vo['id']));?>" >查看</a> |  -->
							<a href="<?php echo U('Capturewawa/edit',array('id'=>$vo['id']));?>" class="btn btn-success btn-small" >编辑</a> |
							<a href="<?php echo U('Capturewawa/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn btn btn-danger btn-small" data-msg="您确定要删除吗？">删除</a>
							
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>
			<!--<div class="table-actions">-->
				<!--<button class="btn btn-primary btn-small js-ajax-submit" type="submit"><?php echo L('SORT');?></button>-->
			<!--</div>-->
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>