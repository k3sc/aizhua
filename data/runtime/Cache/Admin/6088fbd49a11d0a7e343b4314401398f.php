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
    <form method="post" class="well form-search" action="<?php echo U('Users/msglist');?>">
        <span>推送时间：</span>
        <input class="js-date date" style="width:100px;margin-top:5px;" type="text" name="sdate" value="<?php echo ($_POST['sdate']); ?>" placeholder="">
        至
        <input class="js-date date" style="width:100px;margin-top:5px;" type="text" name="edate" value="<?php echo ($_POST['edate']); ?>" placeholder="">
        <span style="">推送方式：</span>
        <select name="type" style="width:auto;margin-top:5px;">
            <option value="">不限</option>
            <option value="1" <?php if($_POST['type']== '1'): ?>selected="selected"<?php endif; ?>>部分</option>
            <option value="2" <?php if($_POST['type']== '2'): ?>selected="selected"<?php endif; ?>>全部</option>
        </select>
        <span style="">推送标题：</span>
        <input type="text" name="title" value="<?php echo ($_POST['title']); ?>" style="width:100px;margin-top:5px;" placeholder="请输入标题">

        <input type="submit" class="btn btn-primary" value="搜索">
    </form>
    <form method="post" class="js-ajax-form" action="<?php echo U('Ads/listorders');?>">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>简介</th>
                <th>推送方式</th>
                <th>参与用户</th>

                <th>推送时间</th>

            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                    <td><?php echo ($vo['id']); ?></td>
                    <td><?php echo ($vo['title']); ?></td>
                    <td><?php echo ($vo['about']); ?></td>
                    <td><?php if($vo['type'] == 1): ?>部分
                        <?php else: ?>
                        全部<?php endif; ?></td>
                    <td>
                    <?php if($vo['type'] == 1): echo ($vo['users']); ?>
                    <?php else: ?>
                        所有用户<?php endif; ?>
                    </td>


                    <td><?php echo (date("Y-m-d H:i:s",$vo['created_at'])); ?></td>

                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="pagination"><?php echo ($page); ?></div>
    </form>
</div>
<script src="/public/js/common.js"></script>
</body>
</html>