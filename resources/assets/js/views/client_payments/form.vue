<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{title}} Client Payment</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Client</label>
                            <typeahead :initial="form.client" :params="{with: 'invoices'}"
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
                             <label>Amount Paid</label>
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
                <hr>
                <div class="alert alert-info" v-if="!form.client_id">
                    <span>Please select the client in order to get all sent and partially paid invoices.</span>
                </div>
                <div class="alert alert-danger" v-else-if="form.client_id && !form.items.length">
                    <span>There are no sent and partially paid invoices for this client.</span>
                </div>
                <table class="item-table" v-if="form.client_id && form.items.length">
                    <thead>
                        <tr>
                            <th>Invoice Date</th>
                            <th>Invoice Number</th>
                            <th>Invoice Total</th>
                            <th>Balance Due</th>
                            <th>Amount Applied</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in form.items">
                            <td class="width-2">
                                <span class="form-control">{{item.date}}</span>
                            </td>
                            <td class="width-3">
                                <span class="form-control">{{item.number}}</span>
                            </td>
                            <td class="width-2">
                                <span class="form-control">{{item.total}}</span>
                            </td>
                            <td class="width-2">
                                <span class="form-control">{{item.total - item.amount_paid}}</span>
                            </td>
                            <td :class="['width-3', errors(`items.${index}.amount_applied`)]">
                                <input type="text" class="form-control" v-model="item.amount_applied">
                                <error-text :error="error[`items.${index}.amount_applied`]"></error-text>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="item-footer" colspan="4">Amount Paid</td>
                            <td class="item-footer">
                                <strong class="item-dark form-control">
                                    {{form.amount_received | formatMoney(form.currency)}}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="item-footer" colspan="4">Amount Applied to Invoices</td>
                            <td class="item-footer">
                                <strong class="item-dark form-control">
                                    {{total | formatMoney(form.currency)}}
                                </strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="panel-footer" v-if="form.client_id && form.items.length">
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
            'create': `/api/client_payments/create`
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner, FileUpload },
        mixins: [ form ],
        data () {
            return {
                resource: '/client_payments',
                store: '/api/client_payments',
                method: 'POST',
                title: 'Create',
                message: 'You have successfully received client payment!',
                currencyURL: '/api/search/currencies',
                clientURL: '/api/search/clients'
            }
        },
        computed: {
            total() {
                return this.form.items.reduce((carry, item) => {
                    return carry + (Number(item.amount_applied))
                }, 0)
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
                    // this.endProcessing()
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

                // invoices
                Vue.set(this.form, 'items', client.invoices)
            },
            onCurrencyUpdate(e) {
                const currency = e.target.value

                Vue.set(this.form, 'currency_id', currency.id)
                Vue.set(this.form, 'currency', currency)
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Client Payment ${this.title}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
