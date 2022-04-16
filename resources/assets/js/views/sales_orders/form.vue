<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{title}} Sales Order</span>
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
                            <label>Date</label>
                            <input type="date" class="form-control" v-model="form.date">
                            <error-text :error="error.date"></error-text>
                        </div>
                    </div>
                    <div class="col col-3">
                        <div class="form-group">
                            <label>
                                Reference
                                <small>(Optional)</small>
                            </label>
                            <input type="text" class="form-control" v-model="form.reference">
                            <error-text :error="error.reference"></error-text>
                        </div>
                    </div>
                </div>
                 <hr>
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>
                                Upload Document
                                <small>(Optional: client purchase order)</small>
                            </label>
                            <file-upload @ready="onDocument"></file-upload>
                            <error-text :error="error.document"></error-text>
                        </div>
                    </div>
                </div>
                <hr>
                <table class="item-table">
                    <thead>
                        <tr>
                            <th>Item Description</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(item, index) in form.items">
                            <tr>
                                <td :class="['width-7', errors(`items.${index}.product_id`)]">
                                    <typeahead :initial="item.product" :trim="80"
                                        @input="onProductUpdated(item, index, $event)"
                                        :url="productURL"
                                    >
                                    </typeahead>
                                    <error-text :error="error[`items.${index}.product_id`]"></error-text>
                                </td>
                                <td :class="['width-2', errors(`items.${index}.unit_price`)]">
                                    <input type="text" class="form-control" v-model="item.unit_price">
                                    <error-text :error="error[`items.${index}.unit_price`]"></error-text>
                                </td>
                                <td :class="['width-1', errors(`items.${index}.qty`)]">
                                    <input type="text" class="form-control" v-model="item.qty">
                                    <error-text :error="error[`items.${index}.qty`]"></error-text>
                                </td>
                                <td class="width-2">
                                    <span class="form-control align-right">
                                        {{Number(item.qty * Number(item.unit_price)) | formatMoney(form.currency, false)}}
                                    </span>
                                </td>
                                <td>
                                    <button class="item-remove" @click="removeProduct(item, index)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="item-tax" v-if="item.taxes.length" v-for="tax in item.taxes">
                                <td colspan="2">{{tax.name}}</td>
                                <td>{{tax.rate}}%</td>
                                <td>+{{(Number(item.qty * Number(item.unit_price)) * tax.rate / 100) | formatMoney(form.currency, false)}}</td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="item-empty">
                                <button class="btn btn-sm" @click="addNewLine">
                                    Add new line
                                </button>
                            </td>
                            <td class="item-footer" colspan="2">Sub Total</td>
                            <td class="item-footer" colspan="2">
                                <span class="item-dark form-control">
                                    {{subTotal | formatMoney(form.currency, false)}}
                                </span>
                            </td>
                        </tr>
                        <tr v-for="(value, key) in selectedTaxes" class="item-selected-tax">
                            <td class="item-empty"></td>
                            <td class="item-footer" colspan="2">
                                <span>{{key}}</span>
                            </td>
                            <td class="item-footer" colspan="2">
                                <span class="">
                                    {{value | formatMoney(form.currency, false)}}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="item-empty"></td>
                            <td class="item-footer" colspan="2">
                                <strong>Total</strong>
                            </td>
                            <td class="item-footer" colspan="2">
                                <strong class="item-dark form-control">
                                    {{total | formatMoney(form.currency)}}
                                </strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <hr>
                <div class="row">
                    <div class="col col-6">
                        <div class="form-group">
                            <label>Terms and Conditions</label>
                            <textarea class="form-control" v-model="form.terms"></textarea>
                            <error-text :error="error.terms"></error-text>
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
            'create': `/api/sales_orders/create`,
            'edit': `/api/sales_orders/${to.params.id}/edit`,
            'clone': `/api/sales_orders/${to.params.id}/edit?mode=clone`,
            'sales_order': `/api/quotations/${to.params.id}/edit?mode=sales_order`,
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner, FileUpload },
        mixins: [ form ],
        data () {
            return {
                resource: '/sales_orders',
                store: '/api/sales_orders',
                method: 'POST',
                title: 'Create',
                message: 'You have successfully created sales order!',
                currencyURL: '/api/search/currencies',
                productURL: '/api/search/products',
                clientURL: '/api/search/clients'
            }
        },
        computed: {
            subTotal() {
                return this.form.items.reduce((carry, item) => {
                    return carry + (Number(item.unit_price) * Number(item.qty))
                }, 0)
            },
            totalTax() {
                return this.form.items.reduce((carry, item) => {

                    if(item.taxes && item.taxes.length) {
                        const taxes = item.taxes.reduce((c, tax) => {
                            return c + (Number(item.unit_price) * Number(item.qty)) * tax.rate / 100
                        }, 0)
                        return carry + taxes
                    }
                    else {
                        return 0
                    }
                }, 0)
            },
            selectedTaxes() {
                const taxes = {}

                this.form.items.forEach((item) => {
                    if(item.taxes && item.taxes.length) {
                        item.taxes.forEach((tax) => {
                            let key = `${tax.name} ${tax.rate}%`
                            if(taxes.hasOwnProperty(key)) {
                                taxes[key] =  taxes[key] + (Number(item.unit_price) * Number(item.qty)) * tax.rate / 100
                            } else {
                                taxes[key] = (Number(item.unit_price) * Number(item.qty)) * tax.rate / 100
                            }
                        })
                    }
                })
                return taxes
            },
            total() {
                return this.subTotal + this.totalTax
            }
        },
        created() {
            if(this.mode === 'edit') {
                this.store = `/api/sales_orders/${this.$route.params.id}?_method=PUT`
                this.message = 'You have successfully updated sales order!'
                this.method = 'POST'
                this.title = 'Edit'
            } else if(this.mode === 'clone') {
                this.store = `/api/sales_orders`
                this.message = 'You have successfully cloned sales order!'
                this.method = 'POST'
                this.title = 'Clone'
            } else if(this.mode === 'sales_order') {
                this.store = `/api/sales_orders`
                this.message = 'You have successfully converted quotation to sales order!'
                this.method = 'POST'
                this.title = 'Convert Quotation to '
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
            onDocument(e) {
                Vue.set(this.$data.form, 'document', e.target.value)
            },
            removeProduct(item, index) {
                if(this.form.items.length > 1) {
                    this.form.items.splice(index, 1)
                }
            },
            addNewLine() {
                this.form.items.push({
                    'product_id': null,
                    'product': null,
                    'unit_price': 0,
                    'qty': 1
                })
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
            onProductUpdated(item, index, e) {
                const product = e.target.value

                // product
                Vue.set(this.form.items[index], 'product', product)
                Vue.set(this.form.items[index], 'product_id', product.id)

                // unit price
                Vue.set(this.form.items[index], 'unit_price', product.unit_price)

                // taxes
                Vue.set(this.form.items[index], 'taxes', product.taxes)
            },
            onProductCurrencyUpdated(v, index, e) {
                const currency = e.target.value

                // currency
                Vue.set(this.form.items[index], 'currency', currency)
                Vue.set(this.form.items[index], 'currency_id', currency.id)
            },
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
            onCurrencyUpdate(e) {
                const currency = e.target.value

                Vue.set(this.form, 'currency_id', currency.id)
                Vue.set(this.form, 'currency', currency)
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Sales Order ${this.title}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
