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
			<!--<li ><a href="<?php echo U('Probability/index');?>">所有概率</a></li>-->
			<li class="active"><a href="">房间概率模式</a></li>
			<li ><a href="<?php echo U('Probability/sell');?>">用户贩卖模式</a></li>
		</ul>


		<form method="post" class="well form-search" action="<?php echo U('Probability/room');?>">
			房间号：<input type="text" name="room_no" value="<?php echo ($room_no); ?>"  placeholder="请输入房间号">
			设备号：<input type="text" name="device_no" value="<?php echo ($device_no); ?>"  placeholder="请输入设备号">
			商品名称：<input type="text" name="goodsname" value="<?php echo ($goodsname); ?>"  placeholder="请输入商品名称">
			房间状态：
			<select name="status" style="width: 150px;">
				<option value="-1">-- 选择房间状态 --</option>
				<option value="0" <?php if(($status) == "0"): ?>selected<?php endif; ?> >在线</option>
				<option value="1" <?php if(($status) == "1"): ?>selected<?php endif; ?> >补货</option>
				<option value="2" <?php if(($status) == "2"): ?>selected<?php endif; ?> >维护</option>
				<option value="3" <?php if(($status) == "3"): ?>selected<?php endif; ?> >游戏中</option>
				<option value="4" <?php if(($status) == "4"): ?>selected<?php endif; ?> >离线</option>
			</select>
			<input type="submit" class="btn btn-primary" value="查询">
		</form>



		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th align="center">
						<input type="checkbox" id="id">
						ID
					</th>
					<th>房间名</th>
					<th>设备号</th>
					<th>商品名称</th>
					<th>成本(元)</th>
					<th>游戏价格(娃娃币)</th>
					<th>房间状态</th>
					<th>一周抓中次数</th>
					<th>概率</th>
					<th align="center"><?php echo L('ACTIONS');?></th>
				</tr>
			</thead>
			<tbody>
				<?php if(is_array($row)): foreach($row as $key=>$vo): ?><tr>
					<td align="center">
						<input type="checkbox" class="checkbox_id" value="<?php echo ($vo["id"]); ?>">
						<?php echo ($vo["id"]); ?>
					</td>
					<td><?php echo ($vo["room_no"]); ?></td>
					<td><?php echo ($vo["deveci_no"]); ?></td>
					<td><?php echo ($vo["giftname"]); ?></td>
					<td><?php echo ($vo["cost"]); ?></td>
					<td><?php echo ($vo["spendcoin"]); ?></td>
					<td
						<?php if(($vo["status"]) == "0"): ?>class="text-success"<?php endif; ?>
						<?php if(($vo["status"]) == "1"): ?>class="text-warning"<?php endif; ?>
						<?php if(($vo["status"]) == "2"): ?>class="text-error"<?php endif; ?>
					><strong><?php echo ($statusArr[$vo['status']]); ?></strong></td>
					<td>
						<?php if(empty($vo["week_count"])): ?>0
							<?php else: ?>
							<?php echo ($vo["week_count"]); endif; ?>
					</td>
					<td><?php echo ($vo["claw_count"]); ?></td>
					<td align="center" class="isstart">
						<!--<a href="<?php echo U('Probability/room_edit',array('id'=>$vo['id']));?>">编辑</a>-->

						<a href="javascript:;" data="<?php echo ($vo["id"]); ?>" <?php if(($vo["is_roommodel"]) == "0"): ?>class="btn btn-danger btn-small"<?php else: ?>class="btn btn-success btn-small"<?php endif; ?> ><?php if(($vo["is_roommodel"]) == "0"): ?>停用<?php else: ?>启用<?php endif; ?></a>
					</td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?></div>

		<button id="btn" class="btn btn-primary" data-toggle="modal" data-target="#modal">批量设置概率</button>

	</div>


	<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modal_title">概率设置</h4>
				</div>
				<div class="modal-body" id="modal_content">

					<table>
						<tr>
							<td>抓</td>
							<td><input type="text" name="claw_count" style="width: 50px;"></td>
							<td>次出一次强抓力，直到中奖</td>
						</tr>
					</table>

					<input type="hidden" name="ids" value="">

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" id="modal_close">关闭</button>
					<button type="button" class="btn btn-primary" id="modal_sbt">提交更改</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal -->
	</div>


</body>
<script src="/public/js/common.js"></script>

<script>
	$(document).ready(function () {
		$("#id").click(function () {
			if( $(this).is(":checked") ){
                $('.checkbox_id').attr('checked','checked');
			}else{
                $('.checkbox_id').removeAttr('checked');
			}
        });

		$("#btn").click(function () {
		    var data = new Array();
		    $(".checkbox_id").each(function () {
                if( $(this).is(':checked') ) {
                    data.push($(this).val())
                }
            });
		    
		    if( !data.length ){
		        alert('没有可操作的数据');
		        return false;
			}
			$("input[name='ids']").val(data);

        });


		$("#modal_sbt").click(function () {
		    var ids = $("input[name='ids']").val();
		    var week_count = $("input[name='week_count']").val();
		    var claw_count = $("input[name='claw_count']").val();

            $.ajax({
				url : "<?php echo U('Probability/batch_setroom');?>",
				type : 'post',
				data : 'ids='+ids+'&week_count='+week_count+'&claw_count='+claw_count,
				dataType : 'json',
				success : function (res) {
					if( res.status == 1 ){
                        $("#modal_content").text('操作成功');
                        $("#modal_sbt").hide();
					}else{
                        $("#modal_content").text('操作失败');
                        $("#modal_sbt").hide();
					}
                },
				error : function () {
					alert('错误');
                }
			});
        });

		$("#modal_close").click(function () {location.reload();});

        $(".isstart a").click(function () {
            var id = $(this).attr('data');
            $.ajax({
                url : "<?php echo U('Probability/isstart_room');?>",
                type : 'post',
                dataType : 'json',
                data : 'id='+id,
                success : function (res) {
                    if( res.status == 1 ){
                        location.reload();
                    }
                },
                error : function () {
                    alert('错误');
                }
            });
        });

    });

</script>


</html>