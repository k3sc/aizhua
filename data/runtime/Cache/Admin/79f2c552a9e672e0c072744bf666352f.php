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



<style>
	.settime{
		float:left;
		width:60px;
		heigth:45px;
		text-align:center;
		line-height:45px;
		border: 1px solid #dce4ec;
		cursor:pointer
	}
	.active1{
		background:#1DCCAA;
		color:#fff;
		border: 1px solid #1DCCAA;
	}
</style>
</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('Statment/index');?>">按时间统计</a></li>
			<li class="active"><a href="javascript:;">按设备统计</a></li>
		</ul>

		<form action="<?php echo U('Statment/from_device');?>" method="post" class="well form-search">
			<div>
				<div class="settime <?php if($time_type == 'day' or ''): ?>active1<?php endif; ?>" ghref="<?php echo ($arrTime['sday']); ?>,<?php echo ($arrTime['eday']); ?>,day">今日</div>
				<div class="settime <?php if($time_type == 'week'): ?>active1<?php endif; ?>" ghref="<?php echo ($arrTime['sweek']); ?>,<?php echo ($arrTime['eweek']); ?>,week">本周</div>
				<div class="settime <?php if($time_type == 'month'): ?>active1<?php endif; ?>" ghref="<?php echo ($arrTime['smonth']); ?>,<?php echo ($arrTime['emonth']); ?>,month">本月</div>
				<div class="settime <?php if($time_type == 'year'): ?>active1<?php endif; ?>" ghref="<?php echo ($arrTime['syear']); ?>,<?php echo ($arrTime['eyear']); ?>,year">半年</div>
				<input type="hidden" id="gettime" name="gettime" value="<?php echo $arrTime['s'.$time_type].','.$arrTime['e'.$time_type]; ?>,<?php echo ($time_type); ?>">
			</div>
			&nbsp;&nbsp;&nbsp;
			<input type="text" name="date1" class="js-date date" placeholder="选择日期" style="width: 100px;" value="<?php echo ($_POST['date1']); ?>" > 至 <input type="text" name="date2" class="js-date date" placeholder="选择日期" style="width: 100px;" value="<?php echo ($_POST['date2']); ?>" >
			<!--消费额区间：<input type="number" value="<?php echo ($_POST['money1']); ?>" name="money1" style="width: 80px;">-->
			<!--至-->
			<!--<input type="number" value="<?php echo ($_POST['money2']); ?>" name="moeny2" style="width: 80px;">-->
			<!--设备唯一码：<input type="text" name="device_uni" value="<?php echo ($_POST['device_uni']); ?>" style="width: 80px;">-->
			<!--设备号：<input type="text" name="device_no" value="<?php echo ($_POST['device_no']); ?>" style="width: 80px;">-->
			<br><br>
			<button class="btn btn-primary">搜索</button>
		</form>

		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>房间号</th>
					<th>房间名</th>
					<th>设备编号</th>
					<th>房间库存</th>
					<th>所在地</th>
					<!-- <th>商品号</th> -->
					<th>商品名</th>
					<th>价格</th>
					<th>消费总额</th>
					<th>消费增币</th>
					<th>夹娃娃次数</th>
					<th>夹中次数</th>
					<th>夹中概率</th>
					<th>强抓力次数</th>
					<th>游戏用户数</th>
					<th>夹中用户</th>
				</tr>
			</thead>
			<tbody>
			<?php if(is_array($result)): foreach($result as $k=>$v): if(!empty($v["room_no"])): ?><tr>
					<td align="center"><?php echo ($v['room_no']); ?></td>
					<td align="center"><?php echo ($v['room_name']); ?></td>
					<td align="center"><?php echo ($v['deveci_no']); ?></td>
					<td align="center"><?php echo ((isset($v['wawa_num']) && ($v['wawa_num'] !== ""))?($v['wawa_num']):0); ?></td>
					<td align="center"><?php echo ($v['addr']); ?></td>
					<!-- <td align="center"><?php echo ($v['wawa_no']); ?></td> -->
					<td align="center"><?php echo ($v['giftname']); ?>( ID:<?php echo ($v['gift_id']); ?> )</td>
					<td align="center"><?php echo ((isset($v['spendcoin']) && ($v['spendcoin'] !== ""))?($v['spendcoin']):0); ?></td>
					<td align="center"><?php echo ((isset($v['xfze']) && ($v['xfze'] !== ""))?($v['xfze']):0); ?></td>
					<td align="center"><?php echo ((isset($v['xfzb']) && ($v['xfzb'] !== ""))?($v['xfzb']):0); ?></td>
					<td align="center"><?php echo ((isset($v['games_total']) && ($v['games_total'] !== ""))?($v['games_total']):0); ?></td>
					<td align="center"><?php echo ((isset($v['ggames_total']) && ($v['ggames_total'] !== ""))?($v['ggames_total']):0); ?></td>
					<td align="center"><?php echo (round($v['ggames_total']/$v['games_total']*100,2)); ?>%</td>
					<td align="center"><?php echo ($v['strong_total']); ?></td>
					<td align="center"><?php echo ((isset($v['game_users']) && ($v['game_users'] !== ""))?($v['game_users']):0); ?></td>
					<td align="center"><?php echo ((isset($v['zgame_users']) && ($v['zgame_users'] !== ""))?($v['zgame_users']):0); ?></td>
				</tr><?php endif; endforeach; endif; ?>
			</tbody>
			<div class="pagination"><?php echo ($page); ?></div>
		</table>
	</div>
	<script src="/public/js/common.js"></script>
</body>

<script>
$(function(){
	$('.table').tablesort().data('tablesort');
	$('.table th').data('sortBy', function(th, td, sorter) {
		return Math.abs(parseInt(td.text(), 10));
	});

	$('.settime').click(function(){
		$('#gettime').val($(this).attr('ghref'));
		$(this).addClass('active1');
		$(this).siblings('div').removeClass('active1');
	});

	$("#clear").click(function(){
		var flag = confirm('确定吗？');
		if( flag ){
			$.ajax({
				url : "<?php echo U('Notice/clear');?>",
				type : 'post',
				data : null,
				success : function(e){
					window.location.href = "<?php echo U('Notice/index');?>";
				},
				error : function(e){
						alert('系统内部错误');
				}
			});
		}
	});

});
</script>
</html>