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
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('AdminPage/index');?>"><?php echo L('PORTAL_ADMINPAGE_INDEX');?></a></li>
			<li><a href="<?php echo U('AdminPage/add');?>"><?php echo L('PORTAL_ADMINPAGE_ADD');?></a></li>
			<li class="active"><a href="#"><?php echo L('PORTAL_ADMINPAGE_EDIT');?></a></li>
		</ul>
		<form action="<?php echo U('Portal/AdminPage/edit_post');?>" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data">
			<div class="row-fluid">
				<div class="span9">
					<table class="table table-bordered">
						<tr>
							<th width="80">标题</th>
							<td>
								<input type="hidden" name="post[post_author]" value="<?php echo ($author); ?>">
								<input type="hidden" name="post[id]" value="<?php echo ($post["id"]); ?>">
								<input type="text" style="width: 400px;" name="post[post_title]" value="<?php echo ($post["post_title"]); ?>" placeholder="请输入标题" <?php if($post['id']<=4)echo readonly; ?> />
								<span class="form-required">*</span>
							</td>
						</tr>
						<tr style="display:none;">
							<th>模版</th>
							<td>
								<?php $tpl_list=sp_admin_get_tpl_file_list(); unset($tpl_list['page']); ?>
								<select style="min-width: 290px;" name="smeta[template]">
									<option value="page">page<?php echo C("TMPL_TEMPLATE_SUFFIX");?></option>
									<?php if(is_array($tpl_list)): foreach($tpl_list as $key=>$vo): $template_selected=$smeta['template']==$vo?"selected":""; ?>
										<option value="<?php echo ($vo); ?>"<?php echo ($template_selected); ?>><?php echo ($vo); echo C("TMPL_TEMPLATE_SUFFIX");?></option><?php endforeach; endif; ?>
								</select>
							</td>
						</tr>
						<tr style="display: none;">
							<th>网页类型</th>
							<td>
								<?php $type=array("0"=>'单页面','2'=>'关于我们'); ?>
								<select style="min-width: 290px;" name="post[type]">
							
									<?php if(is_array($type)): foreach($type as $key=>$vo): $template_selected=$post['type']==$key?"selected":""; ?>
										<option value="<?php echo ($key); ?>"<?php echo ($template_selected); ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
								</select>
							</td>
						</tr>
						
						<tr style="display:none;">
							<th>关键词</th>
							<td>
								<input type="text" name="post[post_keywords]" style="width: 280px" value="<?php echo ($post['post_keywords']); ?>" placeholder="请输入关键字">
								多关键词之间用空格隔开
							</td>
						</tr>
						<tr style="display:none;">
							<th>摘要</th>
							<td><textarea name="post[post_excerpt]" style="width: 98%; height: 50px;"><?php echo ($post["post_excerpt"]); ?></textarea>
							</td>
						</tr>
						<tr>
							<th>内容</th>
							<td>
								<script type="text/plain" id="content" name="post[post_content]"><?php echo ($post["post_content"]); ?></script>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="span3" style="display:none;">
					<table class="table table-bordered">
						<tr>
							<th>缩略图</th>
						</tr>
						<tr>
							<td>
								<div style="text-align: center;">
									<input type='hidden' name='smeta[thumb]' id='thumb' value="<?php echo ((isset($smeta["thumb"]) && ($smeta["thumb"] !== ""))?($smeta["thumb"]):''); ?>">
									<a href='javascript:void(0);' onclick="flashupload('thumb_images', '附件上传','thumb',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,1','','','');return false;">
										<?php if(empty($smeta['thumb'])): ?><img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id='thumb_preview' width='135' height='113' style='cursor: hand' />
										<?php else: ?>
											<img src="<?php echo sp_get_asset_upload_path($smeta['thumb']);?>" id='thumb_preview' width='135' height='113' style='cursor: hand' /><?php endif; ?>
									</a>
									<input type="button" class="btn btn-small" onclick="$('#thumb_preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb').val('');return false;" value="取消图片">
								</div>
							</td>
						</tr>
						<tr>
							<th>发布时间</th>
						</tr>
						<tr>
							<td><input type="text" name="post[post_modified]" value="<?php echo ($post["post_modified"]); ?>" class="js-datetime"></td>
						</tr>
						<tr>
							<th>状态</th>
						</tr>
						<tr>
							<td>
								<?php $status_yes=$post['post_status']==1?"checked":""; $status_no=$post['post_status']==0?"checked":""; ?>
								<label class="radio"><input type="radio" name="post[post_status]" value="1" <?php echo ($status_yes); ?>>审核通过</label>
								<label class="radio"><input type="radio" name="post[post_status]" value="0" <?php echo ($status_no); ?>>待审核</label>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="form-actions">
				<button class="btn btn-primary js-ajax-submit" type="submit"><?php echo L('SAVE');?></button>
				<a class="btn" href="<?php echo U('AdminPage/index');?>"><?php echo L('BACK');?></a>
			</div>
		</form>
	</div>
	<script type="text/javascript" src="/public/js/common.js"></script>
	<script type="text/javascript" src="/public/js/content_addtop.js"></script>
	<script type="text/javascript">
		//编辑器路径定义
		var editorURL = GV.DIMAUB;
	</script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" src="/public/js/ueditor/ueditor.all.min.js"></script>
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
				;
				//增加编辑器验证规则
				jQuery.validator.addMethod('editorcontent', function() {
					try {
						editorcontent.sync();
					} catch (err) {
					}
					;
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
						$(forms).ajaxSubmit({
							url : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
							dataType : 'json',
							beforeSubmit : function(arr, $form, options) {

							},
							success : function(data, statusText, xhr, $form) {
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
												name : '确定',
												callback : function() {
													//reloadPage(window);
													return true;
												},
												focus : true
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