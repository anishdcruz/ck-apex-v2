export default [
    {path: '/products', component: require('../views/products/index.vue')},
    {path: '/products/create', component: require('../views/products/form.vue')},
    {path: '/products/:id/edit', component: require('../views/products/form.vue'), meta: {mode: 'edit'}},
    {path: '/products/:id', component: require('../views/products/show.vue')},
]
