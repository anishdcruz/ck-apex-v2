import { byMethod } from './api'
import { objectToFormData } from './helpers'

export const form = {
    data () {
        return {
            form: {},
            error: {},
            isProcessing: false,
            show: false
        }
    },
    computed: {
        mode () {
            return this.$route.meta.mode
        },
        isEdit () {
            return this.mode === 'edit'
        }
    },
    methods: {
        errors(key) {
            return this.error[key] ? 'error-bg' : ''
        },
        success() {
            this.$message.success(this.message)
        },
        clearErrors() {
            this.error = {}
        },
        beginProcessing() {
            this.isProcessing = true
        },
        endProcessing() {
            this.isProcessing = false
        },
        handleError(error) {
            if(error.response.status === 422) {
                this.error = error.response.data.errors
            }
            this.endProcessing()
        },
        submit(cb) {
            this.clearErrors()
            this.beginProcessing()
            byMethod(this.method, this.store, this.form)
                .then(function({data}) {
                    if(data.saved) {
                        cb(data)
                    }
                })
                .catch(this.handleError)
        },
        submitMultipartForm(form, cb) {
            this.clearErrors()
            this.beginProcessing()
            byMethod(this.method, this.store, objectToFormData(this.form))
                .then(function({data}) {
                    if(data.saved) {
                        cb(data)
                    }
                })
                .catch(this.handleError)
        }
    }
}
