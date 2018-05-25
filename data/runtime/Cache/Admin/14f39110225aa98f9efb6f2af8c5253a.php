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
        <li class="active"><a>营业简报</a></li>
    </ul>
    <table class="table table-hover table-bordered">
        <!--<thead>
        <tr>
          <th>今日充值总额</th>
          <th>今日消费总额</th>
          <th>设备在线数量</th>
          <th>故障设备报告</th>
          <th>发货情况</th>
        </tr>
        </thead>
        <tbody>
        <tr>
          <td><?php echo ($result["recharge_money"]); ?></td>
          <td><?php echo ($result["total_cost"]); ?></td>
          <td><?php echo ($result["online_total"]); ?></td>
          <td><?php echo ($result["fault_total"]); ?></td>
          <td><?php echo ($result["waybill_total"]); ?></td>
        </tr>

        </tbody>-->
        <thead>
        <tr>
            <th>今日充值总额</th>
            <?php if(is_array($result["recharge_money_info"])): foreach($result["recharge_money_info"] as $key=>$v): ?><th><?php echo ($pay_arr[$v['type']]); ?></th><?php endforeach; endif; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo ($result["recharge_money"]); ?></td>
            <?php if(is_array($result["recharge_money_info"])): foreach($result["recharge_money_info"] as $key=>$v): ?><td><?php echo ($v["money"]); ?> 元</td><?php endforeach; endif; ?>
        </tr>
        </tbody>
    </table>
    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th>今日消费总额</th>
            <th>消费增币</th>
            <th>夹娃娃次数</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo ($result["total_cost"]); ?> 币</td>
            <td><?php echo ($result["total_givemoney"]); ?> 币</td>
            <td><?php echo ($result["game_total"]); ?> 次</td>
        </tr>
        </tbody>
    </table>
    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th>当前设备在线数</th>
            <th>今日故障数</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo ($result["online_total"]); ?></td>
            <td>
                <a class="text-error" href="<?php echo U('Fault/index');?>"><u><strong><?php echo ($result["fault_total"]); ?></strong></u></a>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th>待处理发货申请</th>
            <th>今天已出货</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <a class="text-warning" href="<?php echo U('Waybill/lists',array('status'=>1));?>"><u><strong><?php echo ($result["waydbill_total"]); ?></strong></u></a>
            </td>
            <td>
                <a class="text-success" href="<?php echo U('Waybill/lists',array('status'=>2));?>"><u><strong><?php echo ($result["waybill_total"]); ?></strong></u></a>
            </td>
        </tr>
        </tbody>
    </table>

</div>
<script src="/public/js/common.js"></script>
</body>
</html>