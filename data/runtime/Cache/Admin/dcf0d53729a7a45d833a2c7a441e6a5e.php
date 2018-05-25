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
        <!--<li ><a href="<?php echo U('Probability/index');?>">所有概率</a></li>-->
        <li ><a href="<?php echo U('Probability/room');?>">房间概率模式</a></li>
        <li ><a href="<?php echo U('Probability/sell');?>">用户贩卖模式</a></li>
        <li class="active"><a href="">用户贩卖模式规则列表</a></li>
    </ul>

    <table class="table table-hover table-bordered">
        <tbody>
        <?php if(is_array($row)): foreach($row as $key=>$vo): ?><tr>
                <td align="center">
                    一次性充值 <strong class="text-error"><?php echo ($vo["money"]); ?></strong> 元以上，送 <strong class="text-error"><?php echo ($vo["zj_count"]); ?></strong> 次中奖抓力，每抓 <strong class="text-error"><?php echo ($vo["count"]); ?></strong> 次随机出一次
                </td>

                <td align="center">
                    <a class="btn btn-success btn-small edit" href="<?php echo U('Probability/sellmodel_edit',array('id'=>$vo['id']));?>">编辑</a>
                    <a class="btn btn-danger btn-small del" href="<?php echo U('Probability/sellmodel_del',array('id'=>$vo['id']));?>">删除</a>
                </td>
            </tr><?php endforeach; endif; ?>
        <tr>
            <td>
                <form id="sellmodel_add_form" action="<?php echo U('Probability/sellmodel_add');?>" method="post">
                    一次性充值 <input type="text" name="money" style="width: 50px;"> 元以上，送 <input type="text" name="zj_count" style="width: 50px;"> 次中奖抓力，每抓 <input type="text" name="count" style="width: 50px;"> 次随机出一次
                </form>
            </td>
            <td>
                <a href="javascript:;" class="btn btn-default btn-small" id="sellmodel_btn">新增规则</a>
            </td>
        </tr>
        </tbody>
    </table>

</div>

</body>
<script src="/public/js/common.js"></script>

<script>
    $(function () {
        $("#sellmodel_btn").click(function () {
            $("#sellmodel_add_form").submit();
        });
    });
</script>

</html>