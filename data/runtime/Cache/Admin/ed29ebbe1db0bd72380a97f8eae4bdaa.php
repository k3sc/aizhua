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
        <li class="active"><a href="">定时活动列表</a></li>
        <li><a href="<?php echo U('Activeconfig/set_timing');?>">添加定时活动</a></li>
    </ul>

    <a href="<?php echo U('Activeconfig/set_timing');?>" class="btn btn-success btn-sm">+ 添加定时活动</a>
    <div style="height:20px;"></div>
    <form method="post" class="well form-search" action="<?php echo U('Activeconfig/timing_list');?>">
        <span>活动时间段：</span>
        <input style="width:100px;margin-top:5px;" type="text" class="js-date date" name="start_date" value="<?php echo ($_POST['start_date']); ?>" placeholder="开始日期">
        至
        <input style="width:100px;margin-top:5px;" type="text" class="js-date date" name="end_date" value="<?php echo ($_POST['end_date']); ?>" placeholder="结束日期">
        <span style="">活动状态：</span>
        <select name="status" style="width:auto;margin-top:5px;">
            <option value="">全部</option>
            <option value="0" <?php if($_POST['status']== '0'): ?>selected="selected"<?php endif; ?>>未启用</option>
            <option value="1" <?php if($_POST['status']== '1'): ?>selected="selected"<?php endif; ?>>进行中</option>
            <option value="2" <?php if($_POST['status']== '2'): ?>selected="selected"<?php endif; ?>>停用</option>
            <option value="3" <?php if($_POST['status']== '3'): ?>selected="selected"<?php endif; ?>>失效</option>
        </select>
        <span style="">活动类型：</span>
        <select name="type" style="width:auto;margin-top:5px;">
            <option value="">选择活动类型</option>
            <option value="1" <?php if($_POST['type']== '1'): ?>selected="selected"<?php endif; ?>>单次</option>
            <option value="2" <?php if($_POST['type']== '2'): ?>selected="selected"<?php endif; ?>>周期</option>
        </select>
        <span style="">活动标题：</span>
        <input type="text" name="title" value="<?php echo ($_POST['title']); ?>" style="width:100px;margin-top:5px;" placeholder="请输入活动标题">
        <span style="">活动ID：</span>
        <input type="text" name="id" value="<?php echo ($_POST['id']); ?>" style="width:100px;margin-top:5px;" placeholder="请输入活动ID">
        <input type="submit" class="btn btn-primary" value="搜索">
    </form>
    <form method="post" class="js-ajax-form" action="<?php echo U('Ads/listorders');?>">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>活动标题</th>
                <th>活动简介</th>
                <th>活动时间范围</th>
                <th>活动类型</th>
                <th>赠送金币</th>
                <th>活动状态</th>
                <th>发布时间</th>
                <th style="text-align:center;">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
                    <td><?php echo ($vo['id']); ?></td>
                    <td><?php echo ($vo['title']); ?></td>
                    <td><?php echo ($vo['about']); ?></td>

                    <td><?php echo (date("Y-m-d",$vo['start_date'])); ?> - <?php echo (date("Y-m-d",$vo['end_date'])); ?></td>
                    <td>
                        <?php if($vo['type'] == 1): ?>单次
                            <?php else: ?>
                            周期<?php endif; ?>
                    </td>

                    <td><?php echo ($vo['coin']); ?></td>
                    <td>
                        <!--<?php if($vo['end_date'] > time()): ?>-->
                            <!--活动中-->
                            <!--<?php if($vo['status'] != 1): ?>-->
                                <!--未启用-->
                            <!--<?php else: ?>-->
                                <!--已启用-->
                            <!--<?php endif; ?>-->
                        <!--<?php else: ?>-->
                            <!--已结束-->
                            <!--<?php if($vo['status'] != 1): ?>-->
                                <!--未启用-->
                            <!--<?php else: ?>-->
                                <!--已启用-->
                            <!--<?php endif; ?>-->
                        <!--<?php endif; ?>-->

                        <?php switch($vo["status"]): case "0": ?>未启用<?php break;?>
                            <?php case "1": ?>进行中<?php break;?>
                            <?php case "2": ?>停用<?php break;?>
                            <?php case "3": ?>失效<?php break; endswitch;?>

                    </td>
                    <td><?php echo (date("Y-m-d H:i:s",$vo['ctime'])); ?></td>
                    <td style="text-align:center;">
                        <?php if($vo['status'] != 1 and $vo["status"] != 3): ?><a href="<?php echo U('Activeconfig/action_timing',array('id'=>$vo['id'], 'status'=>1));?>" class="js-ajax-dialog-btn" data-msg="您确定要启用吗？">启用</a>
                            | <a href="<?php echo U('Activeconfig/set_timing',array('id'=>$vo['id']));?>" >编辑</a>
                        <?php else: ?>
                            <?php if(($vo["status"]) == "1"): ?><a href="<?php echo U('Activeconfig/action_timing',array('id'=>$vo['id'], 'status'=>2));?>" class="js-ajax-dialog-btn" data-msg="您确定要停止吗？">停用 | </a><?php endif; ?>
                             <a href="<?php echo U('Activeconfig/set_timing',array('id'=>$vo['id']));?>" >编辑</a><?php endif; ?>
                        <?php if($vo["status"] != 1): ?><a href="<?php echo U('Activeconfig/delete',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？"> | 删除</a><?php endif; ?>
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