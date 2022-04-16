import Vue from 'vue'

const title = Vue.prototype.$title = {
    set(text) {
        const appTitle = window.apex.app_name

        if (appTitle) {
            document.title = `${text} | ${appTitle}`
        }
    }
}


export default title
