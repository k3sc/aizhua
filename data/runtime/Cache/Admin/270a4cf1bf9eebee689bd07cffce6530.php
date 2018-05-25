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
			<li ><a href="<?php echo U('Gift/index');?>">兑换礼品列表</a></li>
			<li class="active"><a >兑换礼品编辑</a></li>
		</ul>
		<form  method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data" action="<?php echo U('Gift/edit_post');?>">
			<fieldset>
				<div class="control-group">
					<label class="control-label">兑换礼品分类</label>
					<div class="controls">
						<select name="type_id">
							 <?php echo ($class); ?>
						</select>
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">兑换礼品的名称</label>
					<div class="controls">
						<input type="hidden" name="id" value="<?php echo ($data["id"]); ?>" >
						<input type="text" name="name" value="<?php echo ($data["name"]); ?>" >
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">礼品的库存</label>
					<div class="controls">
						<input type="text" name="quantity" value="<?php echo ($data["quantity"]); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">成本(元)</label>
					<div class="controls">
						<input type="text" class="aa"  name="cost" value="<?php echo ($data["cost"]); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group" >
					<label class="control-label">可兑换礼品的娃娃</label>
					<div class="controls" >
						<?php echo ($data["body_id"]); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">可兑换礼品的娃娃数量</label>
					<div class="controls">
						<input type="text" name="convert_num" value="<?php echo ($data["convert_num"]); ?>">
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">礼品的出货量</label>
					<div class="controls">
						<input type="text" name="shipment_num" value="<?php echo ($data["shipment_num"]); ?>" readonly>
						<span class="form-required">*</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">礼品封面图</label>
					<div class="controls">
						<div >
							<input type="hidden" name="img" id="thumb2" value="<?php echo ($data["img"]); ?>">
							<a href="javascript:void(0);" onclick="flashupload('thumb_images', '附件上传','thumb2',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,1','','','');return false;">
								   <?php if($data["img"] == ''): ?><img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb2_preview" width="135" style="cursor: hand" />
								   <?php else: ?>
								   	    <img src="<?php echo ($data["img"]); ?>" id="thumb2_preview" width="135" style="cursor: hand" /><?php endif; ?>
							</a>
							<input type="button" class="btn btn-small" onclick="$('#thumb2_preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb2').val('');return false;" value="取消图片">
						</div>
						<span class="form-required"></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">礼品详情图</label>
					<div class="span9">
		                <table class="table table-bordered">
		                    <td>
		                        <script type="text/plain" id="content" name="content" ><?php echo (htmlspecialchars_decode($data["content"])); ?></script>
		                    </td>
		                    </tr>
		                </table>
	           		</div>
				</div>
			</fieldset>
			<div class="form-actions">
				<input type="submit" class="btn btn-primary js-ajax-submit" value="<?php echo L('EDIT');?>"/>
				<a class="btn" href="<?php echo U('Gift/index');?>"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script type="text/javascript" src="/public/js/content_addtop.js"></script>
	<script type="text/javascript">
    //编辑器路径定义
    var editorURL = GV.DIMAUB;
	</script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.all.min.js"></script>
	<script>
		$(function () {
			$("#all").click(function () {
				$(this).parent().find('input:checkbox').attr('checked',$(this).is(":checked"));
            });
        });
	</script>
	<script type="text/javascript">
	    $(function() {
	        $(".js-ajax-close-btn").on('click', function(e) {
	            e.preventDefault();
	            Wind.use("artDialog", function() {
	                art.dialog({
	                    id : "question",
	                    icon : "question",
	                    fixed : true,
	                    lock : true,
	                    background : "#CCCCCC",
	                    opacity : 0,
	                    content : "您确定需要关闭当前页面嘛？",
	                    ok : function() {
	                        setCookie("refersh_time", 1);
	                        window.close();
	                        return true;
	                    }
	                });
	            });
	        });
	        /////---------------------
	        Wind.use('validate', 'ajaxForm', 'artDialog', function() {
	            //javascript

	            //编辑器
	            editorcontent = new baidu.editor.ui.Editor();
	            editorcontent.render('content');
	            try {
	                editorcontent.sync();
	            } catch (err) {
	            }
	            //增加编辑器验证规则
	            jQuery.validator.addMethod('editorcontent', function() {
	                try {
	                    editorcontent.sync();
	                } catch (err) {
	                }
	                return editorcontent.hasContents();
	            });
	            var form = $('form.js-ajax-forms');
	            //ie处理placeholder提交问题
	            if ($.browser.msie) {
	                form.find('[placeholder]').each(function() {
	                    var input = $(this);
	                    if (input.val() == input.attr('placeholder')) {
	                        input.val('');
	                    }
	                });
	            }

	            var formloading = false;
	            //表单验证开始
	            form.validate({
	                //是否在获取焦点时验证
	                onfocusout : false,
	                //是否在敲击键盘时验证
	                onkeyup : false,
	                //当鼠标掉级时验证
	                onclick : false,
	                //验证错误
	                showErrors : function(errorMap, errorArr) {
	                    //errorMap {'name':'错误信息'}
	                    //errorArr [{'message':'错误信息',element:({})}]
	                    try {
	                        $(errorArr[0].element).focus();
	                        art.dialog({
	                            id : 'error',
	                            icon : 'error',
	                            lock : true,
	                            fixed : true,
	                            background : "#CCCCCC",
	                            opacity : 0,
	                            content : errorArr[0].message,
	                            cancelVal : '确定',
	                            cancel : function() {
	                                $(errorArr[0].element).focus();
	                            }
	                        });
	                    } catch (err) {
	                    }
	                },
	                //验证规则
	                rules : {
	                    'post[post_title]' : {
	                        required : 1
	                    },
	                    'post[post_content]' : {
	                        editorcontent : true
	                    }
	                },
	                //验证未通过提示消息
	                messages : {
	                    'post[post_title]' : {
	                        required : '请输入标题'
	                    },
	                    'post[post_content]' : {
	                        editorcontent : '内容不能为空'
	                    }
	                },
	                //给未通过验证的元素加效果,闪烁等
	                highlight : false,
	                //是否在获取焦点时验证
	                onfocusout : false,
	                //验证通过，提交表单
	                submitHandler : function(forms) {
	                    if (formloading)
	                        return;
	                    $(forms).ajaxSubmit({
	                        url : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
	                        dataType : 'json',
	                        beforeSubmit : function(arr, $form, options) {
	                            formloading = true;
	                        },
	                        success : function(data, statusText, xhr, $form) {
	                            formloading = false;
	                            if (data.status) {
	                                setCookie("refersh_time", 1);
	                                //添加成功
	                                Wind.use("artDialog", function() {
	                                    art.dialog({
	                                        id : "succeed",
	                                        icon : "succeed",
	                                        fixed : true,
	                                        lock : true,
	                                        background : "#CCCCCC",
	                                        opacity : 0,
	                                        content : data.info,
	                                        button : [ {
	                                            name : '继续添加？',
	                                            callback : function() {
	                                                reloadPage(window);
	                                                return true;
	                                            },
	                                            focus : true
	                                        }, {
	                                            name : '返回列表页',
	                                            callback : function() {
	                                                location = "<?php echo U('Gift/index');?>";
	                                                return true;
	                                            }
	                                        } ]
	                                    });
	                                });
	                            } else {
	                                isalert(data.info);
	                            }
	                        }
	                    });
	                }
	            });
	        });
	        ////-------------------------
	    });
	</script>
</body>
</html>