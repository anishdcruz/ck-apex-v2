export default [
    {path: '/users', component: require('../views/users/index.vue')},
    {path: '/users/create', component: require('../views/users/form.vue')},
    {path: '/users/:id/edit', component: require('../views/users/form.vue'), meta: {mode: 'edit'}},
    {path: '/users/:id', component: require('../views/users/show.vue')},
]
