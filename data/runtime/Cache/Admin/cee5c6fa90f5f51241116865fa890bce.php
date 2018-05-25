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
    <li><a href="<?php echo U('Activeconfig/timing_list');?>">定时活动列表</a></li>
    <li class="active"><a href="javascript:;">添加定时活动</a></li>
  </ul>
  <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Activeconfig/set_timing');?>">
    <fieldset>
      <input type="hidden" name="id" value="<?php echo ($arrAct['id']); ?>">
      <div class="control-group">
        <label class="control-label">活动标题</label>
        <div class="controls">
          <input type="text" name="title" value="<?php echo ($arrAct['title']); ?>">
          <span class="form-required">*</span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label">活动简介</label>
        <div class="controls">
          <textarea name="about" style="width: 400px;height:150px;"><?php echo ($arrAct['about']); ?></textarea>
          <span class="form-required">*</span>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label">活动时间</label>
        <div class="controls">
          <input class="js-date date" style="width:100px;" type="text" name="start_date" <?php if($arrAct['start_date'] != ''): ?>value="<?php echo (date("Y-m-d",$arrAct['start_date'])); ?>"<?php endif; ?> placeholder="开始日期">
          至
          <input class="js-date date" style="width:100px;" type="text" name="end_date" <?php if($arrAct['end_date'] != ''): ?>value="<?php echo (date("Y-m-d",$arrAct['end_date'])); ?>"<?php endif; ?> placeholder="结束日期">
          <span class="form-required">*</span>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label">活动类型</label>
        <div class="controls">
          <label class="control-label toggleBtn" style="width:80px;text-align:left;"><input style="margin-top:-2px" type="radio" value="1" name="type" <?php if($arrAct['type'] == '1'): ?>checked<?php endif; ?>>单次</label>
          <label class="control-label toggleBtn" style="width:150px;text-align:left;"><input style="margin-top:-2px" type="radio" value="2" name="type" <?php if($arrAct['type'] != '1'): ?>checked<?php endif; ?>>周期</label>
        </div>
      </div>
      <div class="control-group" id="timeMain" <?php if($arrAct['type'] == '1'): ?>style="display:none;"<?php endif; ?>>
        <label class="control-label"></label>
        <div class="controls" style="border: 2px solid #dce4ec;border-radius:3px;width:416px;padding:10px;box-sizing: border-box">
          <div style="height:30px;">
            <label class="control-label" id="setDay" style="width:60px;text-align:left;"><input style="margin-top:-2px" type="checkbox" value="1" name="is_every_day" <?php if($arrAct['is_every_day'] == '1'): ?>checked<?php endif; ?>>每天</label>
          </div>
          <?php if(is_array($arrDate)): foreach($arrDate as $k=>$v): ?><label class="control-label" style="width:56px;text-align:left;"><input style="margin-top:-2px" type="checkbox" class="week_list" value="<?php echo ($k+1); ?>" <?php if(strpos($arrAct['str_week'], strval($k+1)) !== false): ?>checked="checked"<?php endif; ?> name="week[]"><?php echo ($v); ?></label><?php endforeach; endif; ?>
          <div style="clear:both"></div>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label">每位用户获得币</label>
        <div class="controls">
          <input type="text" name="coin" value="<?php echo ($arrAct['coin']); ?>">
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
<script>

  // 选择参与用户切换界面
  $('.toggleBtn').click(function(){
    var type = $(this).children('input').val();
    if(parseInt(type) == 1){
      $('#timeMain').css('display', 'none');
    }else{
      $('#timeMain').css('display', 'block');
    }
  });
  // 选择每天则禁用下面选择周几
  $('#setDay').click(function(){
    if($(this).children('input').is(':checked')){
      $('.week_list').attr('disabled', true);
      $('.week_list').attr('checked', false);
    }else{
      $('.week_list').attr('disabled', false);
    }
  });

  <?php if($arrAct['is_every_day'] == '1'): ?>var s = 1;
  <?php else: ?>
      var s = 0;<?php endif; ?>
  if(s == 1){
    $('.week_list').attr('disabled', true);
    $('.week_list').attr('checked', false);
  }

</script>
</body>
</html>