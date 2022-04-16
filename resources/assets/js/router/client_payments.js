export default [
    {path: '/client_payments', component: require('../views/client_payments/index.vue')},
    {path: '/client_payments/create', component: require('../views/client_payments/form.vue')},
    // {path: '/client_payments/:id/edit', component: require('../views/client_payments/form.vue'), meta: {mode: 'edit'}},
    {path: '/client_payments/:id', component: require('../views/client_payments/show.vue')},
]
