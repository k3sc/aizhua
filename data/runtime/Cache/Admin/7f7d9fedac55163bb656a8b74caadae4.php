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
        <li class="active"><a href="">房间列表</a></li>
        <li><a href="<?php echo U('Room/add');?>">添加房间</a></li>
    </ul>

    <form method="post" class="well form-search" action="<?php echo U('Room/index');?>">
        房间号：<input type="text" name="room_no" value="<?php echo ($room_no); ?>"  placeholder="请输入房间号">
        房间名：<input type="text" name="room_name" value="<?php echo ($room_name); ?>"  placeholder="请输入房间名">
        状态：
        <select name="status">
            <option value="-1">选择房间状态</option>
            <option value="0" <?php if(($status) == "0"): ?>selected<?php endif; ?> >在线</option>
            <option value="1" <?php if(($status) == "1"): ?>selected<?php endif; ?> >补货</option>
            <option value="2" <?php if(($status) == "2"): ?>selected<?php endif; ?> >维修</option>
            <option value="3" <?php if(($status) == "3"): ?>selected<?php endif; ?> >游戏中</option>
            <option value="4" <?php if(($status) == "4"): ?>selected<?php endif; ?> >离线</option>
        </select>
        上架/下架：
        <select name="is_show">
            <option value="-1">请选择</option>
            <option value="1" <?php if(($is_show) == "1"): ?>selected<?php endif; ?> >上架</option>
            <option value="0" <?php if(($is_show) == "0"): ?>selected<?php endif; ?> >下架</option>
        </select>
        <input type="submit" class="btn btn-primary" value="搜索">
    </form>


    <form class="js-ajax-form" method="post">
        <div class="table-actions">
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('Room/listorders');?>"><?php echo L('SORT');?></button>
        </div>
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>排序值</th>
                <th>房间号</th>
                <th>房间名</th>
                <th>参数配置名</th>
                <th>商品</th>
                <th>价格(娃娃币)</th>
                <th>设备编号</th>
                <th>所在地</th>
                <th>房间库存</th>
                <th>预警库存</th>
                <th>房间状态</th>
                <th>上架/下架</th>
                <th align="center"><?php echo L('ACTIONS');?></th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($row)): foreach($row as $key=>$vo): ?><tr>
                    <td><input name='listorders[<?php echo ($vo["id"]); ?>]' class="input input-order mr5" type='text' size='3' value='<?php echo ($vo["listorder"]); ?>'></td>
                    <td><?php echo ($vo["room_no"]); ?></td>
                    <td><?php echo ($vo["room_name"]); ?></td>
                    <!--<td><a href=""><?php echo ($vo["config_name"]); ?></a></td>-->
                    <td>
                        <button class="btn btn-primary btn-small config_name" data-toggle="modal" data-target="#myModal" data="<?php echo ($vo["fac_id"]); ?>">
                            <?php echo ($vo["config_name"]); ?>
                        </button>
                    </td>
                    <td>
                        <?php echo ($vo["giftname"]); ?>&nbsp;&nbsp;( <?php echo str_pad($vo['wawa_id'],4,"0",STR_PAD_LEFT); ?> )&nbsp;&nbsp;<img align="right" src="<?php echo ($vo["gifticon"]); ?>" width="30px" height="30px">
                    </td>
                    <td><?php echo ($vo["spendcoin"]); ?></td>
                    <td><?php echo ($vo["deveci_no"]); ?></td>
                    <td><?php echo ($vo["addr"]); ?></td>
                    <td><?php echo ($vo["wawa_num"]); ?></td>
                    <td><?php echo ($vo["yj_kc"]); ?></td>
                    <td <?php if(($vo["status"]) == "2"): ?>class="text-error"<?php endif; ?> <?php if(($vo["status"]) == "1"): ?>class="text-warning"<?php endif; ?> <?php if(($vo["status"]) == "0"): ?>class="text-success"<?php endif; ?> >
                    <strong><?php echo ($status_name[$vo['status']]); ?></strong>
                    </td>
                    <td <?php if(($vo["is_show"]) == "1"): ?>class="text-success"<?php else: ?>class="text-error"<?php endif; ?> ><strong><?php echo ($isshow_name[$vo['is_show']]); ?></strong></td>
                    <td align="center">
                        <a href="<?php echo U('Room/edit',array('id'=>$vo['id']));?>" >详细配置</a> |
                        <a href="<?php echo U('Room/del',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要删除吗？">删除</a>
                    </td>
                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>

    </form>


    <!-- 模态框（Modal） -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        游戏参数配置信息
                    </h4>
                </div>
                <div class="modal-body">
                    xxxxx
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <!--<button type="button" class="btn btn-primary">-->
                        <!--提交更改-->
                    <!--</button>-->
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>


    <div class="pagination"><?php echo ($page); ?></div>
</div>
<script src="/public/js/common.js"></script>
</body>
</html>

<script>
    $(function () {
        $(".config_name").click(function () {
            var fac_id = $(this).attr('data');
            $.ajax({
                url : "<?php echo U('Room/get_game_config');?>",
                type : 'post',
                data : 'fac_id='+fac_id,
                dataType : 'json',
                success :function (res) {
                    if( res.status == 1 ){
                        $(".modal h4").eq(0).text(res.row.name);

                        var str = '<table class="table table-hover table-bordered">' +
                                '<tr>' +
                                   '<td>游戏时间</td>'+
                                   '<td>'+res.row.game_time+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>天车前后速度</td>'+
                                    '<td>'+res.row.qh_speed+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>天车左右速度</td>'+
                                    '<td>'+res.row.zy_speed+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>天车上下速度</td>'+
                                    '<td>'+res.row.sx_speed+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>中奖抓力第一段</td>'+
                                    '<td>'+res.row.zj_first_zhuali+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>中奖抓力第二段</td>'+
                                    '<td>'+res.row.zj_second_zhuali+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>爪子上升到指定高度转二段抓力(中奖)</td>'+
                                    '<td>'+res.row.zj_top+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>爪子上到顶继续保持抓力时间(中奖)</td>'+
                                    '<td>'+res.row.zj_top_time+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>不中奖抓力第一段</td>'+
                                    '<td>'+res.row.bzj_first_zhuali+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>不中奖抓力第二段</td>'+
                                    '<td>'+res.row.bzj_second_zhuali+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>爪子上升到指定高度转二段抓力(不中奖)</td>'+
                                    '<td>'+res.row.bzj_top+'</td>'+
                                '</tr>'+
                                '<tr>' +
                                    '<td>爪子上到顶继续保持抓力时间(不中奖)</td>'+
                                    '<td>'+res.row.bzj_top_time+'</td>'+
                                '</tr>'+
                            '</table>';



                        $(".modal .modal-body").html(str);
                    }
                },
                error : function () {
                    return false;
                }
            });
        });
    });
</script>