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
			<li><a href="<?php echo U('Room/index');?>">房间列表</a></li>
			<li class="active"><a href="javascript:;">房间详细配置</a></li>
		</ul>
		<form method="post" class="form-horizontal js-ajax-form" enctype="multipart/form-data" action="<?php echo U('Room/edit_post',array('id'=>$row['id']));?>">
			<fieldset>
				<div class="control-group">
					<label class="control-label">房间号</label>
					<div class="controls">
						<input type="text" name="room_no" value="<?php echo ($row["room_no"]); ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">房间名</label>
					<div class="controls">
						<input type="text" name="room_name" value="<?php echo ($row["room_name"]); ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">产品名称</label>
					<div class="controls">
						<select id="gift" name="type_id">
							<?php if(is_array($giftarr)): foreach($giftarr as $key=>$vo): ?><option <?php if(($vo["id"]) == $row["type_id"]): ?>selected<?php endif; ?> value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["giftname"]); ?>( <?php echo str_pad($vo['id'],4,"0",STR_PAD_LEFT); ?> )</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<!--<div class="control-group">-->
					<!--<label class="control-label">产品号</label>-->
					<!--<div class="controls">-->
						<!--<input id="wawa_no" type="text" readonly value="<?php echo ($row["wawa_no"]); ?>">-->
					<!--</div>-->
				<!--</div>-->
				<div class="control-group">
					<label class="control-label">产品图片</label>
					<div class="controls">
						<img id="goodsimg" src="<?php echo ($row["gifticon"]); ?>" style="width: 100px;height: 100px;">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">绑定设备</label>
					<div class="controls">
						<select name="device_id">
							<option value="0">选择设备</option>
							<?php if(is_array($device)): foreach($device as $key=>$vo): ?><option <?php if(($row["device_id"]) == $vo["id"]): ?>selected<?php endif; ?> value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["deveci_no"]); ?></option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">库存</label>
					<div class="controls">
						<input type="text" name="wawa_num" value="<?php echo ($row["wawa_num"]); ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">预警库存</label>
					<div class="controls">
						<input type="text" name="yj_kc" value="<?php echo ($row["yj_kc"]); ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">最多人数</label>
					<div class="controls">
						<input type="text" name="max_user" value="<?php echo ($row["max_user"]); ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">抓中次数</label>
					<div class="controls">
						<input type="text" name="zhuazhong_count" value="<?php echo ($row["zhuazhong_count"]); ?>" readonly>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">抓取总次数</label>
					<div class="controls">
						<input type="text" name="count" value="<?php echo ($row["count"]); ?>" readonly>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">房间状态</label>
					<div class="controls">
						<select name="status">
							<option value="0" <?php if(($row["status"]) == "0"): ?>selected<?php endif; ?> >在线</option>
							<option value="1" <?php if(($row["status"]) == "1"): ?>selected<?php endif; ?> >补货中</option>
							<option value="2" <?php if(($row["status"]) == "2"): ?>selected<?php endif; ?> >维修中</option>
                            <option value="3" <?php if(($row["status"]) == "3"): ?>selected<?php endif; ?> >游戏中</option>
                            <option value="4" <?php if(($row["status"]) == "4"): ?>selected<?php endif; ?> >离线</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">上架/下架</label>
					<div class="controls">
						<select name="is_show">
							<option value="1" <?php if(($row["is_show"]) == "1"): ?>selected<?php endif; ?> >上架</option>
							<option value="0" <?php if(($row["is_show"]) == "0"): ?>selected<?php endif; ?> >下架</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">视频1推流</label>
					<div class="controls">
						<?php echo ($row["video1_push"]); ?>
					</div>
				</div>
                <div class="control-group">
					<label class="control-label">视频1拉流</label>
					<div class="controls">
						<?php echo ($row["video1_pull"]); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">视频2推流</label>
					<div class="controls">
						<?php echo ($row["video2_push"]); ?>
					</div>
				</div>
                <div class="control-group">
					<label class="control-label">视频2拉流</label>
					<div class="controls">
						<?php echo ($row["video2_pull"]); ?>
					</div>
				</div>
                <div class="control-group">
					<label class="control-label">音频推流</label>
					<div class="controls">
						<?php echo ($row["audio_push"]); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">音频拉流</label>
					<div class="controls">
						<?php echo ($row["audio_pull"]); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">背景音乐</label>
					<div class="controls">
						<select name="bgmusic_id">
							<?php if(is_array($bgmusic)): foreach($bgmusic as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($row["bgmusic_id"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?>&nbsp;(<?php echo ($vo["id"]); ?>)</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">按钮音效</label>
					<div class="controls">
						<select name="yx_anniu">
							<?php if(is_array($anniu)): foreach($anniu as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($row["yx_anniu"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?>&nbsp;(<?php echo ($vo["id"]); ?>)</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">成功音效</label>
					<div class="controls">
						<select name="yx_chenggong">
							<?php if(is_array($chenggong)): foreach($chenggong as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($row["yx_chenggong"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?>&nbsp;(<?php echo ($vo["id"]); ?>)</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">倒计时音效</label>
					<div class="controls">
						<select name="yx_daojishi">
							<?php if(is_array($daojishi)): foreach($daojishi as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($row["yx_daojishi"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?>&nbsp;(<?php echo ($vo["id"]); ?>)</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">开始音效</label>
					<div class="controls">
						<select name="yx_kaishi">
							<?php if(is_array($kaishi)): foreach($kaishi as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($row["yx_kaishi"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?>&nbsp;(<?php echo ($vo["id"]); ?>)</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">失败音效</label>
					<div class="controls">
						<select name="yx_shibai">
							<?php if(is_array($shibai)): foreach($shibai as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($row["yx_shibai"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?>&nbsp;(<?php echo ($vo["id"]); ?>)</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">下抓音效</label>
					<div class="controls">
						<select name="yx_xiazhua">
							<?php if(is_array($xiazhua)): foreach($xiazhua as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>" <?php if(($row["yx_xiazhua"]) == $vo["id"]): ?>selected<?php endif; ?> ><?php echo ($vo["title"]); ?>&nbsp;(<?php echo ($vo["id"]); ?>)</option><?php endforeach; endif; ?>
						</select>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">概率模式</label>
					<div class="controls">
						<?php if( $row['is_sellmodel'] == 0 && $row['is_roommodel'] == 0 ){ echo '暂无配置'; }else{ if( $row['is_sellmodel'] == 1 )echo '用户贩卖模式、'; if( $row['is_roommodel'] == 1 )echo '房间概率模式   【抓'.$row['claw_count'].'次出一次强抓力】、'; } ?>
					</div>
				</div>
				
				<!--<div class="control-group">-->
					<!--<label class="control-label">概率模式</label>-->
					<!--<div class="controls">-->
						<!--<input type="checkbox" name="proba_model[]" value="1"-->
							<!--<?php if(is_array($row["proba_model"])): foreach($row["proba_model"] as $key=>$vo): ?>-->
								<!--<?php if($vo == 1): ?>checked<?php endif; ?>-->
							<!--<?php endforeach; endif; ?>-->
						<!--&gt;公共模式&nbsp;&nbsp;-->
						<!--<input type="checkbox" name="proba_model[]" value="2"-->
							<!--<?php if(is_array($row["proba_model"])): foreach($row["proba_model"] as $key=>$vo): ?>-->
								<!--<?php if($vo == 2): ?>checked<?php endif; ?>-->
							<!--<?php endforeach; endif; ?>-->
						<!--&gt;房间概率模式&nbsp;&nbsp;-->
						<!--<input type="checkbox" name="proba_model[]" value="3"-->
							<!--<?php if(is_array($row["proba_model"])): foreach($row["proba_model"] as $key=>$vo): ?>-->
								<!--<?php if($vo == 3): ?>checked<?php endif; ?>-->
							<!--<?php endforeach; endif; ?>-->
						<!--&gt;用户贩卖模式&nbsp;&nbsp;-->
					<!--</div>-->
				<!--</div>-->
			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('EDIT');?></button>
				<a class="btn" href="<?php echo U('Room/index');?>"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
<script>
	$(function(){
	    $("#gift").change(function(){
	        var giftid = $(this).val();
	        $.ajax({
				url : "<?php echo U('Room/handle_ajax');?>",
				type : 'post',
				data : 'id='+giftid,
				dataType : 'json',
				success : function(res){
				    if( res.status == 1 ){
                        $("#wawa_no").val(res.wawa_no);
                        $("#goodsimg").attr('src',res.gifticon);
					}

				},
				error : function(){
				    alert('系统内部错误');
				}
			});
		});

	});
</script>
</html>