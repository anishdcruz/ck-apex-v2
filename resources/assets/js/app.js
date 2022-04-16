import Vue from 'vue'
import router from './router'
import App from './App.vue'

import bar from './components/loading'
import message from './components/message'
import title from './lib/title'

import { interceptors } from './lib/api'

import './lib/filters'

router.beforeEach((to, from, next) => {
    bar.start()
    next()
})

interceptors((err) => {
    if(err.response.status === 401) {
        bar.fail()
        window.location = '/?logout=true'
    }

    if(err.response.status === 403) {
        bar.fail()
        bar.finish()
        message.error('Unauthorized access!')
    }

    if(err.response.status === 500) {
        bar.fail()
        bar.finish()
        message.error(err.response.data.message)
    }

    if(err.response.status === 422) {
        bar.fail()
        bar.finish()
        message.error(err.response.data.message)
    }

    if(err.response.status === 404) {
        bar.fail()
        bar.finish()
        message.error(err.response.data.message)
    }
})

const app = new Vue({
    el: '#root',
    render: h => h(App),
    router
})
