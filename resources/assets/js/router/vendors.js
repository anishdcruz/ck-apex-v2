export default [
    {path: '/vendors', component: require('../views/vendors/index.vue')},
    {path: '/vendors/create', component: require('../views/vendors/form.vue')},
    {path: '/vendors/:id/edit', component: require('../views/vendors/form.vue'), meta: {mode: 'edit'}},
    {path: '/vendors/:id', component: require('../views/vendors/show.vue')},
]
