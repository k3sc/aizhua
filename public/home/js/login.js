var Login={
	login_url:'./index.php?g=home&m=user&a=userLogin',
	reg_url:'./index.php?g=home&m=user&a=userReg',
	loginout_url:'./index.php?g=home&m=user&a=logout',
	forget_url:'./index.php?g=home&m=user&a=forget',
	captcha_url:'./index.php?g=home&m=user&a=getCaptcha',
	code_url:'./index.php?g=home&m=user&a=getCode',
	dombody:$("body"),
	type:'login',
	checkPhone:/^0?1[3|4|5|7|8|9][0-9]\d{8}$/,
	checkPass:/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,12}$/,
	init:function(){
		this.threeOpen();
	},
	threeOpen()
	{
		$.ajax(
		{
			url:"./index.php?g=home&m=user&a=threeparty",
			success:function(data)
			{
				var str = JSON.parse(data);
				Login.dom(str['qq'],str['weibo'],str['weixin']);
				Login.addEvent();
			}
		});
	},
	dom:function(qq,weibo,weixin){
		var threeQQ='',threeWeibo='',threeWeixin='';
		var login_html='<div class="login_pop js_login_pop">\
											<div class="title">\
												<span class="close js_close js_login_close"></span>\
												登 录\
											</div><article>';
			 	if(qq=="1")
				{
					threeQQ='<div class="qq js_qq js_login_qq"><span></span></div>';
				}
				if(weibo=="1")
				{
					threeWeibo='<div class="weibo js_weibo js_login_weibo"><span></span></div>';
				}	
				if(weixin=="1")
				{
					threeWeixin='<div class="weixin js_weixin js_login_weixin"><span></span></div>';
				} 

									var		lower_html='</article><article>\
												<div class="tips"></div>\
											</article>\
											<article>\
												<div class="warring js_login_warring">请输入手机号码</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="phone  js_login_phone_input" type="text" placeholder="输入手机号码" maxlength="11">\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_login_pass_input" type="password" placeholder="输入密码">\
												</div>\
												<p><span class="login-btn fl js-reg">注册</span><span class="login-btn fr js-forget">忘记密码</span></p>\
												<p>登录即表示您同意<em><a style="color: #da5537;" href="./index.php?m=page&a=agreement" target="_blank">《用户协议》</a></em></p>\
												<a class="submit js_login_submit get_none">确认</a>\
											</article>\
											<p class="other_login_tip"></p>\
										</div>',
					reg_html='<div class="login_pop js_reg_pop">\
											<div class="title">\
												<span class="close js_close js_login_close"></span>\
												注 册\
											</div>\
											<article>';
						var signin='</article>\
											<article>\
												<div class="tips"></div>\
											</article>\
											<article>\
												<div class="warring js_reg_warring">请输入手机号码</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="phone js_reg_phone_input" type="text" placeholder="输入手机号码" maxlength="11">\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_reg_pass_input" type="password" placeholder="输入密码" >\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_reg_repass_input" type="password" placeholder="输入确认密码" >\
												</div>\
												<div class="key_con">\
													<div class="keyBorder">\
														<i class="keyIcon"></i>\
														<input class="key js_reg_code_input" type="text" placeholder="输入验证码" maxlength="6">\
													</div>\
													<a class="get_none js_reg_getcode">获取验证码</a>\
												</div>\
												<p><span class="js-login login-btn fl">登录</span><span class="login-btn fr js-forget">忘记密码</span></p>\
												<p>注册即表示您同意<em><a style="color: #da5537;" href="./index.php?m=page&a=agreement" target="_blank">《用户协议》</a></em></p>\
												<a class="submit js_reg_submit get_none">确认</a>\
											</article>\
											<p class="other_login_tip"></p>\
											<div class="login_popbox js_reg_popbox">\
												<div class="inner">\
													<i class="close"></i>\
													<div class="warring js_captcha_warring"></div>\
													<input class="js_reg_captcha_input" type="text" placeholder="请输入右侧字符" maxlength="20"/>\
													<img src=""/ class="js_reg_captcha_img">\
													<a class="js_reg_captcha get_none">确定</a>\
												</div>\
											</div>\
										</div>',	
					forget_html='<div class="login_pop js_forget_pop">\
											<div class="title">\
												<span class="close js_close js_login_close"></span>\
												忘记密码\
											</div>\
											<article style="display:none;">\
												<div class="weibo js_weibo js_login_weibo"><span></span></div>\
												<div class="weixin js_weixin js_login_weixin"><span></span></div>\
												<div class="qq js_qq js_login_qq"><span></span></div>\
											</article>\
											<article>\
												<div class="tips" style="display:none;"></div>\
											</article>\
											<article>\
												<div class="warring js_forget_warring">请输入手机号码</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="phone js_forget_phone_input" type="text" placeholder="输入手机号码" maxlength="11">\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_forget_pass_input" type="password" placeholder="输入新密码" >\
												</div>\
												<div class="phoneArea">\
													<i class="phoneIcon"></i>\
													<input class="pass js_forget_repass_input" type="password" placeholder="输入确认密码" >\
												</div>\
												<div class="key_con">\
													<div class="keyBorder">\
														<i class="keyIcon"></i>\
														<input class="key js_forget_code_input" type="text" placeholder="输入验证码" maxlength="6">\
													</div>\
													<a class="get_none js_forget_getcode">获取验证码</a>\
												</div>\
												<p><span class="login-btn fl js-reg">注册</span><span class="login-btn fr js-login">登录</span></p>\
												<a class="submit js_forget_submit get_none">确认</a>\
											</article>\
											<p class="other_login_tip"></p>\
											<div class="login_popbox js_forget_popbox">\
												<div class="inner">\
													<i class="close"></i>\
													<div class="warring js_forget_captcha_warring"></div>\
													<input class="js_forget_captcha_input" type="text" placeholder="请输入右侧字符" maxlength="20"/>\
													<img src=""/ class="js_forget_captcha_img">\
													<a class="js_forget_captcha get_none">确定</a>\
												</div>\
											</div>',								
					login_html_bg = '<div class="login_pop_cover js_login_pop_cover"></div>';
	
			this.dombody.append(login_html_bg+login_html+threeQQ+threeWeibo+threeWeixin+lower_html+reg_html+threeQQ+threeWeibo+threeWeixin+signin+forget_html);
	},
	addEvent:function(){
		this.js_login_pop=$(".js_login_pop"),
		this.js_login_pop_cover=$(".js_login_pop_cover"),
		this.js_reg_pop=$(".js_reg_pop"),
		this.js_forget_pop=$(".js_forget_pop"),
		this.js_close=$(".js_close"),
		this.js_login_phone_input=$(".js_login_phone_input"),
		this.js_login_pass_input=$(".js_login_pass_input"),
		this.js_login_submit=$(".js_login_submit"),
		
		this.js_reg_phone_input=$(".js_reg_phone_input"),
		this.js_reg_pass_input=$(".js_reg_pass_input"),
		this.js_reg_repass_input=$(".js_reg_repass_input"),
		this.js_reg_code_input=$(".js_reg_code_input"),
		this.js_reg_getcode=$(".js_reg_getcode"),
		this.js_reg_popbox=$(".js_reg_popbox");
		this.popboxclose=$(".login_popbox .close");
		this.js_reg_captcha_input=$(".js_reg_captcha_input"),
		this.js_reg_captcha_img=$(".js_reg_captcha_img"),
		this.js_reg_captcha=$(".js_reg_captcha");
		this.js_reg_submit=$(".js_reg_submit");
		
		this.js_forget_phone_input=$(".js_forget_phone_input"),
		this.js_forget_pass_input=$(".js_forget_pass_input"),
		this.js_forget_repass_input=$(".js_forget_repass_input"),
		this.js_forget_code_input=$(".js_forget_code_input"),
		this.js_forget_getcode=$(".js_forget_getcode"),
		this.js_forget_popbox=$(".js_forget_popbox");
		this.js_forget_captcha_input=$(".js_forget_captcha_input"),
		this.js_forget_captcha_img=$(".js_forget_captcha_img"),
		this.js_forget_captcha=$(".js_forget_captcha");
		this.js_forget_submit=$(".js_forget_submit");
		
		var _this=this;
		$(".hd-login .no-login").on("click",function(e){
			e.preventDefault(), _this.login()
		}), 
		$(".js-login").on("click",function(e){
			e.preventDefault(), _this.login()
		}), 
		$(".js-reg").on("click",function(e){
			e.preventDefault(), _this.reg()
		}), 
		$(".js-forget").on("click",function(e){
			e.preventDefault(), _this.forget()
		}), 
		$("#beloginBox .login").on("click",function(e){
			e.preventDefault(), _this.login()
		}),
		$("#beloginBox .reg").on("click",function(e){
			e.preventDefault(), _this.reg()
		}),
		$(".hd-login .logout").click(function(e) {
				e.preventDefault(), _this.logout()
		}), 
		$(".hd-login .already-login .icon-more").on("click", function() {
				$(".icon-more-ed").length === 0 ? $(".icon-more").addClass("icon-more-ed") : $(".icon-more").removeClass("icon-more-ed"), $(".userinfo").fadeToggle(300)
		}), 
		$(".hd-nav .more").hover(function(e) {
			$(this).addClass("hover")
		}, function(e) {
			$(this).removeClass("hover")
		}).on("click", ".link", function(e) {
			e.preventDefault()
		}),
		_this.js_close.on("click",function(){
			_this.closePop()
		})
		_this.popboxclose.on("click",function(){
			$(".login_popbox").fadeOut(300)
		})
		
		$(".js_login_weibo").click(function() {
			window.location.href = "index.php?g=home&m=User&a=weibo";
		}), $(".js_login_weixin").click(function() {
			window.location.href = "index.php?g=home&m=User&a=weixin";
		}), $(".js_login_qq").click(function() {
			/* alert("等待第三方配置...") */
			window.location.href = "index.php?g=home&m=User&a=qq";
		})		
	},
	closePop:function(){
		this.js_login_pop.fadeOut(300),
		this.js_reg_pop.fadeOut(300),
		this.js_forget_pop.fadeOut(300),
		this.js_login_pop_cover.fadeOut(300),
		$(".js_reg_pop input").val("");
		$(".js_login_pop input").val("");
		$(".js_forget_pop input").val("");
	
	},
	loginVerify:function(){
		var _this=this;
		var phone=_this.js_login_phone_input,pass=_this.js_login_pass_input,login=_this.js_login_submit;
		
		phone.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length > 10 && W()
		})
		pass.on("keyup input propertychange", function() {
			W()
		})

		function W(){
			d = $.trim(phone.val()), v = $.trim(pass.val()), d.length > 10 && v.length > 5 ? Y() : N()
		}
		function Y() {
			login.removeClass("get_none").addClass("get_key").unbind().click(L)
		}
		function N() {
			login.unbind().removeClass("get_key").addClass("get_none")
		}
		function L(){
			_this.dologin();
		}
	},
	loginWarring:function(msg){
		var e = null,t, n;
		t = $(".login_popbox"), t.is(":hidden") ? n = $(".js_"+this.type+"_warring") : n = $(".login_popbox .js_"+this.type+"_warring"), n.text(msg).show(), e && (window.clearTimeout(e), e = null), e = window.setTimeout(function() {
					e = null, n.fadeOut(300)
				}, 5e3)
	},
	login:function(){
		this.js_login_pop_cover.fadeIn(300),
		this.js_reg_pop.fadeOut(300),
		this.js_forget_pop.fadeOut(300),
		this.js_login_pop.fadeIn(300),
		this.type='login',
		this.loginVerify()
	},
	dologin:function(){
		var _this=this,
				phone=this.js_login_phone_input.val(),
				pass =this.js_login_pass_input.val();
		$.ajax({
			url: _this.login_url,
			data: {mobile:phone,pass:pass},
			type: "GET",
			dataType: "jsonp",
			jsonp: "callback",
			cache: !1,
			success:function(data){
				if(data && data.errno ==0){

					window.location.reload();

					/*登录环信私信账号--start*/

						handlePageLimit();

						var total = getPageCount();//获取登录数
						if ( total > PAGELIMIT ) {
							alert('私信聊天最多支持' + PAGELIMIT + '个resource同时登录');
							return;
						}

						 
						var user = data['userid'];
						
						var pass = "fmscms"+user;
						if (user == '' || pass == '') {
							alert("私信窗口创建失败！");
							return;
						}
							
						
						//根据用户名密码登录系统
						conn.open({
							apiUrl : Easemob.im.config.apiURL,
							user : user,
							pwd : pass,
							//连接时提供appkey
							appKey : Easemob.im.config.appkey
						});   

					/*登录环信私信账号--end*/

				}else{
					_this.loginWarring(data.errmsg);
					return !1;
				}
				
			}
		})
		
	},
	regVerify:function(){
		var _this=this;
		var phone=_this.js_reg_phone_input,pass=_this.js_reg_pass_input,repass=_this.js_reg_repass_input,code=_this.js_reg_code_input,getcode=_this.js_reg_getcode,popbox=_this.js_reg_popbox,captcha=_this.js_reg_captcha_input,captchaimg=_this.js_reg_captcha_img,captchasub=_this.js_reg_captcha,reg=_this.js_reg_submit;
		
		phone.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length > 10 ? ( C() ?  GY(): GN() ): GN() , W()
		})
		pass.on("keyup input propertychange", function() {
			W()
		})
		repass.on("keyup input propertychange", function() {
			W()
		})
		code.on("keyup input propertychange", function() {
			W()
		})
		captcha.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length == 4 ? CapY(): CapN() 
		})
		captchaimg.on("click",function(){
			getCaptcha()
		})
		function C(){
			if(!_this.checkPhone.test(phone.val())){
				_this.loginWarring("您输入的手机号有误")
				return !1;
			}
			return !0;
		}
		/* 点击 获取验证码 */
		function G(){
			popbox.fadeIn(300),getCaptcha()
		}
		function GY() {
			getcode.hasClass("login_counting") || getcode.removeClass("get_none").addClass("get_key").unbind().click(G)
		}
		function GN() {
			getcode.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 计时器 */
		function O(){
			function r(e) {
				getcode.text(e)
			}
			GN(), CapN();
			var e = 60,
				n = window.setInterval(function() {
					if (e > 0) {
						var i = e--+"s 重新获取";
						getcode.addClass("login_counting"), r(i)
					} else window.clearInterval(n), n = null, r("获取验证码"), getcode.removeClass("login_counting"), C() && GY()
				}, 1e3)			
		}
		/* 验证码 */
		function Cap(){
				_this.type='captcha',getCodeU()
		}
		function CapY(){
			captchasub.removeClass("get_none").addClass("get_key").unbind().click(Cap)
		}
		function CapN(){
			captchasub.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 注册页面 */
		function W(){
			d = $.trim(phone.val()), v = $.trim(pass.val()), rv = $.trim(repass.val()), c = $.trim(code.val()), d.length > 10 && v.length > 5 && rv.length > 5 && c.length == 6 ? Y() : N()
		}
		function Y() {
			reg.removeClass("get_none").addClass("get_key").unbind().click(R)
		}
		function N() {
			reg.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 发送短信验证码 */
		function getCodeU(){
			$.ajax({
				url: _this.code_url,
				data: {mobile:phone.val(),captcha:captcha.val()},
				type: "GET",
				dataType: "jsonp",
				jsonp: "callback",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.type='reg',popbox.fadeOut(300),O()
					}else{
						_this.loginWarring(data.errmsg),CapN(),getCaptcha();
						return !1;
					}
					
				}
			})			
		}
		/* 图片验证码 */
		function getCaptcha(){
			$.ajax({
				url: _this.captcha_url,
				data: {},
				type: "GET",
				dataType: "jsonp",
				jsonp: "callback",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.js_reg_captcha_img.attr("src",data.data.captcha+"&v=" + parseInt(Math.random() * 1e8, 10));
					}else{
						_this.loginWarring(data.errmsg);
						return !1;
					}
					
				}
			})			
		}
		function R(){
			if(!_this.checkPass.test(pass.val())){
				_this.loginWarring("密码必须包含字母和数字，6-12位")
				return !1;
			}
			if( pass.val()!=repass.val() ){
				_this.loginWarring("两次密码不一致")
				return !1;
			}
			_this.doreg();
		}		
	},
	reg:function(){
		this.js_login_pop_cover.fadeIn(300),
		this.js_login_pop.fadeOut(300),		
		this.js_forget_pop.fadeOut(300),		
		this.js_reg_pop.fadeIn(300),
		this.type='reg',		
		this.regVerify()
	},
	doreg:function(){
		var _this=this,
				phone=this.js_reg_phone_input.val(),
				pass =this.js_reg_pass_input.val(),
				code =this.js_reg_code_input.val();
		$.ajax({
			url: _this.reg_url,
			data: {mobile:phone,pass:pass,code:code},
			type: "GET",
			dataType: "jsonp",
			jsonp: "callback",
			cache: !1,
			success:function(data){
				if(data && data.errno ==0){
					//获取注册用户的id
					var userid=data['userid'];


					//环信注册新用户操作方法
					
						var user = userid;
						var pass = "fmscms"+userid;  //密码规则
						
						
						var options = {
							username : user,
							password : pass,
							
							appKey : Easemob.im.config.appkey,
							success : function(result) {
								layer.msg("注册成功！");
								setTimeout(function(){  //使用  setTimeout（）方法设定定时1500毫秒
									window.location.reload();//页面刷新
								},1500);
								
							},
							error : function(e) {
								
							},
							apiUrl : Easemob.im.config.apiURL
						};
						Easemob.im.Helper.registerUser(options);
					


				}else{
					_this.loginWarring(data.errmsg);
					return !1;
				}
				
			}
		})				
	},
	logout:function(){
		var _this=this;
		$.ajax({
			url: _this.loginout_url,
			data: {},
			type: "GET",
			dataType: "jsonp",
			jsonp: "callback",
			cache: !1,
			success:function(data){
				if(data && data.errno ==0){

					/*将环信私信功能注销掉--start*/
					
					conn.stopHeartBeat();
					conn.close();
					clearPageSign();
					/*将环信私信功能注销掉--end*/

					window.location.reload();
				}else{
					_this.loginWarring(data.errmsg);
					return !1;
				}
				
			}
		})		
		
	},
	forgetVerify:function(){
		var _this=this;
		var phone=_this.js_forget_phone_input,pass=_this.js_forget_pass_input,repass=_this.js_forget_repass_input,code=_this.js_forget_code_input,getcode=_this.js_forget_getcode,popbox=_this.js_forget_popbox,captcha=_this.js_forget_captcha_input,captchaimg=_this.js_forget_captcha_img,captchasub=_this.js_forget_captcha,forget=_this.js_forget_submit;
		
		phone.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length > 10 ? ( C() ?  GY(): GN() ): GN() , W()
		})
		pass.on("keyup input propertychange", function() {
			W()
		})
		repass.on("keyup input propertychange", function() {
			W()
		})
		code.on("keyup input propertychange", function() {
			W()
		})
		captcha.on("keyup input propertychange", function() {
			var e = $.trim($(this).val());
			e.length == 4 ? CapY(): CapN() 
		})
		captchaimg.on("click",function(){
			getCaptcha()
		})
		function C(){
			if(!_this.checkPhone.test(phone.val())){
				_this.loginWarring("您输入的手机号有误")
				return !1;
			}
			return !0;
		}
		/* 点击 获取验证码 */
		function G(){
			popbox.fadeIn(300),getCaptcha()
		}
		function GY() {
			getcode.hasClass("login_counting") || getcode.removeClass("get_none").addClass("get_key").unbind().click(G)
		}
		function GN() {
			getcode.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 计时器 */
		function O(){
			function r(e) {
				getcode.text(e)
			}
			GN(), CapN();
			var e = 60,
				n = window.setInterval(function() {
					if (e > 0) {
						var i = e--+"s 重新获取";
						getcode.addClass("login_counting"), r(i)
					} else window.clearInterval(n), n = null, r("获取验证码"), getcode.removeClass("login_counting"), C() && GY()
				}, 1e3)			
		}
		/* 验证码 */
		function Cap(){
				_this.type='forget_captcha',getCodeU()
		}
		function CapY(){
			captchasub.removeClass("get_none").addClass("get_key").unbind().click(Cap)
		}
		function CapN(){
			captchasub.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 忘记密码页面 */
		function W(){
			d = $.trim(phone.val()), v = $.trim(pass.val()), rv = $.trim(repass.val()), c = $.trim(code.val()), d.length > 10 && v.length > 5 && rv.length > 5 && c.length == 6 ? Y() : N()
		}
		function Y() {
			forget.removeClass("get_none").addClass("get_key").unbind().click(R)
		}
		function N() {
			forget.unbind().removeClass("get_key").addClass("get_none")
		}
		/* 发送短信验证码 */
		function getCodeU(){
			$.ajax({
				url: _this.code_url,
				data: {mobile:phone.val(),captcha:captcha.val()},
				type: "GET",
				dataType: "jsonp",
				jsonp: "callback",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.type='forget',popbox.fadeOut(300),O()
					}else{
						_this.loginWarring(data.errmsg),CapN(),getCaptcha();
						return !1;
					}
					
				}
			})			
		}
		/* 图片验证码 */
		function getCaptcha(){
			$.ajax({
				url: _this.captcha_url,
				data: {},
				type: "GET",
				dataType: "jsonp",
				jsonp: "callback",
				cache: !1,
				success:function(data){
					if(data && data.errno ==0){
						_this.js_forget_captcha_img.attr("src",data.data.captcha+"&v=" + parseInt(Math.random() * 1e8, 10));
					}else{
						_this.loginWarring(data.errmsg);
						return !1;
					}
					
				}
			})			
		}
		function R(){
			if(!_this.checkPass.test(pass.val())){
				_this.loginWarring("密码必须包含字母和数字，6-12位")
				return !1;
			}
			if( pass.val()!=repass.val() ){
				_this.loginWarring("两次密码不一致")
				return !1;
			}
			_this.doforget();
		}				
	},
	forget:function(){
		this.js_login_pop_cover.fadeIn(300),
		this.js_reg_pop.fadeOut(300),
		this.js_login_pop.fadeOut(300),
		this.js_forget_pop.fadeIn(300),
		this.type='forget',
		this.forgetVerify()		
	},
	doforget:function(){
		var _this=this,
				phone=this.js_forget_phone_input.val(),
				pass =this.js_forget_pass_input.val(),
				code =this.js_forget_code_input.val();
		$.ajax({
			url: _this.forget_url,
			data: {mobile:phone,pass:pass,code:code},
			type: "GET",
			dataType: "jsonp",
			jsonp: "callback",
			cache: !1,
			success:function(data){
				if(data && data.errno ==0){
					$(".js_reg_pop input").val("");
					$(".js_login_pop input").val("");
					$(".js_forget_pop input").val("");
					alert("重置成功");
					_this.login();
				}else{
					_this.loginWarring(data.errmsg);
					return !1;
				}
				
			}
		})						
		
	}
	
	
}
$(function(){
	Login.init();
	$(".j-follow").on("click",function(t) {
		t.preventDefault(),
		$(this).follow()
	});
	var n = {
		add: function(e) {
			e.addClass("followed").html("已关注")
		},
		cancel: function(e) {
			e.removeClass("followed").html("+ 关注")
		},
		error: function(e) {
			var t = {
				add: "关注失败，请重试",
				cancel: "取消失败，请重试"
			},
				n = $('<div class="follow-tips"><h4>很抱歉</h4><p>' + t[e] + "</p></div>");
			$("body>.follow-tips").remove(), n.appendTo("body"), setTimeout(function() {
				$("body>.follow-tips").addClass("transition")
			}, 100), setTimeout(function() {
				$("body>.follow-tips").removeClass("transition")
			}, 2e3), setTimeout(function() {
				$("body>.follow-tips").remove()
			}, 2200)
		}
	};	
	$.fn.follow = function(e, r, i) {
		var s = $(this);
		r = r || (s.hasClass("followed") ? "cancel" : "add"), e = e || s.closest("[data-userid]").data("userid");
		if(!_DATA.user){
			$(".hd-login .no-login").click();
			return !1;
		}

			var t = './index.php?m=user&a=follow_',
				o = i || n[r];
			$.ajax(t + r, {
				dataType: "jsonp",
				data: {
					touid: e,
					fmt: "jsonp"
				},
				jsonp: "_callback",
				success: function(e) {
					if (!e.errno){
						$("#author-info .follows h4").html(e.data.follows);
						$("#author-info .fans h4").html( e.data.fans);
						return o(s, r);
					} 
					n.error(r)
				}
			})
	}	
});


