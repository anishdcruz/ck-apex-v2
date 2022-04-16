<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{title}} Advance Payment</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Client</label>
                            <typeahead :initial="form.client"
                                :url="clientURL"
                                @input="onClientUpdate"
                            >
                            </typeahead>
                            <error-text :error="error.client_id"></error-text>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="form-group">
                            <label>Currency</label>
                            <typeahead :initial="form.currency"
                                :url="currencyURL"
                                @input="onCurrencyUpdate"
                            >
                            </typeahead>
                            <error-text :error="error.currency_id"></error-text>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="form-group">
                            <label>
                                Number
                                <small>(Auto Generated)</small>
                            </label>
                            <span class="form-control">{{form.number}}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-3">
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" class="form-control" v-model="form.payment_date">
                            <error-text :error="error.payment_date"></error-text>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="form-group">
                            <label>Payment Mode</label>
                            <select class="form-control" v-model="form.payment_mode">
                                <option value="cheque">Cheque</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                            <error-text :error="error.payment_mode"></error-text>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="form-group">
                            <label>
                                Payment Reference
                                <small v-if="form.payment_mode == 'cash'">(Optional)</small>
                            </label>
                            <input type="text" class="form-control" v-model="form.payment_reference">
                            <error-text :error="error.payment_reference"></error-text>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col col-3">
                         <div class="form-group">
                             <label>Amount Received</label>
                             <input type="text" class="form-control" v-model="form.amount_received">
                             <error-text :error="error.amount_received"></error-text>
                         </div>
                    </div>
                    <div class="col col-6">
                        <div class="form-group">
                            <label>
                                Upload Document
                                <small v-if="form.payment_mode == 'cash'">(Optional)</small>
                                <small v-else>(cheque, transfer receipt)</small>
                            </label>
                            <file-upload @ready="onDocument"></file-upload>
                            <error-text :error="error.document"></error-text>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" v-model="form.description"></textarea>
                            <error-text :error="error.description"></error-text>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <spinner v-if="isProcessing"></spinner>
                <div class="btn-group" v-else>
                    <button :disabled="isProcessing" @click="save" class="btn btn-primary">
                        Save
                    </button>
                    <button :disabled="isProcessing" v-if="!isEdit"
                        @click="saveAndNew" class="btn btn-secondary">
                        Save and New
                    </button>
                    <router-link :disabled="isProcessing" :to="`${resource}/${$route.params.id}`"
                        class="btn" v-if="isEdit">
                        Cancel
                    </router-link>
                    <router-link :disabled="isProcessing" :to="`${resource}`"
                        class="btn" v-else>
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
    import FileUpload from '../../components/form/FileUpload.vue'
    import Spinner from '../../components/loading/Spinner.vue'
    import { get, byMethod } from '../../lib/api'
    import { form } from '../../lib/mixins'

    function initializeUrl (to) {
        let urls = {
            'create': `/api/advance_payments/create`
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner, FileUpload },
        mixins: [ form ],
        data () {
            return {
                resource: '/advance_payments',
                store: '/api/advance_payments',
                method: 'POST',
                title: 'Receive',
                message: 'You have successfully received advance payment!',
                currencyURL: '/api/search/currencies',
                clientURL: '/api/search/clients'
            }
        },
        beforeRouteEnter(to, from, next) {

            get(initializeUrl(to), to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false

            get(initializeUrl(to), to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            save() {
                this.submitMultipartForm(this.form, (data) => {
                    this.success()
                    this.$router.push(`${this.resource}/${data.id}`)
                })
            },
            saveAndNew() {
                this.submitMultipartForm(this.form, (data) => {
                    const id = Math.random().toString(36).substring(7)
                    this.endProcessing()
                    this.success()
                    this.$router.push(`${this.resource}/create?new=${id}`)
                })
            },
            onDocument(e) {
                Vue.set(this.$data.form, 'document', e.target.value)
            },
            onClientUpdate(e) {
                const client = e.target.value

                // client
                Vue.set(this.form, 'client', client)
                Vue.set(this.form, 'client_id', client.id)

                // currency
                Vue.set(this.form, 'currency', client.currency)
                Vue.set(this.form, 'currency_id', client.currency.id)
            },
            onCurrencyUpdate(e) {
                const currency = e.target.value

                Vue.set(this.form, 'currency_id', currency.id)
                Vue.set(this.form, 'currency', currency)
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Advance Payment ${this.title}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
