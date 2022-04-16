export default [
    {path: '/expenses', component: require('../views/expenses/index.vue')},
    {path: '/expenses/create', component: require('../views/expenses/form.vue')},
    // {path: '/expenses/:id/edit', component: require('../views/expenses/form.vue'), meta: {mode: 'edit'}},
    {path: '/expenses/:id', component: require('../views/expenses/show.vue')},
]
