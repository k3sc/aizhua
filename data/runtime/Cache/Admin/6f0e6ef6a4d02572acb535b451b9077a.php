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
        <li class="active"><a href="">运单列表</a></li>
    </ul>

    <form action="<?php echo U('Waybill/doExport');?>" id="export_form" method="post">
        <span style="float:left;display:none;" id="dateTime">
          <input type="text" name="startTime" class="js-date date" autocomplete="off">
          至
          <input type="text" name="endTime" class="js-date date" autocomplete="off">
        </span>
        <a href="javascript:;" id="export" class="btn btn-sm btn-danger" style="float:left;">- 批量导出待发货订单</a>
        <div style="clear:both"></div>
    </form>

    <form id="import_form" action="<?php echo U('Waybill/import');?>" method="post" enctype="multipart/form-data">
        <a href="javascript:;" id="import" class="btn btn-success btn-sm" style="position: relative">+ 批量发货
            <input type="file" name="importFile" style="position:absolute;width:140px;height:50px;top:0;left:0;margin:0;padding:0;opacity:0;">
        </a>
    </form>

    <form method="get" class="well form-search" action="<?php echo U('Waybill/lists');?>">
        <input type="hidden" name="g" value="Admin">
        <input type="hidden" name="m" value="Waybill">
        <input type="hidden" name="a" value="lists">
        按单号查询：<input type="text" name="keyword" value="<?php echo ($keyword); ?>"  placeholder="请输入运单号或快递单号">
        按运单状态查询：
        <select name="status">
            <option value="0">选择运单状态</option>
            <?php if(is_array($status_name)): foreach($status_name as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if(($_GET['status']) == $k): ?>selected<?php endif; ?> ><?php echo ($vo); ?></option><?php endforeach; endif; ?>
        </select>
        收件人姓名：<input type="text" name="username" value="<?php echo ($username); ?>">
        收件人手机号：<input type="text" name="mobile" value="<?php echo ($mobile); ?>">
        用户ID：<input type="text" name="uid" value="<?php echo ($uid); ?>">
        <input type="submit" class="btn btn-primary" value="搜索">
    </form>

    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th align="center">运单号</th>
            <th>寄件人(昵称，ID)</th>
            <th>物品明细</th>
            <th>收货人</th>
            <th>地址</th>
            <th>手机号</th>
            <th>物品名称</th>
            <th>物品数量</th>
            <th>邮寄备注</th>
            <th>快递公司</th>
            <th>快递单号</th>
            <th>运单状态</th>
            <th>系统备注</th>
            <th>时间</th>
            <th align="center"><?php echo L('ACTIONS');?></th>
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($data)): foreach($data as $key=>$vo): ?><tr>
                <td align="center"><strong><?php echo ($vo['waybillno']); ?></strong></td>
                <td><?php echo ($vo['user_nicename']); ?>&nbsp;&nbsp;(<?php echo ($vo['user_id']); ?>)</td>
                <td>
                    <?php if(is_array($vo["goods"])): foreach($vo["goods"] as $key=>$v): if(empty($v["wawa_id"])): echo ($v['name']); ?> ( <?php echo ($v["gift_id"]); ?> ) X <?php echo ($v['num']); ?><br>
                            <?php else: ?>
                            <?php echo ($v['name']); ?> ( <?php echo ($v["wawa_id"]); ?> ) X <?php echo ($v['num']); ?><br><?php endif; endforeach; endif; ?>
                </td>
                <td><?php if(empty($vo["uname"])): ?>-<?php else: echo ($vo['uname']); endif; ?></td>
                <td>
                    <?php if(empty($vo["addr"])): ?>-
                        <?php else: ?>
                        <a data="<?php echo ($vo["addr"]); ?>" href="javascript:;" data-toggle="modal" data-target="#modal">
                            <?php echo mb_substr($vo['addr'],0,6,'utf-8'),' ...'; ?>
                        </a><?php endif; ?>
                </td>
                <td><?php if(empty($vo["phone"])): ?>-<?php else: echo ($vo['phone']); endif; ?></td>
                <td><?php echo ($vo['goodsname']); ?></td>
                <td><?php echo ($vo['num']); ?></td>
                <td>
                    <?php if(empty($vo["remark"])): ?>-
                        <?php else: ?>
                        <a data="<?php echo ($vo["remark"]); ?>" href="javascript:;" data-toggle="modal" data-target="#modal">
                            <?php echo mb_substr($vo['remark'],0,6,'utf-8'),' ...'; ?>
                        </a><?php endif; ?>
                </td>
                <td><?php if(empty($vo["kdname"])): ?>-<?php else: echo ($vo['kdname']); endif; ?></td>
                <td><?php if(empty($vo["kdno"])): ?>-<?php else: echo ($vo['kdno']); endif; ?></td>

                <td <?php if(($vo["status"]) == "1"): ?>class="text-error"<?php endif; if(($vo["status"]) == "2"): ?>class="text-warning"<?php endif; if(($vo["status"]) == "3"): ?>class="text-success"<?php endif; ?> >
                    <strong><?php echo ($status_name[$vo['status']]); ?></strong>
                </td>

                <td>
                    <?php if(empty($vo["sys_remark"])): ?>-
                        <?php else: ?>
                        <a data="<?php echo ($vo["sys_remark"]); ?>" href="javascript:;" data-toggle="modal" data-target="#modal">
                            <?php echo mb_substr($vo['sys_remark'],0,6,'utf-8'),' ...'; ?>
                        </a><?php endif; ?>
                </td>
                <?php switch($vo["status"]): case "1": ?><td><?php echo (date('Y-m-d H:i:s',$vo['ctime'])); ?></td><?php break;?>
                    <?php case "2": ?><td><?php echo (date('Y-m-d H:i:s',$vo['fhtime'])); ?></td><?php break;?>
                    <?php case "3": ?><td><?php echo (date('Y-m-d H:i:s',$vo['shtime'])); ?></td><?php break;?>
                    <?php default: ?><td>-</td><?php endswitch;?>

                <td align="center">
                    <!--<a href="<?php echo U('Waybill/del',array('id'=>$vo['waybill_id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">编辑</a> &nbsp;-->
                    <a href="<?php echo U('Waybill/edit',array('waybillno'=>$vo['waybillno']));?>" >编辑</a> &nbsp;
                    <!--<?php if(($vo["status"]) == "1"): ?>-->
                        <!--<a href="<?php echo U('Waybill/fahuo',array('waybillno'=>$vo['waybillno']));?>" class="btn btn-danger">发货</a>-->
                    <!--<?php endif; ?>-->
                    <!--<?php if(($vo["status"]) == "2"): ?>-->
                        <!--<input id="p" class="btn btn-success" type="button" onclick="prints()" value="打印运单">-->
                    <!--<?php endif; ?>-->
                </td>
            </tr><?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="pagination"><?php echo ($page); ?></div>
