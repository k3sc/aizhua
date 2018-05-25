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
			<li class="active"><a href="">用户列表</a></li>
			<!--<li><a href="<?php echo U('Users/add');?>">添加用户</a></li>-->
		</ul>
		<form method="post" class="well form-search" action="<?php echo U('Users/index');?>">
			用户名：<input type="text" name="name" value="<?php echo ($name); ?>" style="width: 150px;">&nbsp;&nbsp;&nbsp;
			<!--手机号：<input type="text" name="mobile" value="<?php echo ($mobile); ?>" style="width: 150px;">&nbsp;&nbsp;-->
			注册时间：
			<input type="text" name="start_time" class="js-date date" value="<?php echo ($start_time); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date date" name="end_time" value="<?php echo ($end_time); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;&nbsp;
			用户ID：<input type="text" name="user_id" value="<?php echo ($user_id); ?>">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>
		<form method="post" class="js-ajax-form" action="<?php echo U('Users/index');?>">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>昵称</th>
						<th>头像</th>
						<!--<th>手机号</th>-->
						<th>娃娃币余额</th>
						<th>充值总额</th>
						<!--<th>充值获赠娃娃币</th>-->
						<th>夹娃娃次数</th>
						<th>夹中次数</th>
						<!--<th>剩余甩抓次数</th>-->
						<!--<th>剩余强抓力次数</th>-->
						<th>登录方式</th>
						<th>系统平台</th>
						<th>注册时间</th>
						<th>最后登录时间</th>
						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
						<td><?php echo ($vo["id"]); ?> </td>
						<td align="center"><?php echo ($vo["user_nicename"]); ?></td>
						<td>
							<?php if($vo["avatar"] != ''): ?><img src="<?php echo ($vo["avatar"]); ?>" style="width: 35px;height: 35px;"><?php endif; ?>	
						</td>
						<!--<td><?php echo ($vo["mobile"]); ?></td>-->
						<td><?php echo ($vo["coin"]); ?></td>
						<td><?php echo ($vo["money"]); ?></td>
						<!--<td><?php echo ($vo["coin_sys_give"]); ?></td>-->
						<td><?php echo ($vo["bodyNums"]); ?></td>
						<td><?php echo ($vo["grasp"]); ?></td>
						<!--<td><?php echo ($vo["claw"]); ?></td>-->
						<!--<td><?php echo ($vo["strong"]); ?></td>-->
						<td <?php if(empty($vo["openid"])): ?>class="text-warning"<?php else: ?>class="text-success"<?php endif; ?> >
							<?php if(empty($vo["openid"])): ?>邮箱登录<?php else: ?>微信登录<?php endif; ?>
						</td>
						<td><?php if(($vo["sys"]) == "1"): ?>Android<?php else: ?>iOS<?php endif; ?></td>
						<td><?php echo ($vo["create_time"]); ?></td>
						<td><?php echo ($vo["last_login_time"]); ?></td>
						<td align="center">
							<?php if($vo["user_status"] == 1): ?><a href="<?php echo U('Users/delete',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要封号吗？" style="color: red;">封号</a> | 
							<?php else: ?>
								<a href="<?php echo U('Users/relieve',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要解封吗？" style="color: golden;">解封</a> |<?php endif; ?>
						    <a href="<?php echo U('Users/body',array('id'=>$vo['id']));?>" >娃娃</a> |
							<a href="<?php echo U('Users/express',array('id'=>$vo['id']));?>" >快递</a> |
							<a href="<?php echo U('Users/bill',array('id'=>$vo['id']));?>" >账单</a> |
							<a href="<?php echo U('Users/game_list',array('id'=>$vo['id']));?>">游戏记录</a> |
							<a href="<?php echo U('Users/addr_list',array('id'=>$vo['id']));?>">地址</a> |
							<a href="<?php echo U('Users/service_list',array('id'=>$vo['id']));?>">上报故障记录</a> |
							<a href="<?php echo U('Users/edit_user',array('id'=>$vo['id']));?>">编辑</a> |
							<a href="<?php echo U('Users/msg',array('users'=>$vo['id']));?>">推送</a>
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?> 共<?php echo ($count); ?>条</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
<script>
    $(function(){
        $('.table').tablesort().data('tablesort');
       


    });
</script>
</body>
</html>