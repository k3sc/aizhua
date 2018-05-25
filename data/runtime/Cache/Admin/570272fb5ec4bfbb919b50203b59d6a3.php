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




<!--<script src="/public/admin/jquery.js"></script>-->



</head>
<body>
<div class="wrap">
    <ul class="nav nav-tabs">
        <li ><a href="<?php echo U('Product/index');?>">娃娃列表</a></li>
        <li class="active"><a >添加</a></li>
    </ul>
    <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Capturewawa/add_post',array('id'=>$get_id));?>">
        <fieldset>
            <div class="control-group">
                <label class="control-label">娃娃名称</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($wawa['giftname']); ?>" name="giftname">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">娃娃分类</label>
                <div class="controls">
                    <select name="type_id">
                        <?php if(is_array($wawa_type)): foreach($wawa_type as $key=>$vo): ?><option <?php if(($vo["id"]) == $wawa["type_id"]): ?>selected<?php endif; ?> value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; ?>
                    </select>
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">娃娃封面图</label>
                <div class="controls">
                    <div >
                        <input type="hidden" name="gifticon" id="thumb2" value="<?php if(empty($wawa["gifticon"])): ?>/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png<?php else: echo ($wawa["gifticon"]); endif; ?>">
                        <a href="javascript:void(0);" onclick="flashupload('thumb_images', '附件上传','thumb2',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,1','','','');return false;">
                            <img src="<?php if(empty($wawa["gifticon"])): ?>/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png<?php else: echo ($wawa["gifticon"]); endif; ?>" id="thumb2_preview" width="135" style="cursor: hand" />
                        </a>
                        <input type="button" class="btn btn-small" onclick="$('#thumb2_preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb2').val('');return false;" value="取消图片">
                    </div>
                    <span class="form-required"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">娃娃详情图片</label>
                <div class="controls">
                    <div id="demo">
                        <div id="img" ></div>
                    </div>
                    <span id="gift_img">
                        <?php if(is_array($wawa["img"])): foreach($wawa["img"] as $key=>$vo): if(!empty($vo)): ?><img src="<?php echo ($vo); ?>" style="width: 135px;height: 100px;"/>&nbsp;
                                <input type="hidden" name="file[]" value="<?php echo ($vo); ?>" ><?php endif; endforeach; endif; ?>
                    </span>
                </div>
            </div>


            <div class="control-group">
                <label class="control-label">不可兑换</label>
                <div class="controls">
                    <input type="checkbox" style="width: 20px;height: 20px;" id="convert_no" name="convert[]" value="1" <?php if(in_array(1,$wawa['convert']))echo 'checked'; ?> >
                    <span class="form-required" style="display: none;"></span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">可兑换娃娃币</label>
                <div class="controls">
                    <input type="checkbox" style="width: 20px;height: 20px;" id="convert_coin" name="convert[]" value="2" <?php if(in_array(2,$wawa['convert']))echo 'checked'; ?> >
                    &nbsp;&nbsp;
                    <span class="form-required" style="display: none;">
                        <input style="width: 50px;" type="text" name="needcoin" value="<?php echo ($wawa['needcoin']); ?>">个
                    </span>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">可兑换礼品</label>
                <div class="controls">
                    <input type="checkbox" style="width: 20px;height: 20px;" id="convert_gift" name="convert[]" value="3" <?php if(in_array(3,$wawa['convert']))echo 'checked'; ?> >
                    <span class="form-required" style="display: none;"></span>
                </div>
            </div>

            <div class="control-group" id="gift" style="display: none;">
                <label class="control-label">可兑换的礼品</label>
                <div class="controls">
                    <?php echo '<input type="checkbox" id="all">全部<br><br>'; foreach($give_gift as $k => $v){ if( in_array($v['id'],explode(',',$wawa['convert_lipin'])) ){ echo '<input type="checkbox" checked name="convert_lipin[]" value="'.$v[id].'" >'."<img src='$v[img]' style='width: 30px;height: 30px;'>".$v['name'].'（'.str_pad($v['id'],4,"0",STR_PAD_LEFT).'）'.'&nbsp;&nbsp;&nbsp;'; }else{ echo '<input type="checkbox" name="convert_lipin[]" value="'.$v[id].'" >'."<img src='$v[img]' style='width: 30px;height: 30px;'>".$v['name'].'（'.str_pad($v['id'],4,"0",STR_PAD_LEFT).'）'.'&nbsp;&nbsp;&nbsp;'; } if( $k % 5 == 0 && $k != 0 )echo '<br><br>'; } ?>
                </div>
            </div>

            <!--<div class="control-group">-->
            <!--<label class="control-label">可兑换的娃娃币数量</label>-->
            <!--<div class="controls">-->
            <!--<input type="text" name="needcoin" value="<?php echo ($wawa['needcoin']); ?>">-->
            <!--<span class="form-required">*</span>-->
            <!--</div>-->
            <!--</div>-->
            <div class="control-group">
                <label class="control-label">库存</label>
                <div class="controls">
                    <input type="text" name="stock" value="<?php echo ($wawa['stock']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">成本</label>
                <div class="controls">
                    <input type="text" name="cost" value="<?php echo ($wawa['cost']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">游戏价格</label>
                <div class="controls">
                    <input type="text" name="spendcoin" value="<?php echo ($wawa['spendcoin']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <!--<div class="control-group">-->
            <!--<label class="control-label">兑换属性</label>-->
            <!--<div class="controls">-->
            <!--<input id="no" type="checkbox" <?php if(in_array(1,$wawa['convert']))echo 'checked'; ?> name="convert[]" value="1">不可兑换-->
            <!--<input type="checkbox" <?php if(in_array(2,$wawa['convert']))echo 'checked'; ?> name="convert[]" value="2">可兑换娃娃币-->
            <!--<input type="checkbox" <?php if(in_array(3,$wawa['convert']))echo 'checked'; ?> name="convert[]" value="3">可兑换娃娃礼品-->
            <!--</div>-->
            <!--</div>-->

            <div class="control-group">
                <label class="control-label">娃娃标签</label>
                <div class="controls">
                    <select name="label_id">
                        <?php if(is_array($wawa_label)): foreach($wawa_label as $key=>$vo): ?><option <?php if(($vo["id"]) == $wawa["label_id"]): ?>selected<?php endif; ?> value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
            <!--
            <div class="control-group">
                <label class="control-label">出货量</label>
                <div class="controls">
                    <input type="text" name="chuhuo" value="<?php echo ($wawa['chuhuo']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">总共被抓中次数</label>
                <div class="controls">
                    <input type="text" name="count_success" value="<?php echo ($wawa['count_success']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">总共被抓次数</label>
                <div class="controls">
                    <input type="text" name="count" value="<?php echo ($wawa['count']); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            -->
            <input type="hidden" name="id" value="<?php echo ($wawa['id']); ?>">

        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('ADD');?></button>
            <a class="btn" href="<?php echo U('Product/index');?>"><?php echo L('BACK');?></a>
        </div>
    </form>



