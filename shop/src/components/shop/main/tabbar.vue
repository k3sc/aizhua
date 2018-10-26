<template>
    <div class="page-tabbar">

        <mt-tab-container class="page-tabbar-container" v-model="selected">
            <mt-tab-container-item id="dataAdmin">

            </mt-tab-container-item>
            <mt-tab-container-item id="shopAdmin">
                <!--<div class="page-part" style="margin-top: 60px;">
                    这是商家中心
                </div>-->
                <!--<div style="height: 7vh;"></div>-->
                <shopadmin></shopadmin>
            </mt-tab-container-item>

            <mt-tab-container-item id="about">
                <div class="page-part">
                    这是关于爱抓
                </div>
            </mt-tab-container-item>
        </mt-tab-container>

        <mt-tabbar v-model="selected" fixed>

            <mt-tab-item id="shopAdmin" >
                <img slot="icon" src="@/assets/100x100.png">
                商家中心
            </mt-tab-item>

            <mt-tab-item id="admission" >
                <img slot="icon" src="@/assets/100x100.png">
                申请入驻/代理
            </mt-tab-item>

            <mt-tab-item id="about">
                <img slot="icon" src="@/assets/100x100.png">
                关于爱抓
            </mt-tab-item>

        </mt-tabbar>
    </div>
</template>

<script>
    import shopAdmin from "@/components/shop/admin/index.vue";
    export default {
        name: 'shop_tabbar',
        props: ['title','id','route','selecteds'],
        data() {
            return {
                selected: false
            };
        },
        components: {
            shopadmin: shopAdmin
        },
        methods:{
        },
        watch: {
            selected: function(newtabbar){
                if(newtabbar == 'admission'){
                    this.$router.push({ path: '/shop/admission' })
                }else if(newtabbar == 'about'){
                    this.$store.commit('title','关于爱抓');
                }else if(newtabbar == 'shopAdmin'){
                    //this.$router.push({ path: '/shop/admin' })
                    this.$store.commit('title','商家管理中心');
                }
            },
        }

    }
</script>

<style>
    .page-tabbar {
        overflow: hidden;
        /*height: 100vh;*/
    }

    .page-wrap {
        overflow: auto;
        height: 100%;
        padding-bottom: 100px;
    }
</style>

