export default [
    {path: '/vendor_payments', component: require('../views/vendor_payments/index.vue')},
    {path: '/vendor_payments/create', component: require('../views/vendor_payments/form.vue')},
    // {path: '/vendor_payments/:id/edit', component: require('../views/vendor_payments/form.vue'), meta: {mode: 'edit'}},
    {path: '/vendor_payments/:id', component: require('../views/vendor_payments/show.vue')},
]
