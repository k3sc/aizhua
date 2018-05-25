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




<script src="/public/admin/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="/public/admin/diyUpload/css/webuploader.css">
<link rel="stylesheet" type="text/css" href="/public/admin/diyUpload/css/diyUpload.css">
<script type="text/javascript" src="/public/admin/diyUpload/js/webuploader.html5only.min.js"></script>
<script type="text/javascript" src="/public/admin/diyUpload/js/diyUpload.js"></script>
<script type="text/javascript" src="/public/js/content_addtop.js"></script>

</head>
<body>
<div class="wrap">
    <ul class="nav nav-tabs">
        <li ><a href="<?php echo U('Users/index');?>">用户列表</a></li>
        <li class="active"><a >编辑</a></li>
    </ul>
    <form method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Users/edit_user',array('id'=>$row['id']));?>">
        <fieldset>
            <div class="control-group">
                <label class="control-label">用户ID</label>
                <div class="controls">
                    <span class="text-error"><?php echo ($row["id"]); ?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">微信openID</label>
                <div class="controls">
                    <span class="text-error"><?php echo ($row["openid"]); ?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">昵称</label>
                <div class="controls">
                    <span class="text-error"><?php echo ($row["user_nicename"]); ?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">邮箱</label>
                <div class="controls">
                    <span class="text-error"><?php echo ($row["user_email"]); ?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">头像</label>
                <div class="controls">
                    <div >
                        <input type="hidden" name="avatar" id="thumb2" value="<?php if(empty($row["avatar"])): ?>/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png<?php else: echo ($row["avatar"]); endif; ?>">
                        <a href="javascript:void(0);" onClick="flashupload('thumb_images', '附件上传','thumb2',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,1','','','');return false;">
                            <img src="<?php if(empty($row["avatar"])): ?>/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png<?php else: echo ($row["avatar"]); endif; ?>" id="thumb2_preview" width="135" style="cursor: hand" />
                        </a>
                        <input type="button" class="btn btn-small" onClick="$('#thumb2_preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb2').val('');return false;" value="取消图片">
                    </div>
                    <span class="form-required"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">性别</label>
                <div class="controls">
                    <span class="text-error"><?php switch($row["sex"]): case "1": ?>男<?php break; case "2": ?>女<?php break; default: ?>保密<?php endswitch;?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">娃娃币余额</label>
                <div class="controls">
                    <input type="text" name="coin" value="<?php echo ($row["coin"]); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">充值获赠娃娃币</label>
                <div class="controls">
                    <input type="text" name="coin_sys_give" value="<?php echo ($row["coin_sys_give"]); ?>">
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">夹娃娃次数</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($wawa_count); ?>" readonly>
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">夹中次数</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($wawa_count_success); ?>" readonly>
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">剩余甩抓次数</label>
                <div class="controls">
                    <input type="text" name="claw" value="<?php echo ($row["claw"]); ?>" >
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">剩余强抓力次数</label>
                <div class="controls">
                    <input type="text" name="strong" value="<?php echo ($row["strong"]); ?>" >
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">邀请获赠娃娃币</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($row["invite_coin"]); ?>" readonly >
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">活动赠币</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($row["active_coin"]); ?>" readonly >
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">邀请人数</label>
                <div class="controls">
                    <input type="text" value="<?php echo ($invite_count); ?>" readonly >
                    <span class="form-required">*</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">推荐人</label>
                <div class="controls">
                    <span class="text-error"><?php if(!empty($invite_person)): echo ($invite_person["user_nicename"]); ?>  ( ID：<?php echo ($invite_person["id"]); ?> )<?php endif; ?></span>
                </div>
            </div>
            <!--
            <div class="control-group">
					<label class="control-label"><?php echo L('ROLE');?></label>
					<div class="controls">
						<?php if(is_array($roles)): foreach($roles as $key=>$vo): ?><label class="checkbox inline">
							<?php $role_id_checked=in_array($vo['id'],$role_ids)?"checked":""; ?>
							<input value="<?php echo ($vo["id"]); ?>" type="checkbox" name="role_id[]" <?php echo ($role_id_checked); ?>><?php echo ($vo["name"]); ?>
						</label><?php endforeach; endif; ?>
					</div>
				</div>
			-->
        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary js-ajax-submit"><?php echo L('EDIT');?></button>
            <a class="btn" href="<?php echo U('Users/index');?>"><?php echo L('BACK');?></a>
        </div>
    </form>
</div>
<script src="/public/js/common.js"></script>
</body>



</html>