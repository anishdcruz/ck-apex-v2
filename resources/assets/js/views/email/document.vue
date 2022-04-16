<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Send {{title}}</span>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger" v-if="warning.email.length">
                    <span>{{warning.email[0]}}</span>
                </div>
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Email To</label>
                            <input type="text" class="form-control" v-model="form.to"/>
                            <error-text :error="error.to"></error-text>
                        </div>
                        <div class="form-group">
                            <label>BCC</label>
                            <input type="text" class="form-control " v-model="form.bcc"/>
                            <error-text :error="error.bcc"></error-text>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" v-model="form.subject"/>
                            <error-text :error="error.subject"></error-text>
                        </div>
                        <div class="alert alert-info">
                            A PDF document of this {{title}} will be attached automatically
                        </div>
                    </div>
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control form-email" v-model="form.message"></textarea>
                            <error-text :error="error.message"></error-text>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" v-if="!warning.email.length">
                <spinner v-if="isProcessing"></spinner>
                <div class="btn-group" v-else>
                    <button :disabled="isProcessing" @click="save" class="btn btn-primary">
                        Save
                    </button>
                    <router-link :disabled="isProcessing" :to="`${resource}`"
                        class="btn">
                        Cancel
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import ErrorText from '../../components/form/ErrorText.vue'
    import Typeahead from '../../components/form/Typeahead.vue'
    import Spinner from '../../components/loading/Spinner.vue'
    import { get, byMethod } from '../../lib/api'
    import { form } from '../../lib/mixins'

    function initializeUrl (to) {
        let urls = {
            'quotation': `/api/email/quotations/${to.params.id}`,
            'advance_payment': `/api/email/advance_payments/${to.params.id}`,
            'sales_order': `/api/email/sales_orders/${to.params.id}`,
            'invoice': `/api/email/invoices/${to.params.id}`,
            'client_payment': `/api/email/client_payments/${to.params.id}`,
            'purchase_order': `/api/email/purchase_orders/${to.params.id}`,
        }

        return (urls[to.params.type] || urls['quotation'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner },
        mixins: [ form ],
        data () {
            return {
                resource: `/quotations/${this.$route.params.id}`,
                store: `/api/email/quotations/${this.$route.params.id}`,
                method: 'POST',
                title: 'Quotation',
                message: 'You have successfully sent Quotation!',
                warning: {
                    email: []
                }
            }
        },
        computed: {
            type() {
                return this.$route.params.type
            }
        },
        created() {
            if(this.type === 'advance_payment') {
                this.resource = `/advance_payments/${this.$route.params.id}`,
                this.store =`/api/email/advance_payments/${this.$route.params.id}`
                this.message = 'You have successfully sent Advance Payment Receipt!'
                this.title = 'Advance Payment Receipt'
            } else if(this.type === 'sales_order') {
                this.resource = `/sales_orders/${this.$route.params.id}`,
                this.store =`/api/email/sales_orders/${this.$route.params.id}`
                this.message = 'You have successfully sent Sales Order!'
                this.title = 'Sales Order'
            } else if(this.type === 'invoice') {
                this.resource = `/invoices/${this.$route.params.id}`,
                this.store =`/api/email/invoices/${this.$route.params.id}`
                this.message = 'You have successfully sent Invoice!'
                this.title = 'Invoice'
            } else if(this.type === 'client_payment') {
                this.resource = `/client_payments/${this.$route.params.id}`,
                this.store =`/api/email/client_payments/${this.$route.params.id}`
                this.message = 'You have successfully sent Payment Receipt!'
                this.title = 'Client Payment Receipt'
            } else if(this.type === 'purchase_order') {
                this.resource = `/purchase_orders/${this.$route.params.id}`,
                this.store =`/api/email/purchase_orders/${this.$route.params.id}`
                this.message = 'You have successfully sent Purchase Order!'
                this.title = 'Purchase Order'
            }
        },
        beforeRouteEnter(to, from, next) {

            get(initializeUrl(to))
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false

            get(initializeUrl(to))
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            save() {
                this.submit((data) => {
                    this.success()
                    this.$router.push(`${this.resource}`)
                })
            },
            saveAndNew() {
                this.submit((data) => {
                    const id = Math.random().toString(36).substring(7)
                    this.endProcessing()
                    this.success()
                    this.$router.push(`${this.resource}/create?new=${id}`)
                })
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                if(res.data.warning) {
                    Vue.set(this.$data, 'warning', res.data.warning)
                }

                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
