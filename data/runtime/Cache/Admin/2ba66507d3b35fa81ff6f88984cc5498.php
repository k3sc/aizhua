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
    <li class="active"><a href="javascript:;">其他活动设置</a></li>
  </ul>
  <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Activeconfig/other');?>">
    <fieldset>
      <input type="hidden" name="id" value="<?php echo ($arrAct['id']); ?>">

      <div class="control-group">
        <label class="control-label">邀请码功能开关</label>
        <div class="controls">
          <label class="control-label" style="width:60px;text-align:left;"><input style="margin-top:-2px" type="radio" value="1" name="invitation_code" <?php if($arrAct['invitation_code'] == '1'): ?>checked<?php endif; ?>>开启</label>
          <label class="control-label" style="width:60px;text-align:left;"><input style="margin-top:-2px" type="radio" value="2" name="invitation_code" <?php if($arrAct['invitation_code'] == '2'): ?>checked<?php endif; ?>>关闭</label>
        </div>
      </div>

      <div style="height:1px;width:100%;background:#dce4ec;margin-bottom:15px;"></div>

      <div class="control-group">
        <label class="control-label">注册送娃娃币数量</label>
        <div class="controls">
          <input type="text" name="register_coin" value="<?php echo ($arrAct['register_coin']); ?>">
          <span class="form-required">*</span>
        </div>
      </div>

      <div style="height:1px;width:100%;background:#dce4ec;margin-bottom:15px;"></div>

      <div class="control-group">
        <label class="control-label">首冲活动</label>
        <div class="controls">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label">首次充值</label>
        <div class="controls"><input type="text" style="width:120px;" name="first_pay" value="<?php echo ($arrAct['first_pay']); ?>"> 元以上可额外获得 <input style="width:120px;" type="text" name="first_pay_coin" value="<?php echo ($arrAct['first_pay_coin']); ?>">娃娃币
          <span class="form-required">*</span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label">活动时间</label>
        <div class="controls"><input class="js-date date" type="text" style="width:120px;" name="sdate" value="<?php echo (date("Y-m-d",$arrAct['sdate'])); ?>"> 至 <input class="js-date date" style="width:120px;" type="text" name="edate" value="<?php echo (date("Y-m-d",$arrAct['edate'])); ?>">
          <span class="form-required">*</span>
        </div>
      </div>

    </fieldset>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary js-ajax-submit">提交</button>
    </div>
  </form>
</div>
<script src="/public/js/common.js"></script>
</body>
</html>