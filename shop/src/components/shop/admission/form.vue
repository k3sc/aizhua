<template>
    <div id="form">
        <headers></headers>
        <div  style="padding-bottom: 20px;">
            <!--<div style="height: 1.8rem"></div>-->
            <yd-cell-group class="demo-small-pitch" title="您的称呼">
                <yd-cell-item>
                    <span slot="left">称呼：</span>
                    <yd-input slot="right" ref="nicename" required v-model="nicename" max="20" placeholder="请输入您的称呼"></yd-input>
                </yd-cell-item>
            </yd-cell-group>

            <yd-cell-group title="填写一种联系方式，平台将安排专人与您联系">

                <yd-cell-item>
                    <span slot="left">微信号：</span>
                    <yd-input slot="right" ref="weixin"  v-model="weixin" max="20" placeholder="请输入微信号(选填)"></yd-input>
                </yd-cell-item>

                <yd-cell-item>
                    <span slot="left">QQ号：</span>
                    <yd-input slot="right" ref="qq" v-model="qq" regex="^\d{5,12}$" placeholder="请输入您的QQ号码(选填)"></yd-input>
                </yd-cell-item>

                <yd-cell-item>
                    <span slot="left">手机号：</span>
                    <yd-input slot="right" ref="mobile" v-model="mobile" regex="mobile" placeholder="请输入手机号码(选填)"></yd-input>
                </yd-cell-item>

                <yd-cell-item>
                    <span slot="left">邮箱：</span>
                    <yd-input slot="right" ref="email" v-model="email" regex="email" placeholder="请输入邮箱地址(选填)"></yd-input>
                </yd-cell-item>

            </yd-cell-group>

            <mt-checklist
                    class="page-part"
                    title="我的推广手段"
                    v-model="extendval"
                    :options="extend">
            </mt-checklist>

            <yd-cell-group v-if="qtinput">
                <yd-cell-item>
                    <yd-textarea slot="right" placeholder="请输入其它推广手段" v-model="qtVal"></yd-textarea>
                </yd-cell-item>
            </yd-cell-group>

            <yd-button-group>
                <yd-button size="large" @click.native="submit">提交</yd-button>
            </yd-button-group>

        </div>
    </div>


</template>


<script type="text/babel">
    import header from "@/components/shop/main/header.vue";
    import {Toast,MessageBox} from 'mint-ui';
    export default {
        name: "shop_admission_form",
        data() {
            return {
                extendval: [],
                nicename: '',
                weixin: '',
                qq: '',
                mobile: '',
                email: '',
                result: '',
                qtinput: false,
                qtVal: '',
                contact: {qq:'',weixin:'',email:'',mobile:''},
            }
        },
        created: function(){
            this.extend = [{
                label: '娃娃机店',
                value: 'wwjd',
                },
                {
                    label: '餐厅',
                    value: 'ct',
                },
                {
                    label: '兴趣微信群/QQ群',
                    value: 'wxandqq'
                },
                {
                    label: '其它',
                    value: 'qt'
                }];
        },
        methods: {
            submit() {
                if (!this.weixin && !this.qq && !this.mobile && !this.email){
                    Toast({
                        message: "联系方式需要选填一个",
                        position: 'bottom',
                    });
                    return;
                }

                if (!this.regex()) return
                if (!this.required()) return
                if (!this.checked()) return
            },
            regex(){
                //处理正则验证
                const regex = {qq:"QQ号格式错误",mobile:"手机号码格式错误",email:"邮箱格式错误"}
                for (const index in regex){
                    const input = this.$refs[index]
                    //${input.valid} true ${input.errorMsg} 字符不符合规则  ${input.errorCode} OT_REGEX_RULE
                    if(this[index]!=''){
                        if(`${input.valid}` == 'false'){
                            Toast({
                                message: regex[index],
                                position: 'bottom',
                            });
                            return false;
                        }
                    }
                }
                return true;
            },
            checked(){
                //验证多选一
                var checked = {extendval:"推广手段必须选一个"}
                for(var index in checked){
                    if(this[index].length <= 0){
                        Toast({
                            message: checked[index],
                            position: 'bottom',
                        });
                        return false;
                    }
                }
                return true;
            },
            required(){
                //验证必填的
                var required = {nicename:"昵称不能为空"}
                if(this.qtinput) required.qtVal = "请输入其它推广手段"
                for(var index in required){
                    var res = this.isnull(this[index])
                    if(res){
                        Toast({
                            message: required[index],
                            position: 'bottom',
                        });
                        return false;
                    }
                }
                return true;
            },
            isnull(val){
                var str = val.replace(/(^\s*)|(\s*$)/g, '');
                if (str == '' || str == undefined || str == null) {
                    return true;
                } else {
                    return false;
                }
            }
        },
        components: {
            headers: header
        },
        watch: {
            extendval: function(newdata){
                if(newdata.indexOf('qt') != -1){
                    this.qtinput = true
                }else{
                    this.qtinput = false
                }
            }
        }
    }
</script>
<style>
    .page-checklist .page-part {
        margin-top: 40px;
    }
</style>