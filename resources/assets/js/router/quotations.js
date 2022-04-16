export default [
    {path: '/quotations', component: require('../views/quotations/index.vue')},
    {path: '/quotations/create', component: require('../views/quotations/form.vue')},
    {path: '/quotations/:id/edit', component: require('../views/quotations/form.vue'), meta: {mode: 'edit'}},
    {path: '/quotations/:id/clone', component: require('../views/quotations/form.vue'), meta: {mode: 'clone'}},
    {path: '/quotations/:id/sales_order', component: require('../views/sales_orders/form.vue'), meta: {mode: 'sales_order'}},
    {path: '/quotations/:id/invoice', component: require('../views/invoices/form.vue'), meta: {mode: 'quotation'}},
    {path: '/quotations/:id', component: require('../views/quotations/show.vue')},
]
