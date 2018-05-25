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

        <li class="active"><a href="javascript:;">消息推送</a></li>
    </ul>
    <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('users/msg');?>">
        <fieldset>
		<input type="hidden" name="t" value="<?php echo ($t); ?>">
            <div class="control-group">
                <label class="control-label">推送用户</label>
                <div class="controls">
                    <label class="control-label toggleBtn" style="width:150px;text-align:left;"><input style="margin-top:-2px" type="radio" value="1" name="type" checked >部分用户(id)</label>
                    <label class="control-label toggleBtn" style="width:150px;text-align:left;"><input style="margin-top:-2px" type="radio" value="2" name="type" >所有用户</label>
                </div>
            </div>
            <div class="control-group" id="userMain" style="">
                <label class="control-label"></label>
                <div class="controls">
                    <textarea name="users" id="userList" style="width:400px;height:150px;"> <?php echo ($ids); ?></textarea>
                    <span class="form-required">
              可添加多个用户,以逗号隔开!
              <br><font color="red"><strong>注意：请使用英文逗号，请勿输入多余字符和空格</strong></font>
              <br><font color="green">正确示例：1,2,3,4,5</font>
            </span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">推送标题</label>
                <div class="controls">
                    <input type="text" name="title" value="<?php echo ($msg['title']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">推送内容</label>
                <div class="controls">
                    <textarea name="about" style="width: 400px;height:150px;"><?php echo ($msg['about']); ?></textarea>
                    <span class="form-required">*</span>
                </div>
            </div>

        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary js-ajax-submit" >提交</button>
        </div>
    </form>
</div>
<script src="/public/js/common.js"></script>
</body>
</html>