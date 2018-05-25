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
   		<li ><a href="<?php echo U('Gift/index');?>">兑换礼品列表</a></li>
		<li class="active"><a >兑换礼品添加</a></li>
    </ul>
    <form action="<?php echo U('Gift/add_post');?>" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data">
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
						<!-- <input type="radio" name="type" value="1" id="type_0" checked > -->
						<input type="text"  class="aa"  name="gift_name" value="<?php echo $_SESSION['gift_name']['value']; ?>" >
						<span class="form-required">*</span>
					</div>
			</div>
			<div class="control-group">
				<label class="control-label">礼品的库存</label>
				<div class="controls">
					<input type="text" class="aa"  name="quantity" value="<?php echo $_SESSION['quantity']['value']; ?>">
					<span class="form-required">*</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">成本(元)</label>
				<div class="controls">
					<input type="text" class="aa"  name="cost" value="<?php echo $_SESSION['cost']['value']; ?>">
					<span class="form-required">*</span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">礼品封面图</label>
				<div class="controls">
					<div >
						<input type="hidden" class="aa" name="img" id="thumb2" value="">
						<a href="javascript:void(0);" onclick="flashupload('thumb_images', '附件上传','thumb2',thumb_images,'1,jpg|jpeg|gif|png|bmp,1,,,1','','','');return false;">
							<img src="/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png" id="thumb2_preview" width="135" style="cursor: hand" />
						</a>
						<input type="button" class="btn btn-small" onclick="$('#thumb2_preview').attr('src','/admin/themes/simplebootx/Public/assets/images/default-thumbnail.png');$('#thumb2').val('');return false;" value="取消图片">
					</div>
					<span class="form-required"></span>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">可兑换礼品的娃娃</label>
				<div class="controls" id="body" >
					<?php foreach($_SESSION['BodyName'] as $key=>$value){ echo "<input name='body_id[]' class='bodyName' type='checkbox' value='$key' checked='checked'/>$value&nbsp;"; } ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">要兑换礼品的娃娃数量</label>
				<div class="controls">
					<input type="text" class="aa" name="convert_num" value="<?php echo $_SESSION['convert_num']['value']; ?>">
					<span class="form-required">*</span>
				</div>
			</div>
			<div class="control-group">
					<label class="control-label">礼品详情</label>
	            <div class="span9">
	                <table class="table table-bordered">
	                    <td>
	                        <script type="text/plain" id="content" name="content" ></script>
	                    </td>
	                    </tr>
	                </table>
	            </div>
			</div>
    	</fieldset>
        <div class="form-actions">
            <input type="submit" class="btn btn-primary js-ajax-submit" value="添加"/>
			<a class="btn" href="<?php echo U('Gift/index');?>"><?php echo L('BACK');?></a>
        </div>
    </form>
    <!-- 娃娃列表 -->
	<form method="post"  action="" class="well form-search">
		<form method="post" class="js-ajax-form" action="<?php echo U('Gift/add');?>">
	 		娃娃编号:
			<input type="text" name="id" value="<?php echo ($id); ?>" placeholder="" style="width: 55px;">&nbsp;&nbsp;
			娃娃名称:
			<input type="text" name="name" value="<?php echo ($name); ?>"  placeholder="" style="width: 80px;">&nbsp;&nbsp;
			娃娃分类:
			<select name="type" placeholder="娃娃分类" style="width: 120px;">&nbsp;&nbsp; 
		        <option value="">娃娃分类</option>
				<?php echo ($bodyType); ?>
		    </select>
		    标签:  
		    <select name="tage" placeholder="娃娃分类" style="width: 120px;">&nbsp;&nbsp; 
		        <option value="">请选择</option>
				<?php echo ($bodyTage); ?>;
			</select>
			<input type="submit" class="btn btn-primary" value="搜索">
       	
       </form>
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th align="center">
						<!--<button type="submit" id="allAdd" value="" class="btn btn-success btn-small">全部添加</button>-->
					</th>
					<th>娃娃编号</th>
					<th>娃娃名称</th>
					<th>娃娃主图</th>
					<th>游戏价格</th>
					<th>成本(元)</th>
					<th>标签</th>
					<th>娃娃分类</th>
					<th>抓到总次数</th>
					<th>可兑的换礼品</th>
				</tr>
			</thead>
			<tbody id="mark">
				<?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr>
					<td align="center">
						<button type="button" data-id="<?php echo ($vo["id"]); ?>" class="Add btn btn-success btn-small" value="">添加</button>
					</td>
					<td id="bodyid"><?php echo str_pad($vo['id'],4,"0",STR_PAD_LEFT); ?></td>
					<td><?php echo ($vo["giftname"]); ?></td>
					<td><?php if($vo["gifticon"] != ''): ?><img src="<?php echo ($vo["gifticon"]); ?>" style="width: 30px;height: 30px;"><?php endif; ?>	
					</td>
					<td><?php echo ($vo["spendcoin"]); ?></td>
					<td><?php echo ($vo["cost"]); ?></td>
					<td><?php echo ($vo["label_id"]); ?></td>
					<td><?php echo ($vo["type_id"]); ?></td>
					<td><?php echo ($vo["count_success"]); ?></td>
					<td><?php echo ($vo["convert_lipin"]); ?></td>
				</tr><?php endforeach; endif; ?>
			</tbody>
		</table>
		<div class="pagination"><?php echo ($page); ?> 共<?php echo ($count); ?>条</div>
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
<script type="text/javascript">
	//删除想要兑换的娃娃
	$(function(){
		$(".bodyName").click(function(){
			var id  = $(this).val();
			//console.log(id);
			 $.ajax({
				url : "<?php echo U('gift/ajax');?>",
				type : 'get',
				data : 'delId='+id,
				success : function(e){

				},
	            error : function(e){

	            }
			});
		});
	});

	//获取想要兑换的娃娃
	$(function(){
		$(".Add").click(function(event) {
			var string = '';
			var id  = $(this).parent().next().text();
			var name= $(this).parent().next().next().text();
			string ='<span><input name="body_id[]" type="checkbox" value="'+id+'" checked="checked"/>'+name+'&nbsp;</span>';
			 $.ajax({
				url : "<?php echo U('gift/ajax');?>",
				type : 'get',
				data : 'bodyId='+id+'&bodyBame='+name,
				success : function(e){

				},
	            error : function(e){

	            }
			});
			// string ='<input name="body_id[]" type="checkbox" value="'+id+'" checked="checked"/>'+name+'&nbsp;';
			$('#body').prepend(string);
			$(this).attr('disabled','disabled');
		});
	})
</script>

<script>
//把获取的值先存入session
	$(function(){
	    $(".aa").change(function(){
	        var value = $(this).val();
	        var name  = $(this).attr('name');
	        //console.log($value);
	        // alert(name);
	        $.ajax({
				url : "<?php echo U('gift/ajax');?>",
				type : 'get',
				data : 'value='+value+'&name='+name,
				success : function(e){

				},
	            error : function(e){

	            }
			});
		});
	})
</script>

<script>
	$(window).bind('beforeunload',function () {
        $.ajax({
            url : "<?php echo U('gift/clear_session');?>",
            type : 'get',
            success : function(e){

            },
            error : function(e){

            }
        });
    });
</script>
<script>
	$(function () {
		$("input:checkbox").live('click',function () {
		    var id = $(this).val();
		    $(".Add[data-id="+id+"]").removeAttr('disabled');
			$(this).parent().remove();
        });
    })
</script>

</html>