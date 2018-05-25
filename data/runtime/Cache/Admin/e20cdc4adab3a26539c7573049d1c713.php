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
        <li ><a href="<?php echo U('Gameconfig/index');?>">游戏参数配置列表</a></li>
        <li class="active"><a >添加</a></li>
    </ul>
    <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Gameconfig/add_post');?>">
        <fieldset>
            <div class="control-group">
                <label class="control-label">参数名</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['name']); ?>" name="name" required>
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">游戏时间</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['game_time']); ?>" name="game_time" required range="0,100">
                    <span class="form-required">*</span>
                (30)</div>
            </div>

            <div class="control-group">
                <label class="control-label"></label>
                <div class="controls text-error">
                    <strong>------------- 天车速度 -------------</strong>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">前后速度</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['qh_speed']); ?>" name="qh_speed" required range="0,100">
                    <span class="form-required">*</span>
                (50)</div>
            </div>
            <div class="control-group">
                <label class="control-label">左右速度</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['zy_speed']); ?>" name="zy_speed" required range="0,100">
                    <span class="form-required">*</span>
                (50)</div>
            </div>
            <div class="control-group">
                <label class="control-label">上下速度</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['sx_speed']); ?>" name="sx_speed" required range="0,100">
                    <span class="form-required">*</span>
                (100)</div>
            </div>
            
             <div class="control-group">
                <label class="control-label"></label>
                <div class="controls text-error">
                    <strong>------------- 天车时间 -------------</strong>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">前后运行时间</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['qh_time']); ?>" name="qh_time" required range="1,100">
                    <span class="form-required">* C2参数1, 单位100ms</span>
                (4)</div>
            </div>
            <div class="control-group">
                <label class="control-label">左右运行时间</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['zy_time']); ?>" name="zy_time" required range="1,100">
                    <span class="form-required">* C2参数2, 单位100ms</span>
                (4)</div>
            </div>

            <div class="control-group">
                <label class="control-label"></label>
                <div class="controls text-error">
                    <strong>------------- 中奖力度 -------------</strong>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">强抓力</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['zj_first_zhuali']); ?>" name="zj_first_zhuali" required range="0,100">
                    <span class="form-required">* C1参数5(60-70)</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">弱抓力</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['zj_second_zhuali']); ?>" name="zj_second_zhuali" required range="0,100">
                    <span class="form-required">* C1参数4(60-70)</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">C1(%)</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['zj_top']); ?>" name="zj_top" required range="0,255">
                    <span class="form-required">* C1参数6</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">C2(ms)</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['zj_top_time']); ?>" name="zj_top_time" required range="0,100">
                    <span class="form-required">* C2参数6, 单位10ms</span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label"></label>
                <div class="controls text-error">
                    <strong>------------ 不中奖力度 ------------</strong>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">强抓力</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['bzj_first_zhuali']); ?>" name="bzj_first_zhuali" required range="0,100">
                    <span class="form-required">* C1参数5(60-70)</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">弱抓力</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['bzj_second_zhuali']); ?>" name="bzj_second_zhuali" required range="0,100">
                    <span class="form-required">* C1参数4(25以下)</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">C1(%)</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['bzj_top']); ?>" name="bzj_top" required range="0,255">
                    <span class="form-required">* C1参数6</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">C2(ms)</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($gameconfig['bzj_top_time']); ?>" name="bzj_top_time" required range="0,100">
                    <span class="form-required">* C2参数6, 单位10ms</span>
                </div>
            </div>

        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('ADD');?></button>
            <a class="btn" href="<?php echo U('Gameconfig/index');?>"><?php echo L('BACK');?></a>
        </div>
    </form>
</div>
<script src="/public/js/common.js"></script>
</body>

<script>
    $(function () {
        $('form.js-ajax-form').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: true,
            rules: {

            }
        });

    });
</script>

</html>