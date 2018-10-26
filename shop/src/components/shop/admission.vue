<template>
    <div class="content" style="margin-top: 2rem">
        此页面由爱抓分享给目标商家，说服商家入驻爱抓平台

        爱抓娃娃App开放代理

        代理三大好处

        1、玩家一次注册，代理终身提成


        2、App内专属位置展示你的店铺，玩家打开App就能看到你的店铺形象


        3、您自己编辑营销文字发送给用户App，吸引用户到你的线下店铺

        <router-link to="/shop/admission/form">
        <div class="page-button" >
            <div class="page-button-group">
                <mt-button  style="width: 80vw" type="primary">申请入驻</mt-button>
            </div>
        </div>
        </router-link>


    </div>


</template>
<script>
    export default {
        name: 'shop_admission'
    };
</script>

<style lang="css">
    .page-button{
        position: fixed;
        bottom: 15vh;
        left: 10vw;
    }
    .content {
        font-size: 0.5rem;
    }

    @component-namespace page {
        @component button {
            padding: 0 15px 15px;

            @descendent group {
                margin-bottom: 15px;

            & > * {
                    margin-bottom: 15px;
                }
            }
        }
    }
</style>