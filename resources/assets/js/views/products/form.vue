<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{title}} Product</span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-4">
                        <div class="form-group">
                            <label>
                                Item Code
                                <small>(Auto Generated)</small>
                            </label>
                            <span class="form-control">{{form.item_code}}</span>
                        </div>
                        <div class="form-group">
                            <label>
                                Unit Price
                            </label>
                            <input type="text" class="form-control" v-model="form.unit_price">
                            <error-text :error="error.unit_price"></error-text>
                        </div>
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
                    <div class="col col-4">
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" v-model="form.description">
                            </textarea>
                            <error-text :error="error.description"></error-text>
                        </div>
                    </div>
                    <div class="col col-4">
                        <div class="form-group">
                            <strong>Inventory</strong>
                        </div>
                        <div class="form-check">
                            <label>
                                <input type="checkbox" v-model="form.has_inventory">
                                Track Inventory for this Item
                            </label>
                            <error-text :error="error.has_inventory"></error-text>
                        </div>
                    </div>
                </div>
                <hr>
                <table class="item-table" v-if="form.taxes.length">
                    <thead>
                        <tr>
                            <th>Tax Name</th>
                            <th>Rate</th>
                            <th>Tax Authority</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(v, index) in form.taxes">
                            <td :class="['width-4', errors(`items.${index}.name`)]">
                                <input type="text" class="form-control" v-model="v.name">
                                <error-text :error="error[`items.${index}.name`]"></error-text>
                            </td>
                            <td :class="['width-2', errors(`items.${index}.rate`)]">
                                <input type="text" class="form-control" v-model="v.rate">
                                <error-text :error="error[`items.${index}.rate`]"></error-text>
                            </td>
                            <td :class="['width-6', errors(`items.${index}.tax_authority`)]">
                                <input type="text" class="form-control" v-model="v.tax_authority">
                                <error-text :error="error[`items.${index}.tax_authority`]"></error-text>
                            </td>
                            <td>
                                <button class="item-remove" @click="removeTax(v, index)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="item-empty">
                                <button class="btn btn-sm" @click="addNewTax">
                                    Add new Tax
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div v-else>
                    <button class="btn btn-success btn-sm" @click="addNewTax">
                        Add new Tax
                    </button>
                </div>
                <hr>
                <table class="item-table" v-if="form.items.length">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Reference</th>
                            <th>Unit Price</th>
                            <th>Currency</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(v, index) in form.items">
                            <td :class="['width-5', errors(`items.${index}.vendor_id`)]">
                                <typeahead :initial="v.vendor" :trim="80"
                                    @input="onVendorUpdated(v, index, $event)"
                                    :url="vendorURL"
                                >
                                </typeahead>
                                <error-text :error="error[`items.${index}.vendor_id`]"></error-text>
                            </td>
                            <td :class="['width-2', errors(`items.${index}.reference`)]">
                                <input type="text" class="form-control" v-model="v.reference">
                                <error-text :error="error[`items.${index}.reference`]"></error-text>
                            </td>
                            <td :class="['width-2', errors(`items.${index}.price`)]">
                                <input type="text" class="form-control" v-model="v.price">
                                <error-text :error="error[`items.${index}.price`]"></error-text>
                            </td>
                            <td :class="['width-3', errors(`items.${index}.currency_id`)]">
                                <typeahead :initial="v.currency" :trim="80"
                                    @input="onVendorCurrencyUpdated(v, index, $event)"
                                    :url="currencyURL"
                                >
                                </typeahead>
                                 <error-text :error="error[`items.${index}.currency_id`]"></error-text>
                            </td>
                            <td>
                                <button class="item-remove" @click="removeVendor(v, index)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="item-empty">
                                <button class="btn btn-sm" @click="addNewVendor">
                                    Add new Vendor
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div v-else>
                    <button class="btn btn-success btn-sm" @click="addNewVendor">
                        Add new Vendor
                    </button>
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
    import Spinner from '../../components/loading/Spinner.vue'
    import { get, byMethod } from '../../lib/api'
    import { form } from '../../lib/mixins'

    function initializeUrl (to) {
        let urls = {
            'create': `/api/products/create`,
            'edit': `/api/products/${to.params.id}/edit`,
            'clone': `/api/products/${to.params.id}/edit?mode=clone`,
        }

        return (urls[to.meta.mode] || urls['create'])
    }

    export default {
        components: { ErrorText, Typeahead, Spinner },
        mixins: [ form ],
        data () {
            return {
                form: {
                    items: [],
                    taxes: []
                },
                resource: '/products',
                store: '/api/products',
                method: 'POST',
                title: 'Create',
                message: 'You have successfully created product!',
                currencyURL: '/api/search/currencies',
                vendorURL: '/api/search/vendors'
            }
        },
        created() {
            if(this.mode === 'edit') {
                this.store = `/api/products/${this.$route.params.id}`
                this.message = 'You have successfully updated product!'
                this.method = 'PUT'
                this.title = 'Edit'
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
            removeVendor(v, index) {
                this.form.items.splice(index, 1)
            },
            removeTax(v, index) {
                this.form.taxes.splice(index, 1)
            },
            onVendorUpdated(v, index, e) {
                const vendor = e.target.value

                // vendor
                Vue.set(this.form.items[index], 'vendor', vendor)
                Vue.set(this.form.items[index], 'vendor_id', vendor.id)

                // currency
                Vue.set(this.form.items[index], 'currency', vendor.currency)
                Vue.set(this.form.items[index], 'currency_id', vendor.currency.id)
            },
            onVendorCurrencyUpdated(v, index, e) {
                const currency = e.target.value

                // currency
                Vue.set(this.form.items[index], 'currency', currency)
                Vue.set(this.form.items[index], 'currency_id', currency.id)
            },
            addNewVendor() {
                this.form.items.push({
                    'vendor_id': null,
                    'vendor': null,
                    'reference': null,
                    'price': 0,
                    'currency_id': null,
                    'currency': null
                })
            },
            addNewTax() {
                this.form.taxes.push({
                    'name': '',
                    'rate': 0,
                    'tax_authority': ''
                })
            },
            save() {
                this.submit((data) => {
                    this.success()
                    this.$router.push(`${this.resource}/${data.id}`)
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
            onCurrencyUpdate(e) {
                const currency = e.target.value

                Vue.set(this.form, 'currency_id', currency.id)
                Vue.set(this.form, 'currency', currency)
            },
            setData(res) {
                Vue.set(this.$data, 'form', res.data.form)
                this.$title.set(`Product ${this.title}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
