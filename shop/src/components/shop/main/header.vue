<template>
    <div class="shop-header">
        <mt-header fixed :title="$store.state.title" style="height: 7vh;background-color:#65c6f6;z-index: 666">
            <router-link to="" slot="left">
                <mt-button  @click="$router.history.go(-1)" icon="back">返回</mt-button>
            </router-link>
            <mt-button icon="more" slot="right"></mt-button>
        </mt-header>
        <div style="height: 7vh;"></div>
    </div>
</template>

<script>
    export default{
        name: 'shop_header',
        props: ['title'],
        data(){
            return {

            }
        },

    }
</script>

<style lang="css">
    @component-namespace page {
        @component header {
            @descendent main {
                margin-top: 50px;
                min-height: 120vh;

            > * {
                  margin-bottom: 15px;
              }
            }
        }
    }
</style>

