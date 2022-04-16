<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Bill {{model.number}}</span>
                <div>
                    <router-link :to="`/bills`" class="btn" title="Go Back">
                        <i class="fa fa-arrow-left"></i>
                    </router-link>
                    <router-link :to="`/bills/${model.id}/clone`" class="btn"
                        title="Clone">
                        <i class="fa fa-files-o"></i>
                    </router-link>
                    <div class="btn-group">
                        <a :href="`/docs/bills/${model.id}`" target="_blank" class="btn" title="View PDF">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                        <a :href="`/docs/bills/${model.id}?mode=download`" target="_blank" class="btn" title="Download PDF">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                    <router-link v-if="model.is_editable" :to="`/bills/${model.id}/edit`"
                        class="btn" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </router-link>
                     <dropdown title="More" ref="more">
                        <li>
                            <a @click.stop="deleteModel">Delete</a>
                        </li>
                    </dropdown>
                </div>
            </div>
            <div class="panel-body">
                <div class="document">
                    <div class="document-heading">
                        <div class="row">
                            <div class="col col-4">
                                <p><strong>Bill From:</strong></p>
                                <router-link :to="`/vendors/${model.vendor_id}`">
                                    <span>{{model.vendor.person}}</span><br>
                                    <span>{{model.vendor.company}}</span><br>
                                    <pre>{{model.vendor.billing_address}}</pre>
                                </router-link>
                            </div>
                            <div class="col col-2">
                                &nbsp;
                            </div>
                            <div class="col col-6">
                                <table class="document-summary">
                                    <tbody>
                                        <tr>
                                            <td>Status</td>
                                            <td><status :id="model.status_id" /></td>
                                        </tr>
                                        <tr>
                                            <td>Number:</td>
                                            <td>{{model.number}}</td>
                                        </tr>
                                        <tr>
                                            <td>Date:</td>
                                            <td>{{model.date}}</td>
                                        </tr>
                                        <tr v-if="model.reference">
                                            <td>Reference:</td>
                                            <td>{{model.reference}}</td>
                                        </tr>
                                        <tr v-if="model.purchase_order">
                                            <td>Purchase Order Number:</td>
                                            <td>
                                                <router-link :to="`/purchase_orders/${model.purchase_order_id}`">
                                                    {{model.purchase_order.number}}
                                                </router-link>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Currency:</td>
                                            <td>{{model.currency.text}}</td>
                                        </tr>
                                        <tr>
                                            <td>Total:</td>
                                            <td>{{model.total | formatMoney(model.currency, false)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="document-body">
                        <table class="document-table">
                            <thead>
                                <tr>
                                    <th>Vendor Ref</th>
                                    <th>Item Description</th>
                                    <th class="align-center">Unit Price</th>
                                    <th class="align-center">Qty</th>
                                    <th class="align-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in model.items">
                                    <td class="width-2">
                                        {{item.vendor_reference}}
                                    </td>
                                    <td class="width-4">
                                        <router-link :to="`/products/${item.product_id}`">
                                            <pre>{{item.product.description}}</pre>
                                            <small>({{item.product.item_code}})</small>
                                        </router-link>
                                    </td>
                                    <td class="width-2 align-right">
                                        {{item.unit_price | formatMoney(model.currency, false)}}
                                    </td>
                                    <td class="width-1 align-center">{{item.qty}}</td>
                                    <td class="width-2 align-right">
                                        {{(item.qty * item.unit_price) | formatMoney(model.currency, false)}}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="2">
                                        <strong>Total</strong>
                                    </td>
                                    <td class="align-right">
                                        <strong>
                                            {{model.total | formatMoney(model.currency)}}
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="document-footer row">
                        <div class="col col-9">
                            <strong>Terms and Conditions</strong>
                            <div class="document-terms">
                                <pre>{{model.terms}}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel" v-if="model.document">
            <div class="panel-heading">
                <span class="panel-title">Uploaded Document</span>
            </div>
            <div class="panel-body">
                <a :href="`/uploads/${model.document}`" target="_blank">
                    <img class="panel-image" :src="`/uploads/${model.document}`">
                </a>
            </div>
        </div>
        <div class="panel" v-if="model.vendor_payments.length">
            <div class="panel-heading">
                <span class="panel-title">Vendor Payments</span>
            </div>
            <div class="panel-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Payment Date</th>
                            <th>Payment Mode</th>
                            <th>Amount Applied</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in model.vendor_payments">
                            <td class="width-3">
                                <router-link :to="`/vendor_payments/${item.vendor_payment_id}`">
                                    {{item.parent.number}}
                                </router-link>
                            </td>
                            <td class="width-3">{{item.parent.payment_date}}</td>
                            <td class="width-3">{{item.parent.payment_mode}}</td>
                            <td class="width-3">{{item.amount_applied | formatMoney(item.parent.currency)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get, byMethod } from '../../lib/api'
    import Status from '../../components/status/Bill.vue'
    import {Dropdown} from '../../components/dropdown'
    export default {
        components: { Status, Dropdown },
        data() {
            return {
                show: false,
                model: {
                    items: [],
                    currency: {},
                    vendor: {},
                    vendor_payments: []
                }
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/bills/${to.params.id}`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/bills/${to.params.id}`)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            deleteModel() {
                this.$refs.more.close()
                const r = confirm("Are you sure!")
                if(r != true) { return }
                this.$bar.start()
                byMethod('delete', `/api/bills/${this.model.id}`)
                    .then(({data}) => {
                        if(data.deleted) {
                            this.$router.push('/bills')
                            this.$message.success(`You have successfully deleted bill!`)
                        }
                        this.$bar.finish()
                    })
                    .catch((err) => {})
            },
            setData(res) {
                Vue.set(this.$data, 'model', res.data.data)
                this.$title.set(`Bill - ${this.model.number}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
