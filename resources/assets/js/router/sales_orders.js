export default [
    {path: '/sales_orders', component: require('../views/sales_orders/index.vue')},
    {path: '/sales_orders/create', component: require('../views/sales_orders/form.vue')},
    {path: '/sales_orders/:id/edit', component: require('../views/sales_orders/form.vue'), meta: {mode: 'edit'}},
    {path: '/sales_orders/:id/clone', component: require('../views/sales_orders/form.vue'), meta: {mode: 'clone'}},
    {path: '/sales_orders/:id/invoice', component: require('../views/invoices/form.vue'), meta: {mode: 'sales_order'}},
    {path: '/sales_orders/:id/goods_issue', component: require('../views/goods_issue/form.vue'), meta: {mode: 'sales_order'}},
    {path: '/sales_orders/:id', component: require('../views/sales_orders/show.vue')},
]
