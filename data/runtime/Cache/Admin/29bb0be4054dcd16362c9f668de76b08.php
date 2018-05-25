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
        <li ><a href="<?php echo U('Device/device_list');?>">设备列表</a></li>
        <li class="active"><a >编辑</a></li>
    </ul>
    <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Device/device_edit_post',array('id'=>$device['id']));?>">
        <fieldset>

            <div class="control-group">
                <label class="control-label">设备唯一码</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($device['device_unique_code']); ?>" readonly>
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">设备编号</label>
                <div class="controls">
                    <input type="text" name="deveci_no" value="<?php echo ($device['deveci_no']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">所在地</label>
                <div class="controls">
                    <select name="device_addr_id">
                        <?php if(is_array($addr)): foreach($addr as $key=>$vo): ?><option <?php if(($vo["id"]) == $device["device_addr_id"]): ?>selected<?php endif; ?> value="<?php echo ($vo['id']); ?>"><?php echo ($vo['addr']); ?></option><?php endforeach; endif; ?>
                    </select>
                    <span class="form-required">*</span>
                </div>
            </div>
            <!--<div class="control-group">-->
                <!--<label class="control-label">选择娃娃</label>-->
                <!--<div class="controls">-->
                    <!--<select name="wawa_id">-->
                        <!--<?php if(is_array($wawa)): foreach($wawa as $key=>$vo): ?>-->
                            <!--<option <?php if(($vo["id"]) == $device["wawa_id"]): ?>selected<?php endif; ?> value="<?php echo ($vo['id']); ?>"><?php echo ($vo['giftname']); ?></option>-->
                        <!--<?php endforeach; endif; ?>-->
                    <!--</select>-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">库存</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" name="device_stock" value="<?php echo ($device['device_stock']); ?>">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">预警库存</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" name="device_stock_predict" value="<?php echo ($device['device_stock_predict']); ?>">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">绑定房间</label>-->
                <!--<div class="controls">-->
                    <!--<select name="room_id">-->
                        <!--<?php if(is_array($room)): foreach($room as $key=>$vo): ?>-->
                            <!--<option <?php if(($vo["id"]) == $device["room_id"]): ?>selected<?php endif; ?> value="<?php echo ($vo['id']); ?>"><?php echo ($vo['room_name']); ?></option>-->
                        <!--<?php endforeach; endif; ?>-->
                    <!--</select>-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->

            <div class="control-group">
                <label class="control-label"><font color="#6495ed"><b>选择游戏参数</b></font></label>
                <div class="controls">
                    <select id="game_config_id" name="game_config_id">
                        <?php if(is_array($game_config)): foreach($game_config as $key=>$vo): ?><option value="<?php echo ($vo['id']); ?>" <?php if(($device["game_config_id"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
                    </select>
                    <!--<input type="button" id="use" class="btn btn-follow" value="应用">-->
                </div>
            </div>

            <!--<div class="control-group">-->
                <!--<label class="control-label">游戏时间</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['game_time']); ?>" name="game_time">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">天车前后速度</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['qh_speed']); ?>" name="qh_speed">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">天车左右速度</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['zy_speed']); ?>" name="zy_speed">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">天车上下速度</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['sx_speed']); ?>" name="sx_speed">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">中奖第一段抓力</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['zj_first_zhuali']); ?>" name="zj_first_zhuali">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">中奖第二段抓力</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['zj_second_zhuali']); ?>" name="zj_second_zhuali">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">指定高度转二段抓力(中奖)</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['zj_top']); ?>" name="zj_top">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">保持抓力时间(中奖)</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['zj_top_time']); ?>" name="zj_top_time">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">不中奖第一段抓力</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['bzj_first_zhuali']); ?>" name="bzj_first_zhuali">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">不中奖第二段抓力</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['bzj_second_zhuali']); ?>" name="bzj_second_zhuali">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">指定高度转二段抓力(不中奖)</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['bzj_top']); ?>" name="bzj_top">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->
            <!--<div class="control-group">-->
                <!--<label class="control-label">保持抓力时间(不中奖)</label>-->
                <!--<div class="controls">-->
                    <!--<input type="text" value="<?php echo ($device['bzj_top_time']); ?>" name="bzj_top_time">-->
                    <!--<span class="form-required">*</span>-->
                <!--</div>-->
            <!--</div>-->


            <input type="hidden" name="device_id" value="<?php echo ($device['id']); ?>">

        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('EDIT');?></button>
            <a class="btn" href="<?php echo U('Device/device_list');?>"><?php echo L('BACK');?></a>
        </div>
    </form>
</div>
<script src="/public/js/common.js"></script>
</body>

<script>
    $(function () {
        $("#use").click(function () {
            var game_config_id = $("#game_config").val();
            $.ajax({
                url : "<?php echo U('Device/response_ajax');?>",
                type : 'post',
                data : 'id='+game_config_id,
                success : function(e){
                    for(var key in e){
                        $("input[name='"+key+"']").val(e[key]);
                    }
                },
                error : function(e){
                    alert('系统内部错误');
                }
            });
        });
    });

</script>

</html>