</div>
<script src="/public/js/common.js"></script>
<script type="text/javascript" src="/public/js/content_addtop.js"></script>
<link rel="stylesheet" type="text/css" href="/public/admin/diyUpload/css/webuploader.css">
<link rel="stylesheet" type="text/css" href="/public/admin/diyUpload/css/diyUpload.css">
<script type="text/javascript" src="/public/admin/diyUpload/js/webuploader.html5only.min.js"></script>
<script type="text/javascript" src="/public/admin/diyUpload/js/diyUpload.js"></script>
</body>
</html>




<script>
    $(function () {
        if( $("#convert_no").is(":checked") ){
            $("#convert_coin,#convert_gift").attr('disabled','disabled').removeAttr('checked');
            $("input[name='needcoin']").val(0);
            $("#gift input:checkbox").removeAttr("checked");
        }else{
            if( $("#convert_coin").is(":checked") ){
                $("#convert_coin").next().show();
            }else{
                $("#convert_coin").next().hide();
                $("input[name='needcoin']").val(0);
            }
            if( $("#convert_gift").is(":checked") ){
                $("#gift").show();
            }else{
                $("#gift").hide();
                $("#gift input:checkbox").removeAttr("checked");
            }
        }

        $("#convert_no").click(function () {
            if( $(this).is(":checked") ){
                $("#convert_coin").attr('disabled','disabled').removeAttr('checked').next().hide();
                $("input[name='needcoin']").val(0);
                $("#convert_gift").attr('disabled','disabled').removeAttr('checked');
                $("#gift").hide();
                $("#gift input:checkbox").removeAttr("checked");
            }else{
                $("#convert_coin").removeAttr('disabled');
                $("#convert_gift").removeAttr('disabled');
            }
        });
        $("#convert_coin").click(function () {
            if( $(this).is(":checked") ){
                $(this).next().show();
            }else{
                $("input[name='needcoin']").val(0);
                $(this).next().hide();
            }
        });
        $("#convert_gift").click(function () {
            if( $(this).is(":checked") ){
                $("#gift").show();
            }else{
                $("#gift").hide();
                $("#gift input:checkbox").removeAttr("checked");
            }
        });

        $("#all").click(function () {
            if( $(this).is(":checked") ){
                $(this).siblings('input:checkbox').attr("checked","checked");
            }else{
                $(this).siblings('input:checkbox').removeAttr("checked");
            }
        });

    });
</script>



<script type="text/javascript">

    /*
    * 服务器地址,成功返回,失败返回参数格式依照jquery.ajax习惯;
    * 其他参数同WebUploader
    */

    $('#img').diyUpload({
        url:'<?php echo U("Capturewawa/getFile");?>',
        success:function( data ) {
            console.log(data);
            var string = "<input type='hidden' name='file[]'  value='"+data.file+"'>";
            $("#img").append(string);
        },
        error:function( err ) {
            console.log(err);
        },
        buttonText : '选择文件',
        chunked:true,
        // 分片大小
        chunkSize:512 * 1024,
        //最大上传的文件数量, 总文件大小,单个文件大小(单位字节);
        fileNumLimit:5,
        fileSizeLimit:500000 * 1024,
        fileSingleSizeLimit:50000 * 1024,
        accept: {}
    });
</script>