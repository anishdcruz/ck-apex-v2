export default [
    {path: '/bills', component: require('../views/bills/index.vue')},
    {path: '/bills/create', component: require('../views/bills/form.vue')},
    {path: '/bills/:id/edit', component: require('../views/bills/form.vue'), meta: {mode: 'edit'}},
    {path: '/bills/:id/clone', component: require('../views/bills/form.vue'), meta: {mode: 'clone'}},
    {path: '/bills/:id', component: require('../views/bills/show.vue')},
]
