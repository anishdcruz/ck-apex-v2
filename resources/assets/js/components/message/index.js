import Vue from 'vue'
import Message from './Message.vue'

const message = Vue.prototype.$message = new Vue(Message).$mount()

document.body.appendChild(message.$el)

export default message




