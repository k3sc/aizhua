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
        <li><a href="<?php echo U('Settingother/index');?>">其他设置</a></li>
        <li class="active"><a href="javascript:;">背景音乐</a></li>
        <li><a href="<?php echo U('Settingother/gameAudio');?>">游戏音效</a></li>
    </ul>
    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th>序号</th>
            <th>标题</th>
            <th>地址</th>
            <th align="center"><?php echo L('ACTIONS');?></th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($row)): foreach($row as $k=>$vo): ?><tr>
                <td><?php echo ($k+1); ?></td>
                <td><?php echo ($vo["title"]); ?></td>
                <td><?php echo ($vo["addr"]); ?></td>
                <td align="center">
                    <a href="<?php echo U('Settingother/del_bgmusic',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
                </td>
            </tr><?php endforeach; endif; ?>
        </tbody>
    </table>

    <form id="import_form" action="<?php echo U('Settingother/upload_bgmusic');?>" method="post" enctype="multipart/form-data" class="well form-search">
        标题：<input id="title" type="text" name="title" placeholder="请输入标题，标题不能为空">
        <a href="javascript:;" id="import" class="btn btn-success btn-sm" style="position: relative">上传背景音乐
            <input type="file" name="importFile" style="position:absolute;width:140px;height:50px;top:0;left:0;margin:0;padding:0;opacity:0;">
        </a>
        <span class="text-error">上传文件需要一定时间，请耐心等待...</span>
    </form>

</div>
<script src="/public/js/common.js"></script>
</body>

<script>
    $(function(){
        if( $("input[name='title']").val() == '' ){
            $("form a").attr('disabled','disabled');
            $("form a").children('input').attr('disabled','disabled');
        }else{
            $("form a").removeAttr('disabled');
            $("form a").children('input').removeAttr('disabled');
        }

        $("input[name='title']").keyup(function(){
            if( $.trim( $(this).val() ) != '' ){
                $("form a").removeAttr('disabled');
                $("form a").children('input').removeAttr('disabled');
            }else{
                $("form a").attr('disabled','disabled');
                $("form a").children('input').attr('disabled','disabled');
            }
        });

        $("#import").change(function(){
            $("#import_form").submit();
        });
    });
</script>

</html>