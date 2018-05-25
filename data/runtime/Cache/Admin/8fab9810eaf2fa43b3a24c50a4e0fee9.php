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
        <li ><a href="<?php echo U('Waybill/lists');?>">运单列表</a></li>
        <li class="active"><a href="javascript:;">编辑运单</a></li>
    </ul>
    <form action="<?php echo U('waybill/edit_post',array('waybillno'=>$data[0]['waybillno']));?>" method="post">
        <table class="table table-hover table-bordered">
            <tr>
                <td>订单编号：<font color="red"><b><?php echo ($data[0]["waybillno"]); ?></b></font></td>
                <td>收件人姓名：<input type="text" name="uname" value="<?php echo ($data[0]["uname"]); ?>" <?php if(($data[0]["status"]) != "1"): ?>readonly<?php endif; ?> ></td>
                <td>收件人手机号：<input type="text" name="phone" value="<?php echo ($data[0]["phone"]); ?>" <?php if(($data[0]["status"]) != "1"): ?>readonly<?php endif; ?> ></td>
            </tr>
            <tr>
                <td colspan="3">收件人详细地址：<input type="text" name="addr" value="<?php echo ($data[0]["addr"]); echo ($data[0]["addr_info"]); ?>" style="width: 1175px;" <?php if(($data[0]["status"]) != "1"): ?>readonly<?php endif; ?> ></td>
            </tr>
            <tr>
                <td colspan="3">物品数量：<input type="text" value="<?php echo ($num); ?>" readonly></td>
            </tr>
            <tr>
                <td colspan="3">物品名称：<input type="text" value="<?php echo ($data[0]["goodsname"]); ?>" <?php if(($data[0]["status"]) != "1"): ?>readonly<?php endif; ?> ></td>
            </tr>
            <tr>
                <td colspan="3">物品明细
                    <table border="1px solid black">
                        <?php if(is_array($data[0]["goods"])): foreach($data[0]["goods"] as $key=>$vo): ?><tr>
                                <td><?php echo ($vo["name"]); ?>( ID:<?php if(empty($vo["wawa_id"])): echo ($vo["gift_id"]); else: echo ($vo["wawa_id"]); endif; ?> )</td>
                                <td><?php echo ($vo["num"]); ?></td>
                            </tr><?php endforeach; endif; ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">用户留言备注：<b><?php echo ($data[0]["remark"]); ?></b></td>
            </tr>
            <br><br>
            <tr>
                <td>用户ID：<?php echo ($data[0]["user_id"]); ?></td>
                <td>用户昵称：<?php echo ($data[0]["user_nicename"]); ?></td>
                <td>下单时间：<?php echo (date("Y-m-d H:i:s",$data[0]["ctime"])); ?></td>
            </tr>
            <tr>
                <td>快递公司: <input type="text" name="kdname" value="<?php echo ($data[0]["kdname"]); ?>" <?php if(($data[0]["status"]) != "1"): ?>readonly<?php endif; ?> ></td>
                <td colspan="2">快递单号: <input type="text" name="kdno" value="<?php echo ($data[0]["kdno"]); ?>" <?php if(($data[0]["status"]) != "1"): ?>readonly<?php endif; ?> ></td>
            </tr>
            <tr>
                <td colspan="3">系统备注： <textarea style="width: 725px; height: 50px;" name="sys_remark" <?php if(($data[0]["status"]) == "3"): ?>readonly<?php endif; ?> ><?php echo ($data[0]["sys_remark"]); ?></textarea></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center"><input class="btn btn-success" type="submit" value="保存"></td>
            </tr>
        </table>
    </form>
</div>
<!--<script src="/public/js/jqprint/jquery-1.4.4.min.js"></script>-->
<script src="/public/js/common.js"></script>
<script src="/public/js/jqprint/jquery.jqprint.js"></script>

</body>
</html>