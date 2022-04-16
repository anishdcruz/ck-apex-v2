<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Sales Order {{model.number}}</span>
                <div>
                    <router-link :to="`/sales_orders`" class="btn" title="Go Back">
                        <i class="fa fa-arrow-left"></i>
                    </router-link>
                    <router-link :to="`/email/${model.id}/sales_order`"
                        :class="['btn', model.status_id === 1 ? 'btn-primary' : '']"
                        title="Sent Email">
                        <i class="fa fa-envelope-o"></i>
                    </router-link>
                    <router-link :to="`/sales_orders/${model.id}/clone`" class="btn"
                        title="Clone">
                        <i class="fa fa-files-o"></i>
                    </router-link>
                    <div class="btn-group">
                        <a :href="`/docs/sales_orders/${model.id}`" target="_blank" class="btn" title="View PDF">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                        <a :href="`/docs/sales_orders/${model.id}?mode=download`" target="_blank"
                            class="btn" title="Download PDF">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                    <router-link v-if="model.is_editable" :to="`/sales_orders/${model.id}/edit`"
                        class="btn" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </router-link>
                     <dropdown title="More" ref="more">
                        <li v-if="model.status_id === 1">
                            <a @click.stop="markAs(2)">Mark as Sent</a>
                        </li>
                        <li v-if="[1, 2, 4].indexOf(model.status_id) >= 0">
                            <a @click.stop="markAs(3)">Mark as Confirmed</a>
                        </li>
                        <li  v-if="[1, 2, 3].indexOf(model.status_id) >= 0">
                            <a @click.stop="markAs(4)">Mark as On Hold</a>
                        </li>
                        <li  v-if="[3].indexOf(model.status_id) >= 0">
                            <a @click.stop="markAs(4)">Mark as Closed</a>
                        </li>
                        <li v-if="[2, 3, 8].indexOf(model.status_id) >= 0">
                            <router-link :to="`/sales_orders/${model.id}/invoice`">
                                Convert to Invoice
                            </router-link>
                        </li>
                        <li v-if="[3, 7].indexOf(model.status_id) >= 0">
                            <a :href="`/docs/sales_orders/${model.id}?mode=pick`" target="_blank">
                                Print Pick List
                            </a>
                        </li>
                        <li v-if="[3, 7].indexOf(model.status_id) >= 0">
                            <router-link :to="`/sales_orders/${model.id}/goods_issue`">
                                Post Goods Issue
                            </router-link>
                        </li>
                        <li>
                            <a @click.stop="deleteModel">
                                Delete
                            </a>
                        </li>
                    </dropdown>
                </div>
            </div>
            <div class="panel-body">
                <div class="document">
                    <div class="document-heading">
                        <div class="row">
                            <div class="col col-4">
                                <p><strong>Bill To:</strong></p>
                                <router-link :to="`/clients/${model.client_id}`">
                                    <span>{{model.client.person}}</span><br>
                                    <span>{{model.client.company}}</span><br>
                                    <pre>{{model.client.billing_address}}</pre>
                                </router-link>
                            </div>
                            <div class="col col-3">
                                <p><strong>Ship To:</strong></p>
                                <router-link :to="`/clients/${model.client_id}`">
                                    <span>{{model.client.person}}</span><br>
                                    <span>{{model.client.company}}</span><br>
                                    <pre>{{model.client.shipping_address}}</pre>
                                </router-link>
                            </div>
                            <div class="col col-5">
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
                                        <tr v-if="model.quotation_id">
                                            <td>Quotation Number:</td>
                                            <td>
                                                <router-link :to="`/quotations/${model.quotation_id}`">
                                                    {{model.quotation.number}}
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
                                    <th>Item Code</th>
                                    <th>Item Description</th>
                                    <th class="align-center">Unit Price</th>
                                    <th class="align-center">Qty</th>
                                    <th class="align-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="item in model.items">
                                    <tr>
                                        <td class="width-2">
                                            <router-link :to="`/products/${item.product_id}`">
                                                {{item.product.item_code}}
                                            </router-link>
                                        </td>
                                        <td class="width-4">
                                            <router-link :to="`/products/${item.product_id}`">
                                                <pre>{{item.product.description}}</pre>
                                            </router-link>
                                        </td>
                                        <td class="width-2 align-right">
                                            {{item.unit_price | formatMoney(model.currency, false)}}
                                        </td>
                                        <td class="width-1 align-center">
                                            {{item.qty}}
                                            <small class="status status-draft" v-if="item.qty_issued > 0">
                                            Issued: {{item.qty_issued}}
                                        </small>
                                        </td>
                                        <td class="width-2 align-right">
                                            {{(item.qty * item.unit_price) | formatMoney(model.currency, false)}}
                                        </td>
                                    </tr>
                                    <tr class="item-tax-show" v-if="item.taxes.length" v-for="tax in item.taxes">
                                        <td colspan="3">{{tax.name}}</td>
                                        <td>{{tax.rate}}%</td>
                                        <td>+{{(Number(item.qty * Number(item.unit_price)) * tax.rate / 100) | formatMoney(model.currency, false)}}</td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"></td>
                                    <td colspan="2">Sub Total</td>
                                    <td class="align-right">
                                        {{model.sub_total | formatMoney(model.currency, false)}}
                                    </td>
                                </tr>
                                <tr v-for="(value, key) in selectedTaxes" class="item-selected-tax">
                                    <td colspan="2"></td>
                                    <td colspan="2">{{key}}</td>
                                    <td class="align-right">
                                        {{value | formatMoney(model.currency, false)}}
                                    </td>
                                </tr>
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
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get, post, byMethod } from '../../lib/api'
    import Status from '../../components/status/SalesOrder.vue'
    import {Dropdown} from '../../components/dropdown'
    export default {
        components: { Status, Dropdown },
        data() {
            return {
                show: false,
                model: {
                    items: [],
                    currency: {},
                    client: {}
                }
            }
        },
         computed: {
            selectedTaxes() {
                const taxes = {}

                this.model.items.forEach((item) => {
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
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/sales_orders/${to.params.id}`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/sales_orders/${to.params.id}`)
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
                byMethod('delete', `/api/sales_orders/${this.model.id}`)
                    .then(({data}) => {
                        if(data.deleted) {
                            this.$router.push('/sales_orders')
                            this.$message.success(`You have successfully deleted sales order!`)
                        }
                        this.$bar.finish()
                    })
                    .catch((err) => {})
            },
            markAs(status) {
                this.$refs.more.close()
                this.$bar.start()
                post(`/api/sales_orders/${this.model.id}/mark`, {status: status})
                    .then(({data}) => {
                        if(data.saved) {
                            Vue.set(this.model, 'status_id', data.status_id)
                            Vue.set(this.model, 'is_editable', data.is_editable)
                            this.$message.success(`You have successfully marked sales order!`)
                        }
                        this.$bar.finish()
                    })

            },
            setData(res) {
                Vue.set(this.$data, 'model', res.data.data)
                this.$title.set(`Sales Order - ${this.model.number}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
