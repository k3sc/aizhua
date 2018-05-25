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
.home_info li em {
	float: left;
	width: 120px;
	font-style: normal;
}
li {
	list-style: none;
}
</style>
<link href="/public/simpleboot/css/admin.css"  rel="stylesheet" type="text/css">
<script src="/public/js/highcharts.js"></script>
</head>
<body>
	<div class="admin_index" style="width:100%;padding:10px;">
		<div>
			<form class="well form-search" name="form1" method="post" style="float:left" action="/index.php?g=admin&m=statmentsimp&a=index">
				日期：<input class="js-date date" type="text" name="date" style="width: 200px;" value="<?php echo ($_POST['date']); ?>" placeholder="日期"><input type="button" class="btn btn-primary" value="搜索" onclick="form1.submit();">
			</form>
		</div>
		<div class="layer" style="clear:both;margin:0;padding:0">
			<div style="width: 100%;overflow: scroll">
				<div id="container1" style="width:30000px;height:220px"></div>
			</div>


			<div style="width: 100%;overflow: scroll">
				<div id="container2" style="width:30000px;height:220px"></div>
			</div>

			<div style="width: 100%;overflow: scroll">
				<div id="container3" style="width:30000px;height:220px"></div>
			</div>
		</div>
	</div>
	<script src="/public/js/common.js"></script>
	<script>
    $(function () {
      $('#container1').highcharts({
        chart: {
          type: 'line'
        },
        title: {
          text: '今天用户实时在线情况'
        },
        subtitle: {
          text: ''
        },
        xAxis: {
          categories: [<?php if(is_array($gArrOnline)): foreach($gArrOnline as $k=>$v): ?>'<?php echo floor($k/60); ?>时<?php echo ($k%60); ?>分',<?php endforeach; endif; ?>]
        },
        yAxis: {
          title: {
            text: '在线人数'
          }
        },
        plotOptions: {
          line: {
            dataLabels: {
              enabled: true          // 开启数据标签
            },
            enableMouseTracking: false // 关闭鼠标跟踪，对应的提示框、点击事件会失效
          }
        },
        series: [{
          name: '在线用户数',
          data: [<?php if(is_array($gArrOnline)): foreach($gArrOnline as $k=>$v): echo ($v); ?>,<?php endforeach; endif; ?>]
        }]
      });
    });

		// 今天夹娃娃次数实时情况
    $(function () {
      $('#container2').highcharts({
        chart: {
          type: 'line'
        },
        title: {
          text: '今天夹娃娃次数实时情况'
        },
        subtitle: {
          text: ''
        },
        xAxis: {
          categories: [<?php if(is_array($gArrNums)): foreach($gArrNums as $k=>$v): ?>'<?php echo floor($k/60); ?>时<?php echo ($k%60); ?>分',<?php endforeach; endif; ?>]
        },
        yAxis: {
          title: {
            text: '夹娃娃次数'
          }
        },
        plotOptions: {
          line: {
            dataLabels: {
              enabled: true          // 开启数据标签
            },
            enableMouseTracking: false // 关闭鼠标跟踪，对应的提示框、点击事件会失效
          }
        },
        series: [{
          name: '夹娃娃次数',
          data: [<?php if(is_array($gArrNums)): foreach($gArrNums as $k=>$v): echo ($v); ?>,<?php endforeach; endif; ?>]
        }]
      });
    });

    // 今天夹中娃娃次数实时情况
    $(function () {
      $('#container3').highcharts({
        chart: {
          type: 'line'
        },
        title: {
          text: '今天夹中娃娃次数实时情况'
        },
        subtitle: {
          text: ''
        },
        xAxis: {
          categories: [<?php if(is_array($gArrgNums)): foreach($gArrgNums as $k=>$v): ?>'<?php echo floor($k/60); ?>时<?php echo ($k%60); ?>分',<?php endforeach; endif; ?>]
        },
        yAxis: {
          title: {
            text: '夹中娃娃次数'
          }
        },
        plotOptions: {
          line: {
            dataLabels: {
              enabled: true          // 开启数据标签
            },
            enableMouseTracking: false // 关闭鼠标跟踪，对应的提示框、点击事件会失效
          }
        },
        series: [{
          name: '夹中娃娃次数',
          data: [<?php if(is_array($gArrgNums)): foreach($gArrgNums as $k=>$v): echo ($v); ?>,<?php endforeach; endif; ?>]
        }]
      });
    });
	</script>
</body>
</html>