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
        <li class="active"><a >故障上报记录</a></li>
    </ul>
    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th align="center">ID</th>
            <th>内容</th>
            <th>花费金币</th>
            <th>房间名（房间号）</th>
            <th>处理状态</th>
            <th>时间</th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($row)): foreach($row as $key=>$vo): ?><tr>
                <td><?php echo ($vo["id"]); ?> </td>
                <td><?php echo ($vo["content"]); ?></td>
                <td><?php echo ($vo["coin"]); ?></td>
                <td><?php echo ($vo["room_name"]); ?>(<?php echo ($vo["room_no"]); ?>)</td>
                <td><?php if(($vo["status"]) == "1"): ?>已处理<?php else: ?>未处理<?php endif; ?></td>
                <td><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>
            </tr><?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
<script src="/public/js/common.js"></script>
</body>
</html>