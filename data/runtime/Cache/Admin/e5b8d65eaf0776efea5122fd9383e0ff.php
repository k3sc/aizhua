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
        <li ><a href="<?php echo U('Users/index');?>">用户列表</a></li>
        <li class="active"><a >充值流水</a></li>
        <li ><a href="<?php echo U('Users/consume',array('id'=>$_GET['id']));?>">消费流水</a></li>
        <li ><a href="<?php echo U('Users/sysbill',array('id'=>$_GET['id']));?>">系统赠送流水</a></li>
    </ul>

        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <!--<th align="center">ID</th>-->
                <th>充值订单号</th>
                <th>充值描述</th>
                <th>支付类型</th>
                <th>充值金额</th>
                <th>兑换点数(包含赠送)</th>
                <th>获赠点数</th>
                <th>状态</th>
                <th>时间</th>
                <!--<th align="center"><?php echo L('ACTIONS');?></th>-->
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($row)): foreach($row as $key=>$vo): ?><tr>
                    <td align="center"><?php echo ($vo["oid"]); ?></td>
                    <td><?php echo ($vo["log"]); ?></td>
                    <td><?php echo ($paytype[$vo['type']]); ?></td>
                    <td><?php echo ($vo["money"]); ?></td>
                    <td><?php echo ($vo["coin"]); ?></td>
                    <td><?php echo ($vo["coingive"]); ?></td>
                    <td><?php echo ($status[$vo['status']]); ?></td>
                    <td><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>
                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="pagination"><?php echo ($page); ?> 共&nbsp;&nbsp;<?php echo ($count); ?>&nbsp;&nbsp;条</div>
</div>
<script src="/public/js/common.js"></script>
</body>
</html>