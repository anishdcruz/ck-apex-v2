export default [
    {path: '/advance_payments', component: require('../views/advance_payments/index.vue')},
    {path: '/advance_payments/create', component: require('../views/advance_payments/form.vue')},
    // {path: '/advance_payments/:id/edit', component: require('../views/advance_payments/form.vue'), meta: {mode: 'edit'}},
    {path: '/advance_payments/:id', component: require('../views/advance_payments/show.vue')},
]