</div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal_title">详情</h4>
            </div>
            <div class="modal-body" id="modal_content">
                some content...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="modal_close">关闭</button>
                <!--<button type="button" class="btn btn-primary" id="modal_sbt">提交更改</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<!--<script src="/public/js/jqprint/jquery-1.4.4.min.js"></script>-->
<script src="/public/js/common.js"></script>
<script src="/public/js/jqprint/jquery.jqprint.js"></script>

</body>

<script>
    $(function () {
        $("table tbody tr td a").click(function () {
            var content = $(this).attr('data');
            $("#modal_content").text(content);
        });
    });
</script>


<script>
    $(function(){
        // 导出csv数据
        $("#export").click(function(){
            // 隐藏则显示
            if ($("#dateTime").is(":hidden")) {
                $("#dateTime").css('display', 'block');
                $('input[name="startTime"]').val('<?php echo ($smarty["get"]["createtime1"]); ?>');
                $('input[name="endTime"]').val('<?php echo ($smarty["get"]["createtime2"]); ?>');
                // 显示则获取时间范围,然后导出
            } else {
                var startTime = $.trim($('input[name="startTime"]').val());
                var endTime = $.trim($('input[name="endTime"]').val());
                if (startTime != '' && endTime != '') {
                    $("#export_form").submit();
                }
            }
        });
    });
</script>

<script>
    function prints(){
        var waybillno = $("#p").parents('td').siblings('td').eq(0).text();
        location.href = "/index.php?g=admin&m=waybill&a=prints&waybillno="+waybillno;
    }
</script>

<script>
    $(function(){
        $("#import").change(function(){
            $("#import_form").submit();
        });
    });
</script>


</html>