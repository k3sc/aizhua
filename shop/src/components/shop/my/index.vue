<template>
    <div class="shop-my">
        <mains></mains>
    </div>
</template>

<script>
    import main from "@/components/shop/main.vue";
    export default{
        name: "shop_my",
        data(){
            return {
                test: 1,
            };
        },
        created: function(){
            this.$store.commit('set',{title:"首页"})
        },
        components: {
            mains: main
        },
    }

</script>