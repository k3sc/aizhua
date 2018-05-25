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
        <li ><a href="<?php echo U('Users/index');?>">用户列表</a></li>
        <li class="active"><a>游戏记录列表</a></li>
    </ul>

        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>时间</th>
                <th>产品名</th>
                <th>产品图</th>
                <th>花费娃娃币</th>
                <th>视频</th>
                <th>房间号</th>
                <th>中奖状态</th>
                <th>设备号</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($row)): foreach($row as $key=>$vo): ?><tr>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td><?php echo (date("Y-m-d H:i:s",$vo["ctime"])); ?></td>
                    <td><?php echo ($vo["name"]); ?></td>
                    <td>
                        <?php if($vo["img"] != null): ?><img width="80" src="<?php echo ($vo["img"]); ?>">
                        <?php else: ?>
                            无<?php endif; ?>
                    </td>
                    <td><?php echo ($vo["coin"]); ?></td>
                    <td>
                        <?php if($vo["video"] != null): ?><!--<video width="160" src="<?php echo ($vo["video"]); ?>" controls="controls"></video>-->
                            <a href="<?php echo U('Users/video',array('url'=>$vo['video']));?>" target="_blank" class="btn btn-success">查看视频</a>
                        <?php else: ?>
                            无<?php endif; ?>
                    </td>
                    <td><?php echo ($vo["room_no"]); ?></td>
                    <td>
                        <span><?php if(($vo["success"]) == "0"): ?>失败<?php else: ?>成功<?php endif; ?></span>
                        <input data_id='<?php echo ($vo["id"]); ?>' data_success="<?php echo ($vo["success"]); ?>" type="button" class="btn btn-primary setSuccessBtn" value='<?php if(($vo["success"]) == "0"): ?>设置成功<?php else: ?>设置失败<?php endif; ?>'>
                    </td>
                    <td><?php echo ($vo["fac_id"]); ?></td>
                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="pagination"><?php echo ($page); ?> 共&nbsp;&nbsp;<?php echo ($count); ?>&nbsp;&nbsp;条</div>
</div>
<script>
    $(function(){
      $('.setSuccessBtn').on('click', function(){
        var obj = $(this);
        var id = obj.attr('data_id');
        var success = obj.attr('data_success');

         $.post('<?php echo U("Users/setSuccess");?>', {'id': id, 'success': success}, function(res){
           if( res.res ){
               obj.prev('span').html('成功');
               obj.val('设置失败');
               obj.attr('data_success', res.res);
           }else{
               obj.prev('span').html('失败');
               obj.val('设置成功');
               obj.attr('data_success', res.res);
           }
         },'json');
      });
    });
</script>
<script src="/public/js/common.js"></script>
</body>
</html>