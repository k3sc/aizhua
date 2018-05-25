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
			<li><a href="<?php echo U('Chargerules/index');?>">充值规则列表</a></li>
			<li class="active"><a >充值规则修改</a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Chargerules/do_edit');?>">
		   <input type="hidden" name="id" value="<?php echo ($rules['id']); ?>">
			<fieldset>
				<div class="control-group">
					<label class="control-label">充值金额</label>
					<div class="controls">
						<input type="text" name="money" value="<?php echo ($rules['money']); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">充值娃娃币</label>
					<div class="controls">
						<input type="text" name="coin" value="<?php echo ($rules['coin']); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">赠送娃娃币</label>
					<div class="controls">
						<input type="text" name="give_coin" value="<?php echo ($rules['give_coin']); ?>">
						<span class="form-required">*</span>
					</div>
				</div>

				<div class="control-group" id="gvie">
					<label class="control-label">赠送的礼品</label>
					<div class="controls">
						<?php echo ($gift); ?>
					</div>
				</div><div class="control-group">
					<label class="control-label">赠送的礼品的数量</label>
					<div class="controls">
						<input type="text" name="number" value="<?php echo ($rules['number']); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group" id="claw">
					<label class="control-label">赠送甩爪次数</label>
					<div class="controls">
						<input type="text" name="claw" value="<?php echo ($rules['claw']); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
                <!--<div class="control-group">-->
					<!--<label class="control-label">首充送娃娃币</label>-->
					<!--<div class="controls">-->
						<!--<input type="text" name="firstgive" value="<?php echo ($rules['firstgive']); ?>">-->
						<!--<span class="form-required">*</span>-->
					<!--</div>-->
				<!--</div>-->
			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('EDIT');?></button>
				<a class="btn" href="<?php echo U('Chargerules/index');?>"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
<script>
  	var aa = $('#gvie input[name="give"]:checked ').val();
  	if(aa){
  		$("#claw").show()
  	}else{
  		$('#claw').hide();
  	}
	
	$('.gift').click(function() {
		$("#claw").show()
	});
	

</script>
</html>