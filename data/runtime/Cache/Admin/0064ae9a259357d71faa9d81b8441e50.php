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
        <li class="active"><a href="">设备列表</a></li>
        <li><a href="<?php echo U('Device/device_add');?>">设备添加</a></li>
    </ul>

    <form action="<?php echo U('Device/device_list');?>" method="post" class="well form-search">
        设备唯一码：<input type="text" name="unicode" value="<?php echo ($_POST['unicode']); ?>">
        设备号：<input type="text" name="device_no" value="<?php echo ($_POST['device_no']); ?>">
        所在地：
        <select name="device_addr_id" style="width: 150px;">
            <option value=""> -- 全部 --</option>
            <?php if(is_array($addr)): foreach($addr as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($_POST['device_addr_id']) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["addr"]); ?></option><?php endforeach; endif; ?>
        </select>
        设备状态：
        <select name="status" style="width: 100px;">
            <option value="">-- 全部 --</option>
            <option value="1" <?php if(($_POST['status']) == "1"): ?>selected<?php endif; ?> >-- 正常 --</option>
            <option value="0" <?php if(($_POST['status']) == "0"): ?>selected<?php endif; ?> >-- 故障 --</option>
            <option value="2" <?php if(($_POST['status']) == "2"): ?>selected<?php endif; ?> >-- 离线 --</option>
        </select>
        <input type="submit" value="搜索" class="btn btn-primary">
    </form>

    <form method="post" class="js-ajax-form" >
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>设备唯一码</th>
                <th>设备号</th>
                <th>设备所在地</th>
                <!--<th>库存</th>-->
                <!--<th>预警库存</th>-->
                <!--<th>产品名称</th>-->
                <!--<th>产品价格（元）</th>-->
                <th>绑定房间</th>
                <th>设备状态</th>
                <th>创建时间</th>
                <th align="center"><?php echo L('ACTIONS');?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($device_list)): foreach($device_list as $key=>$vo): ?><tr>
                    <td><?php echo ($vo['device_unique_code']); ?></td>
                    <td><?php echo ($vo['deveci_no']); ?></td>
                    <td><?php echo ($vo['addr']); ?></td>
                    <!--<td><?php echo ($vo['device_stock']); ?></td>-->
                    <!--<td><?php echo ($vo['device_stock_predict']); ?></td>-->
                    <!--<td><?php echo ($vo['giftname']); ?></td>-->
                    <!--<td><?php echo ($vo['cost']); ?></td>-->
                    <td><?php echo ($vo['room_no']); ?>&nbsp;&nbsp; <?php if(!empty($vo["room_name"])): ?>【<?php echo ($vo['room_name']); ?>】<?php else: ?>暂未被房间绑定，可前往房间管理进行绑定<?php endif; ?></td>
                    <td <?php if(($vo["status"]) == "1"): ?>class="text-success"<?php else: ?>class="text-error"<?php endif; ?> ><strong><?php echo ($statusArr[$vo['status']]); ?></strong></td>
                    <td><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>

                    <td align="center">
                        <a href="<?php echo U('Device/device_edit',array('id'=>$vo['id']));?>" >编辑</a> |
                        <a href="<?php echo U('Device/device_del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a> |
                        <a href="<?php echo U('Device/device_fault',array('deveci_no'=>$vo['device_unique_code']));?>">故障记录</a>
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