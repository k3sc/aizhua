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
        <li class="active"><a href="<?php echo U('Settingother/index');?>">其他设置</a></li>
        <li ><a href="<?php echo U('Settingother/bgmusic');?>">背景音乐</a></li>
        <li ><a href="<?php echo U('Settingother/gameAudio');?>">游戏音效</a></li>
    </ul>
    <form id="form" method="post" class="form-horizontal js-ajax-form" action="<?php echo U('Settingother/post');?>" enctype="multipart/form-data">
        <fieldset>
            <div class="control-group" style="display: none;">
                <label class="control-label">游戏语音开关</label>
                <div class="controls">
                    <select name="yy">
                        <option <?php if(($row["yy"]) == "1"): ?>selected<?php endif; ?> value="1">开</option>
                        <option <?php if(($row["yy"]) == "2"): ?>selected<?php endif; ?> value="2">关</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">邮箱登陆开关</label>
                <div class="controls">
                    <select name="yx">
                        <option <?php if(($row["yx"]) == "1"): ?>selected<?php endif; ?> value="1">开</option>
                        <option <?php if(($row["yx"]) == "2"): ?>selected<?php endif; ?> value="2">关</option>
                    </select>
                </div>
            </div>
            <div class="control-group" style="display: none;">
                <label class="control-label">推送通知开关</label>
                <div class="controls">
                    <select name="tz">
                        <option <?php if(($row["tz"]) == "1"): ?>selected<?php endif; ?> value="1">开</option>
                        <option <?php if(($row["tz"]) == "2"): ?>selected<?php endif; ?> value="2">关</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">聊天关键字屏蔽</label>
                <div class="controls">
                    <textarea name="keyword" style="width: 490px;height: 110px;"><?php echo ($row["keyword"]); ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">聊天快捷短语</label>
                <div class="controls">
                    <textarea name="quick" style="width: 490px;height: 110px;"><?php echo ($row["quick"]); ?></textarea>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">支付方式</label>
                <div class="controls">
                    <input type="checkbox" name="paytype[]" value="1" <?php if(in_array(1,$row['paytype']))echo checked; ?> >微信 &nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="paytype[]" value="2" <?php if(in_array(2,$row['paytype']))echo checked; ?> >支付宝 &nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="paytype[]" value="3" <?php if(in_array(3,$row['paytype']))echo checked; ?> >PayPal &nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="paytype[]" value="4" <?php if(in_array(4,$row['paytype']))echo checked; ?> >Apple &nbsp;&nbsp;&nbsp;
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">汇率</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" name="rate" value="<?php echo ($rate); ?>">
            </div>
            <div class="control-group">
                <label class="control-label">值班管理员手机号码</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" name="phone" value="<?php echo ($phone); ?>">
            </div>
            <div class="control-group">
                <label class="control-label">邀请好友获得娃娃币数量</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" name="code_wawabi" value="<?php echo ($code_wawabi); ?>">
            </div>
            <div class="control-group">
                <label class="control-label">推广下载链接</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" name="app_url" value="<?php echo ($app_url); ?>">
            </div>
            <div class="control-group">
                <label class="control-label">Android版本名称</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" name="app_ver_name" value="<?php echo ($version["app_ver_name"]); ?>">
            </div>
            <div class="control-group">
                <label class="control-label">Android版本号</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" name="apk_ver" value="<?php echo ($version["apk_ver"]); ?>">
                <span class="text-error">输入整数</span>
            </div>
            <!--<div class="control-group">
                <label class="control-label">apk</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="javascript:;" id="upload_apk" class="btn btn-success btn-sm" style="position: relative">上传apk文件
                    <input type="file" name="apk" style="position:absolute;width:140px;height:50px;top:0;left:0;margin:0;padding:0;opacity:0;">
                </a>
                <span class="text-error">上传文件需要一定时间，请耐心等待...</span>
            </div>-->
            <!--<div class="control-group">
                <label class="control-label">apk</label>
                <div class="controls">
                    <div id="demo">
                        <div id="img" ></div>
                    </div>-->
                    <!--<span id="gift_img">-->
                        <!--<?php if(is_array($wawa["img"])): foreach($wawa["img"] as $key=>$vo): ?>-->
                            <!--<?php if(!empty($vo)): ?>-->
                                <!--<img src="<?php echo ($vo); ?>" style="width: 135px;height: 100px;"/>&nbsp;-->
                                <!--<input type="hidden" name="file[]" value="<?php echo ($vo); ?>" >-->
                            <!--<?php endif; ?>-->
                        <!--<?php endforeach; endif; ?>-->
                    <!--</span>-->
                <!--</div>
            </div>-->
            <div class="control-group">
                <label class="control-label">apk下载地址</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="text" name="apk_url" value="<?php echo ($version["apk_url"]); ?>">
            </div>
            <div class="control-group">
                <label class="control-label">Android版本更新内容</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <div class="controls">
                    <script type="text/plain" id="content" name="app_update_content"><?php echo (htmlspecialchars_decode($version["app_update_content"])); ?></script>
                </div>
            </div>
        </fieldset>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary js-ajax-submit">设定</button>
        </div>
    </form>
</div>
<script src="/public/js/common.js"></script>
<script type="text/javascript" src="/public/js/content_addtop.js"></script>
<link rel="stylesheet" type="text/css" href="/public/admin/diyUpload/css/webuploader.css">
<link rel="stylesheet" type="text/css" href="/public/admin/diyUpload/css/diyUpload.css">
<script type="text/javascript" src="/public/admin/diyUpload/js/webuploader.html5only.min.js"></script>
<script type="text/javascript" src="/public/admin/diyUpload/js/diyUpload.js"></script>
<script type="text/javascript">
    //编辑器路径定义
    var editorURL = GV.DIMAUB;
</script>
<script type="text/javascript" src="/public/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/public/js/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">
    $(function() {
        //编辑器
        editorcontent = new baidu.editor.ui.Editor();
        editorcontent.render('content');
        try {
            editorcontent.sync();
        } catch (err) {
        }
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
                    'slide_name' : {
                        required : 1
                    }
                },
                //验证未通过提示消息
                messages : {
                    'slide_name' : {
                        required : '请输入名称'
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
                                            name : '返回列表',
                                            callback : function() {
                                                location.href = "<?php echo U('slide/index');?>";
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

<script>
    $(function () {
        $("#upload_apk").change(function () {
            $("#form").submit();
        });
    });
</script>

<script type="text/javascript">

    /*
    * 服务器地址,成功返回,失败返回参数格式依照jquery.ajax习惯;
    * 其他参数同WebUploader
    */

    $('#img').diyUpload({
        url:'<?php echo U("Settingother/getFile");?>',
        success:function( data ) {
            // console.log(data);
            var string = "<input type='hidden' name='file'  value='"+data.filename+"'>";
            $("#img").append(string);
        },
        error:function( err ) {
            // console.log(err);
        },
        buttonText : '选择apk文件',
        chunked:true,
        // 分片大小
        chunkSize:512 * 1024,
        //最大上传的文件数量, 总文件大小,单个文件大小(单位字节);
        fileNumLimit:1,
        fileSizeLimit:500000 * 1024 * 1024,
        fileSingleSizeLimit:50000 * 1024 * 1024,
        accept: {}
    });
</script>


</html>