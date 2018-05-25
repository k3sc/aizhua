//websocket 断线重链
(function(global,factory){if(typeof define==="function"&&define.amd){define([],factory)}else{if(typeof module!=="undefined"&&module.exports){module.exports=factory()}else{global.ReconnectingWebSocket=factory()}}})(this,function(){if(!("WebSocket" in window)){return}function ReconnectingWebSocket(url,protocols,options){var settings={debug:false,automaticOpen:true,reconnectInterval:1000,maxReconnectInterval:30000,reconnectDecay:1.5,timeoutInterval:2000,maxReconnectAttempts:null,binaryType:"blob"};if(!options){options={}}for(var key in settings){if(typeof options[key]!=="undefined"){this[key]=options[key]}else{this[key]=settings[key]}}this.url=url;this.reconnectAttempts=0;this.readyState=WebSocket.CONNECTING;this.protocol=null;var self=this;var ws;var forcedClose=false;var timedOut=false;var eventTarget=document.createElement("div");eventTarget.addEventListener("open",function(event){self.onopen(event)});eventTarget.addEventListener("close",function(event){self.onclose(event)});eventTarget.addEventListener("connecting",function(event){self.onconnecting(event)});eventTarget.addEventListener("message",function(event){self.onmessage(event)});eventTarget.addEventListener("error",function(event){self.onerror(event)});this.addEventListener=eventTarget.addEventListener.bind(eventTarget);this.removeEventListener=eventTarget.removeEventListener.bind(eventTarget);this.dispatchEvent=eventTarget.dispatchEvent.bind(eventTarget);function generateEvent(s,args){var evt=document.createEvent("CustomEvent");evt.initCustomEvent(s,false,false,args);return evt}this.open=function(reconnectAttempt){ws=new WebSocket(self.url,protocols||[]);ws.binaryType=this.binaryType;if(reconnectAttempt){if(this.maxReconnectAttempts&&this.reconnectAttempts>this.maxReconnectAttempts){return}}else{eventTarget.dispatchEvent(generateEvent("connecting"));this.reconnectAttempts=0}if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","attempt-connect",self.url)}var localWs=ws;var timeout=setTimeout(function(){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","connection-timeout",self.url)}timedOut=true;localWs.close();timedOut=false},self.timeoutInterval);ws.onopen=function(event){clearTimeout(timeout);if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onopen",self.url)}self.protocol=ws.protocol;self.readyState=WebSocket.OPEN;self.reconnectAttempts=0;var e=generateEvent("open");e.isReconnect=reconnectAttempt;reconnectAttempt=false;eventTarget.dispatchEvent(e)};ws.onclose=function(event){clearTimeout(timeout);ws=null;if(forcedClose){self.readyState=WebSocket.CLOSED;eventTarget.dispatchEvent(generateEvent("close"))}else{self.readyState=WebSocket.CONNECTING;var e=generateEvent("connecting");e.code=event.code;e.reason=event.reason;e.wasClean=event.wasClean;eventTarget.dispatchEvent(e);if(!reconnectAttempt&&!timedOut){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onclose",self.url)}eventTarget.dispatchEvent(generateEvent("close"))}var timeout=self.reconnectInterval*Math.pow(self.reconnectDecay,self.reconnectAttempts);setTimeout(function(){self.reconnectAttempts++;self.open(true)},timeout>self.maxReconnectInterval?self.maxReconnectInterval:timeout)}};ws.onmessage=function(event){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onmessage",self.url,event.data)}var e=generateEvent("message");e.data=event.data;eventTarget.dispatchEvent(e)};ws.onerror=function(event){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onerror",self.url,event)}eventTarget.dispatchEvent(generateEvent("error"))}};if(this.automaticOpen==true){this.open(false)}this.send=function(data){if(ws){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","send",self.url,data)}return ws.send(data)}else{throw"INVALID_STATE_ERR : Pausing to reconnect websocket"}};this.close=function(code,reason){if(typeof code=="undefined"){code=1000}forcedClose=true;if(ws){ws.close(code,reason)}};this.refresh=function(){if(ws){ws.close()}}}ReconnectingWebSocket.prototype.onopen=function(event){};ReconnectingWebSocket.prototype.onclose=function(event){};ReconnectingWebSocket.prototype.onconnecting=function(event){};ReconnectingWebSocket.prototype.onmessage=function(event){};ReconnectingWebSocket.prototype.onerror=function(event){};ReconnectingWebSocket.debugAll=false;ReconnectingWebSocket.CONNECTING=WebSocket.CONNECTING;ReconnectingWebSocket.OPEN=WebSocket.OPEN;ReconnectingWebSocket.CLOSING=WebSocket.CLOSING;ReconnectingWebSocket.CLOSED=WebSocket.CLOSED;return ReconnectingWebSocket});

var reconnFlag = false;
var timerID = 0;
var modefontcolor = [];
//modefontcolor数组中一维代表登录模式
//1电脑直播手机观看
//2手机直播手机观看
//3电脑观看
//数组二维代表字体颜色(0聊天1礼物)
modefontcolor[1] = ['#ffffff','#ff9900','yellow'];
modefontcolor[2] = ['#717171','#ff9900','red'];
modefontcolor[3] = ['#717171','#ff9900','red'];
//socket数据
var SocketIO = {

    _firstLogin:false,
    _initConnect:function() {
        console.log('正在建立连接...');
        try{
            socket  =  new WebSocket('ws://139.129.19.190:7272');
        }catch(e){
            console.log('连接异常 ： '+e);
            return;
        }
        SocketIO._wbSocket = socket;

        socket.onclose = function() {
            console.log('连接关闭.');

            if (!reconnFlag) {
                timerID = setInterval(SocketIO._initConnect,2000);
                reconnFlag = true;
            }
        }

        socket.onopen = function() {
            

            if (reconnFlag) {
                window.clearInterval(window.timerID);
                timerID=0;
                reconnFlag = false;
            }
            if(!SocketIO._firstLogin) {
                var data = {};

                data._method_ = "login";
                data.room_id  = room_id;
                data.user_name = User.nickname;
                data.user_id = User.id;
                data.levelid = User.level;
                data.token   = User.token;
                

                SocketIO._sendMsg(JSON.stringify(data));
                SocketIO._firstLogin = true;
            }
            
        }

        socket.onmessage = SocketIO._msgReceive
    },

    _chatMessage:function(msg){
        var data = {};
        data._method_ = "SendPubMsg";
        data._type_ = "flyMsg";
        data.fly=fly;
        data.levelid = User.level;
        data.content   = msg;
        data.client_name = User.nickname;
        SocketIO._sendMsg(JSON.stringify(data));
        console.log(data.levelid);
    },

    _sendMsg:function(msgBuf){
        if(msgBuf!=null&&msgBuf!='undefined'){
            SocketIO._wbSocket.send(msgBuf);
        }else{
            console.log('发送消息为空!');
        }
    },

    _msgReceive:function(event) {
        var data = JSON.parse(event.data);
        console.log(data);
        eval('_chat._func_' + data.type  + '(data)');

    }
}

//消息逻辑处理
var _chat = {
    remove_msg:function(){
        // while ($("#chat_hall>p").length >= 10) {
        //         $("#chat_hall").children().first().remove();
        //     }
        if($("#chat_hall>p").length > 100) {
            $("#chat_hall>p").slice(0,50).remove()
        }
    },
    gift_msg:function(count,html) {
        if(count != 0){
            
            $(".msg-box .msg-con").append(html);
            _chat.remove_msg();
            setTimeout("_chat.gift_msg("+ (count-1) +",'"+html+"')",500);
            var scrojh=$("#upchat_hall")[0].scrollHeight;
            $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
        }
    },

    _func_login: function(data) {
        _chat.show_message(data.client_name,"来到了直播间",data.levelid,1,0,data.user_id);
    },

    _func_sysmsg: function(data) {
        _chat.show_message("系统提示",data.content,"","",2,'');
    },

    _func_sendGift: function (data) {
        console.log(data);
        msgHtml ='<p><label class ="user_nickname" user_id='+data.from_user_id+'><img  src="/style/meilibo/dist/images/level/public_icon_vip'+data.levelid+'@2x.png" style="margin-bottom: -2px;margin-right:2px;" width="35" height="15">'+data.from_client_name+'</label>：送了1个<font style="'+modefontcolor[mode][1]+'";> '+data.giftName+'</font></p>';
        setTimeout("_chat.gift_msg("+ (data.giftCount) +",'"+msgHtml+"')",500);
        if(data.isred==2||data.isred==5||data.isred==6||data.isred==4||data.isred==3){
            Ctrfn.bigshowgift(data.giftId);
        }
        Ctrfn.sendShow(data.giftCount,data.from_client_name,data.from_client_avatar,data.giftPath,data.giftName);
        
    },
    _func_onLineClient: function(data) {
        $(".userinfo .unum").html(data.all_num);

        var onLineUserhtml = '';

        for (var i=0;i<10;i++) {
            //console.log(typeof(data.client_list[i]));
            if( typeof(data.client_list[i]) == "object") {
                var errorImg='/style/avatar/0/0_big.jpg';
                var srcImg=_chat._isHasImg(avatarPath(data.client_list[i].user_id));
                if(srcImg){
                    onLineUserhtml += '<li><img user_id="'+data.client_list[i].user_id+'" src="'+avatarPath(data.client_list[i].user_id)+'"></li>';
                }else{
                    onLineUserhtml += '<li><img user_id="'+data.client_list[i].user_id+'" src="'+errorImg+'"></li>';
                }
            }
        }
        $("#userpic").html(onLineUserhtml);

    },
    _isHasImg:function(pathImg){
            var ImgObj=new Image();
            ImgObj.src= pathImg;
             if(ImgObj.fileSize > 0 || (ImgObj.width > 0 && ImgObj.height > 0))
             {
               return true;
             } else {
               return false;
            }
        },
    _func_error:function(data) {
      console.log(data);
      _chat.show_message("系统提示",data.content,"","",2,'');
    },

    _func_SendPubMsg:function(data) {
        if(data.type=="SendPubMsg"){
            _chat.show_message(data.from_client_name,data.content,data.levelid,1,1,data.from_user_id);
            if(data.fly =="FlyMsg"){
                var div='<div><div style="margin-right:5px;"><img onerror="this.src=\'/style/avatar/0/0_big.jpg\'"  src="'+data.avatar+'"></div><div><p class="nickname">"'+data.from_client_name+'":</p><p>'+data.content+'</p></div></div>';
                $(".chat_barrage_box").append(div);
                Ctrfn.init_screen();
            }
        }
    },

    _func_ping:function(data) {
        console.log(data);

        var msg = {};
        msg._method_ = "pong";

        SocketIO._sendMsg(JSON.stringify(msg));
    },

    _func_logout: function(data) {
        console.log(data);
    },
    //showType表示消息在不同聊天环境下的样式 eg:
    //1:PC端直播 手机直播 手机观看 
    //2:pc端直播 手机直播 PC端观看
    //msg_type消息种类 0:聊天2:系统提示
    show_message: function(nickName,msg,level,showType,msg_type,user_id ) {
        if(level==null){
            level=0;
        }
        var color = modefontcolor[mode][0];
        var _msg = '';
        if(msg_type==0){
                _msg = '<p><label class ="user_nickname" user_id='+user_id+'><img src="/style/meilibo/dist/images/level/public_icon_vip'+level+'@2x.png" style="margin-bottom: -2px;margin-right:2px;" width="35" height="15" >'+nickName+'</label> : <font color="'+color+'">'+msg+'</font></p>'  
        }else if(msg_type == 1) {
                _msg = '<p><label class ="user_nickname" user_id='+user_id+'><img src="/style/meilibo/dist/images/level/public_icon_vip'+level+'@2x.png" style="margin-bottom: -2px;margin-right:2px;" width="35" height="15" >'+nickName+'</label> : <font color="'+color+'">'+msg+'</font></p>'  
        } else if(msg_type == 2) {
            _msg = '<p><font color="'+modefontcolor[mode][2]+'" class="firstfont">'+msg+'</font></p>'
        } else {
            if(showType==0){
                _msg = '<p><label class ="user_nickname" user_id='+user_id+'><img src="/style/meilibo/dist/images/level/public_icon_vip'+level+'@2x.png" style="margin-bottom: -2px;margin-right:2px;" width="35" height="15" >'+nickName+'</label> : <font color="'+color+'">'+msg+'</font></p>'  
            }
        }
        
        $('#chat_hall').append(_msg);
        _chat.remove_msg();
        var scrojh=$("#upchat_hall")[0].scrollHeight;
        $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
    }
}



function sysmsg(msg) {
    $("#chat_hall").append("<p><font color='greenyellow'>" + msg + "<br /></font></p>");
}

function onLineClient(data) {
    $(".userinfo .unum").html(data.all_num);
    console.log("dddd"+data);

    var onLineUserhtml = '';
    for (var i=0;i<5;i++) {
        onLineUserhtml += '<li><img src="'+avatarPath(data.client_list[i].user_id)+'"></li>';
    }
    
    $("#userpic").html(onLineUserhtml);
}

function onUserLogin(data) {
    $("#chat_hall").append("<p><font color='greenyellow'>" + msg + "<br /></font></p>");
}

function avatarPath(path) {
    return IMG_PATH+"/avatar/"+$.md5(path.toString()).substring(0,3)+"/"+path+"_small.jpg" 
}

SocketIO._initConnect();