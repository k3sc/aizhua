<template>
    <div class="shop-admin">
        <div class="admin-profit">
            <div class="profit-top">
                <div class="today-profit">
                    <div class="title">今日收益</div>
                    <yd-countup endnum="845" duration="3" decimals="2" separator="," suffix="元"
                                class="profit"></yd-countup>
                </div>
            </div>

            <div class="profit-bottom">
                <div class="unsettled-profit">
                    <div class="title">未结算收益</div>
                    <yd-countup endnum="666666" duration="3" decimals="2" separator="," suffix="元"
                                class="profit"></yd-countup>
                </div>
                <div class="total-profit">
                    <div class="title">累计收益</div>
                    <yd-countup endnum="88888888" duration="3" decimals="2" separator="," suffix="元"
                                class="profit"></yd-countup>
                </div>
            </div>
        </div>

        <div class="admin-mian">
            <yd-grids-group :rows="2" title="功能">
                <yd-grids-item>
                    <p slot="text" class="title">每日收益</p>
                    <span slot="text" class="desc">查看每天业绩</span>
                </yd-grids-item>
                <yd-grids-item>
                    <p slot="text"  class="title">客户管理</p>
                    <span slot="text" class="desc">查看客户资料</span>
                </yd-grids-item>
            </yd-grids-group>
            <yd-grids-group :rows="2">
                <yd-grids-item>
                    <p slot="text"  class="title" >钱包</p>
                    <span slot="text"  class="desc" >申请提现</span>
                </yd-grids-item>
                <yd-grids-item>
                    <p slot="text"  class="title" >设置</p>
                    <span slot="text"  class="desc" >修改密码</span>
                </yd-grids-item>
            </yd-grids-group>
            <yd-grids-group :rows="2" title="推广线上抓娃娃">
                <yd-grids-item>
                    <p slot="text"  class="title" >海报宣传物料</p>
                    <span slot="text"  class="desc" >转发朋友圈立即赚钱</span>
                </yd-grids-item>
                <div  @click="go('/shop/admin/qrcode')">
                    <yd-grids-item>
                        <p slot="text"  class="title" >加粉二维码</p>
                        <span slot="text"  class="desc" >让用户主动找你</span>
                    </yd-grids-item>
                </div>
            </yd-grids-group>

        </div>

    </div>
</template>

<script>
    export default{
        name: "shop_admin",
        data(){
            return {}
        },
        methods: {
            go: function(url){
                this.$router.push(url)
            }
        },
    }
</script>

<style lang="less">
    .admin-profit {
        position: relative;
        height: 30vh;
        background-color: #fff;
    }
    .profit-top {
        width: 100%;
        text-align: center;
        position: absolute;
        margin-top: 4vh;
        font-size: 0.5rem;
        .profit {
            font-size: 0.8rem;
            margin-top: 0.1vh;
        }
    }

    .profit-bottom {
        position: absolute;
        width: 100%;
        bottom: 6vh;
        font-size: 0.4rem;
        .unsettled-profit, .total-profit {
            width: 50%;
            float: left;
            text-align: center;
            .profit {
                font-size: 0.5rem;
                /*font-weight: bold;*/
            }
        }

    }
    .admin-mian{
        .title{
            height: 34px;
            font-size: 0.42rem;
        }
        .desc{
            font-size: 0.35rem;
            color: #9c9c9c;
        }
    }


</style>