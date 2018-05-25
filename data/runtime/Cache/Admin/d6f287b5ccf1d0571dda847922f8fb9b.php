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
   <!-- <ul class="nav nav-tabs">
        <li class="active"><a href="">上币活动列表</a></li>
        <li><a href="<?php echo U('Activeconfig/add_act');?>">添加上币活动</a></li>
    </ul>

    <a href="<?php echo U('Activeconfig/add_act');?>" class="btn btn-success btn-sm">+ 添加上币活动</a>
    <span class="text-error"><strong>启用上币活动需要一定时间，请耐心等待系统执行完毕...</strong></span>
    <div style="height:20px;"></div>
    -->
    <form method="post" class="well form-search" action="<?php echo U('Users/setwawa');?>">
        <span style="">赠送人id：</span>
        <input type="text" name="ff_id" value="<?php echo ($_GET['ff_id']); ?>" style="width:100px;margin-top:5px;" placeholder="">

        <span style="">赠送人：</span>
        <input type="text" name="ff_nickname" value="<?php echo ($_GET['ff_nickname']); ?>" style="width:100px;margin-top:5px;" placeholder="">

        <span style="">被赠送人id：</span>
        <input type="text" name="tt_id" value="<?php echo ($_GET['tt_id']); ?>" style="width:100px;margin-top:5px;" placeholder="">

        <span style="">被赠送人：</span>
        <input type="text" name="tt_nickname" value="<?php echo ($_GET['tt_nickname']); ?>" style="width:100px;margin-top:5px;" placeholder="">


        <span style="">礼物id：</span>
        <input type="text" name="gg_id" value="<?php echo ($_GET['gg_id']); ?>" style="width:100px;margin-top:5px;" placeholder="">

        <span style="">礼物：</span>
        <input type="text" name="giftname" value="<?php echo ($_GET['giftname']); ?>" style="width:100px;margin-top:5px;" placeholder="">




        <input type="submit" class="btn btn-primary" value="搜索">
    </form>
    <form method="post" class="js-ajax-form" action="<?php echo U('Ads/listorders');?>">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>

                <th>赠送人id</th>
                <th>赠送人昵称</th>
                <th>被赠送人id</th>
                <th>被赠送人昵称</th>
                <th>娃娃id</th>
                <th>娃娃</th>

                <th>时间</th>

            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                    <td><?php echo ($vo['ff_id']); ?></td>
                    <td><?php echo ($vo['ff_nickname']); ?></td>
                    <td><?php echo ($vo['tt_id']); ?></td>
                    <td><?php echo ($vo['tt_nickname']); ?></td>
                    <td><?php echo ($vo['gg_id']); ?></td>
                    <td><?php echo ($vo['giftname']); ?></td>
                    <td><?php echo (date("Y-m-d H:i:s",$vo['ctime'])); ?></td>





                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="pagination"><?php echo ($page); ?></div>
    </form>
</div>
<script src="/public/js/common.js"></script>
</body>
</html>