function beforeSearch(){
	//获取输入框的值
	var uid=$("#searchfriend").val();
	searchMember(uid);	
}


function searchMember(uid){
    var searchInfo=uid;
    if(searchInfo=="请输入用户id"||searchInfo==""){
        alert("请输入用户id！");
        return;
    };
    
    //将searchResult清空
    $(".searchResult").html("");

    $.ajax({
    	type:'GET',
    	url:'./index.php?g=Home&m=User&a=searchMember',
    	data:{keyword:searchInfo},
    	dataType:'json',
    	success:function(data){
    		//console.log(data);
    		$("#searchfriend").val("");
    		if(data.code==1){
    			var msg="查询失败！";
    		$(".searchResult").html(msg);
    		$(".searchResult").css("height",25);
    		$("#contractlist11").css('height',416);//改变陌生人列表高度，让左右对齐
    		$("#momogrouplist").css("height",412).css("overflow",'auto');
    		$("#momogrouplistUL").css("height",412).css("overflow",'auto');

    		}else if(data.code==0){
    			var array=data['info'];
    			//console.log(array);
    			//判断陌生人列表中是否存在该id的li，如果存在，将input清空
    			var momoLilength=$("#momogrouplistUL").children("#"+array['id']).length;

    			if(momoLilength==0){
    				var msg="<li class='searchUser' type='chat' class='offline' className='offline' onclick=chooseContactDivClick(this) id='"+array['id']+"'><img style='float:left;' width='' src='"+array['avatar']+"'><span class='chatUserName'>"+array['user_nicename']+"</span><div class='clearboth'></div><li>";
	    			
	    			$(".searchResult").append(msg);
	    			$(".searchResult").addClass('searchMsg');
	    			$(".searchResult").height(60);
	    			$("#momogrouplistUL").css("height",378).css("overflow",'auto');
	    			$("#momogrouplist").css('height', 376);
    				$("#contractlist11").css('height', 380);
    				

    			}else{
    				$("#searchfriend").val("");
    				$(".searchResult").html();
    				$(".searchResult").height(0).removeClass('searchMsg');
    				$("#contractlist11").css('height', 440);
    				$("#momogrouplistUL").css("height",437).css("overflow",'auto');
    				alert("该用户已经在列表中");
    			}
    			
    			

    		}else if(data.code==2){
    			alert("请先登录再操作");
    			$(this).login();
    			return;
    		}

    		
    	},
    	error:function(){
    		var msg="查询失败！!";
    		$(".searchResult").html(msg);
    		$(".searchResult").css("height",25);
    		$(".accordion-group").css("height",416);
    		$("#contractlist11").css('height',416);//改变陌生人列表高度，让左侧聊天人列表高度和右侧聊天窗口高度对齐
    		$("#momogrouplist").css("height",412).css("overflow",'auto');
    		$("#momogrouplistUL").css("height",412).css("overflow",'auto');


    	}
    });
    
}