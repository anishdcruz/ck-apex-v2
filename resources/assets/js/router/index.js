import Vue from 'vue'
import VueRouter from 'vue-router'
import clients from './clients'
import vendors from './vendors'
import products from './products'
import quotations from './quotations'
import advancePayments from './advance_payments'
import SalesOrders from './sales_orders'
import invoices from './invoices'
import clientPayments from './client_payments'
import expenses from './expenses'
import purchaseOrders from './purchase_orders'
import bills from './bills'
import vendorPayments from './vendor_payments'
import users from './users'
import receiveOrders from './receive_orders'
import goodsIssue from './goods_issue'

import PersonalSettings from '../views/settings/personal.vue'
import EmailDocument from '../views/email/document.vue'
import Settings from '../views/settings/form.vue'
import NotFound from '../views/error/not_found.vue'

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    scrollBehavior (to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return { x: 0, y: 0 }
        }
    },
    routes: [
        {path: '/', component: require('../views/dashboard/index.vue')},
        ...clients,
        ...vendors,
        ...products,
        ...quotations,
        ...advancePayments,
        ...SalesOrders,
        ...invoices,
        ...clientPayments,
        ...expenses,
        ...purchaseOrders,
        ...bills,
        ...vendorPayments,
        ...users,
        ...receiveOrders,
        ...goodsIssue,
        // single views
        {path: '/personal-settings', component: PersonalSettings},
        {path: '/email/:id/:type', component: EmailDocument},
        {path: '/settings', component: Settings},
        {path: '*', component: NotFound}
    ]
})

export default router
