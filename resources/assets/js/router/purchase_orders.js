export default [
    {path: '/purchase_orders', component: require('../views/purchase_orders/index.vue')},
    {path: '/purchase_orders/create', component: require('../views/purchase_orders/form.vue')},
    {path: '/purchase_orders/:id/edit', component: require('../views/purchase_orders/form.vue'), meta: {mode: 'edit'}},
    {path: '/purchase_orders/:id/clone', component: require('../views/purchase_orders/form.vue'), meta: {mode: 'clone'}},
    {path: '/purchase_orders/:id/bill', component: require('../views/bills/form.vue'), meta: {mode: 'purchase_order'}},
    {path: '/purchase_orders/:id/receive_order', component: require('../views/receive_orders/form.vue'), meta: {mode: 'purchase_order'}},
    {path: '/purchase_orders/:id', component: require('../views/purchase_orders/show.vue')},
]
