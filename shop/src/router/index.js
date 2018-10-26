import Vue from 'vue'
import Router from 'vue-router'
import index from '@/components/index'
import shop_my from '@/components/shop/my'
import shop_main from '@/components/shop/main'
import shop_tabbar from '@/components/shop/main/tabbar'
import shop_header from '@/components/shop/main/header'

import shop_admission from '@/components/shop/admission'
import shop_admission_form from '@/components/shop/admission/form'

import shop_admin from '@/components/shop/admin'
import qrcode from '@/components/shop/admin/qrcode'


Vue.use(Router)

export default new Router({
    routes: [
        {
            path: '/',
            name: 'index',
            component: index
        },
        {
            path: '/shop/my',
            name: 'shop_my',
            component: shop_my
        },
        {
            path: '/shop/main/tabbar',
            name: 'shop_tabbar',
            component: shop_tabbar
        },
        {
            path: '/shop/main/header',
            name: 'shop_header',
            component: shop_header
        },
        {
            path: '/shop/main',
            name: 'shop_main',
            component: shop_main
        },
        {
            path: '/shop/admission',
            name: 'shop_admission',
            component: shop_admission
        },
        {
            path: '/shop/admission/form',
            name: 'shop_admission_form',
            component: shop_admission_form,
            meta: {
                title: "申请入驻"
            }
        },
        {
            path: '/shop/admin/',
            name: 'shop_admin',
            component: shop_admin,
            meta: {
                title: "商家后台管理"
            },
            children:[

            ]
        },
        {
            path: '/shop/admin/qrcode',
            name: 'shop_admin_qrcode',
            component: qrcode,
            meta: {
                title: "吸粉二维码"
            }
        }
    ]
})
