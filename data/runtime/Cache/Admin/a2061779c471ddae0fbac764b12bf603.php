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
			<li class="active"><a href="">反馈列表</a></li>
		</ul>

		<!--<form id="export_form" action="<?php echo U('Feedback/export');?>" method="post" enctype="multipart/form-data">-->
			<!--<a href="javascript:;" id="export" class="btn btn-success btn-sm">导出数据 +-->
				<!--<input type="file" name="feedback_data" style="position:absolute;width:130px;height:120px;top:0;left:0;margin:0;padding:0;opacity:0;">-->
			<!--</a>-->
		<!--</form>-->

		<form action="<?php echo U('Feedback/export');?>" id="export_form" method="post">
			<div class="">
				  <span style="float:left;display:none;" id="dateTime">
					<input type="text" name="startTime" class="js-date date" autocomplete="off">
					至
					<input type="text" name="endTime" class="js-date date" autocomplete="off">
				  </span>
					<a href="javascript:;" id="export" class="btn btn-sm btn-danger" style="float:left;margin-left:8px;">导出数据</a>
					<div style="clear:both"></div>
			</div>
		</form>


		<form class="well form-search" method="post" action="<?php echo U('Feedback/index');?>">
		  状态：
			<select class="select_2" name="status">
				<option value="">全部</option>
				<option value="0" <?php if($formget["status"] == '0'): ?>selected<?php endif; ?> >处理中</option>
				<option value="1" <?php if($formget["status"] == '1'): ?>selected<?php endif; ?> >已处理</option>			
			</select>
			提交时间：
			<input type="text" name="start_time" class="js-date date" value="<?php echo ($formget["start_time"]); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			用户ID：
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入会员ID">
			联系方式：
			<input type="text" name="contact" value="<?php echo ($formget["contact"]); ?>" placeholder="请输入手机号码或邮箱">
			反馈内容：
			<input type="text" name="content" value="<?php echo ($formget["content"]); ?>" placeholder="请输入反馈内容">
			<input type="submit" class="btn btn-primary" value="搜索">
		</form>				
		<form method="post" class="js-ajax-form" >

		
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th>会员</th>						
						<th>联系方式</th>
						<th>系统版本</th>
						<th>手机型号</th>
						<th>APP版本</th>
						<th>反馈信息</th>
						<th>状态</th>
						<th>提交时间</th>
						<th>处理时间</th>

						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
					<?php $status=array("0"=>"处理中","1"=>"已处理", "2"=>"审核失败"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td><?php echo ($vo['userinfo']['user_nicename']); ?> ( <?php echo ($vo['uid']); ?> )</td>	
						<td><?php echo ($vo['contact']); ?></td>
						<td><?php echo ($vo['version']); ?></td>
						<td><?php echo ($vo['model']); ?></td>
						<td><?php echo ($vo['appversion']); ?></td>
						<td>
							<a href="javascript:;" class="detail" data-toggle="modal" data-target="#myModal" data="<?php echo ($vo["content"]); ?>"><?php echo mb_substr($vo['content'],0,25,'utf-8');?></a>
						</td>
						<td <?php if(($vo["status"]) == "0"): ?>class="text-error"<?php else: ?>class="text-success"<?php endif; ?> ><strong><?php echo ($status[$vo['status']]); ?></strong></td>
						<td><?php echo (date("Y-m-d H:i:s",$vo["addtime"])); ?></td>						
						<td>
						 <?php if($vo['status'] == '0'): ?>处理中
						 <?php else: ?>
						     <?php echo (date("Y-m-d H:i:s",$vo["uptime"])); endif; ?>						
						 </td>

						<td align="center">	
							<?php if($vo['status'] == '0'): ?><a class="btn btn-danger btn-small" href="<?php echo U('Feedback/setstatus',array('id'=>$vo['id']));?>" >标记处理</a>  |<?php endif; ?>
							
							<a href="<?php echo U('Feedback/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a> |
							<a href="<?php echo U('Users/msg',array('users'=>$vo['uid'],'t'=>'feedback'));?>">推送</a>
						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>

		</form>
	</div>

	<!-- 模态框（Modal） -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">
						游戏参数配置信息
					</h4>
				</div>
				<div class="modal-body">
					xxxxx
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭
					</button>
					<!--<button type="button" class="btn btn-primary">-->
					<!--提交更改-->
					<!--</button>-->
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal -->
	</div>

</body>
<script src="/public/js/common.js"></script>
<script>
    $(function(){
        // 导出csv数据
        $("#export").click(function(){
			// 隐藏则显示
			if ($("#dateTime").is(":hidden")) {
				$("#dateTime").css('display', 'block');
				$('input[name="startTime"]').val('<?php echo ($smarty["get"]["createtime1"]); ?>');
				$('input[name="endTime"]').val('<?php echo ($smarty["get"]["createtime2"]); ?>');
				// 显示则获取时间范围,然后导出
			} else {
				var startTime = $.trim($('input[name="startTime"]').val());
				var endTime = $.trim($('input[name="endTime"]').val());
				if (startTime != '' && endTime != '') {
                    $("#export_form").submit();
				}
			}
        });

        $(".detail").click(function () {
            var content = $(this).attr('data');
			$("#myModalLabel").text('反馈内容详情');
			$("#myModal .modal-body").html(content);
        });

    });
</script>


</html>