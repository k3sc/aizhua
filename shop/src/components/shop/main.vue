<template>
    <div class="shop-main">
        <headers :title="title"></headers>
        <tabbar></tabbar>
        <div>{{title}}</div>
    </div>
</template>

<script>
    import header from "@/components/shop/main/header.vue";
    import tabbar from "@/components/shop/main/tabbar.vue";
    export default{
        name: "shop_main",
        props: ['title',],
        data(){
            return {}
        },
        components: {
            tabbar,
            headers: header
        },
    }

</script>