<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Purchase Order {{model.number}}</span>
                <div>
                    <router-link :to="`/purchase_orders`" class="btn" title="Go Back">
                        <i class="fa fa-arrow-left"></i>
                    </router-link>
                    <router-link :to="`/email/${model.id}/purchase_order`"
                        :class="['btn', model.status_id === 1 ? 'btn-primary' : '']"
                        title="Sent Email">
                        <i class="fa fa-envelope-o"></i>
                    </router-link>
                    <router-link :to="`/purchase_orders/${model.id}/clone`" class="btn"
                        title="Clone">
                        <i class="fa fa-files-o"></i>
                    </router-link>
                    <div class="btn-group">
                        <a :href="`/docs/purchase_orders/${model.id}`" target="_blank" class="btn" title="View PDF">
                            <i class="fa fa-file-pdf-o"></i>
                        </a>
                        <a :href="`/docs/purchase_orders/${model.id}?mode=download`" target="_blank"
                            class="btn" title="Download PDF">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                    <router-link v-if="model.is_editable" :to="`/purchase_orders/${model.id}/edit`"
                        class="btn" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </router-link>
                     <dropdown title="More" ref="more">
                        <li v-if="model.status_id === 1">
                            <a @click.stop="markAs(2)">Mark as Sent</a>
                        </li>
                        <li v-if="[2, 5].indexOf(model.status_id) >= 0">
                            <a @click.stop="markAs(3)">Mark as Confirmed</a>
                        </li>
                        <li  v-if="[1, 2, 3].indexOf(model.status_id) >= 0">
                            <a @click.stop="markAs(5)">Mark as Cancelled</a>
                        </li>
                        <li v-if="[3].indexOf(model.status_id) >= 0">
                            <a @click.stop="markAs(6)">Mark as Closed</a>
                        </li>
                        <li v-if="[3, 8].indexOf(model.status_id) >= 0">
                            <a :href="`/docs/purchase_orders/${model.id}?mode=receive`" target="_blank">Print Receive Order List</a>
                        </li>
                        <li v-if="[3, 8].indexOf(model.status_id) >= 0">
                            <router-link :to="`/purchase_orders/${model.id}/receive_order`">
                                Receive Order
                            </router-link>
                        </li>
                        <li v-if="[7].indexOf(model.status_id) >= 0">
                            <router-link :to="`/purchase_orders/${model.id}/bill`">
                                Convert to Bill
                            </router-link>
                        </li>
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
                                <p><strong>To:</strong></p>
                                <router-link :to="`/vendors/${model.vendor_id}`">
                                    <span>{{model.vendor.person}}</span><br>
                                    <span>{{model.vendor.company}}</span><br>
                                    <pre>{{model.vendor.billing_address}}</pre>
                                </router-link>
                            </div>
                            <div class="col col-4">
                                &nbsp;
                            </div>
                            <div class="col col-4">
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
                                    <td class="width-1 align-center">
                                        {{item.qty}}
                                        <small class="status status-draft" v-if="item.qty_received > 0">
                                            Received: {{item.qty_received}}
                                        </small>
                                    </td>
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
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get, post, byMethod } from '../../lib/api'
    import Status from '../../components/status/PurchaseOrder.vue'
    import {Dropdown} from '../../components/dropdown'
    export default {
        components: { Status, Dropdown },
        data() {
            return {
                show: false,
                model: {
                    items: [],
                    currency: {},
                    vendor: {}
                }
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/purchase_orders/${to.params.id}`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/purchase_orders/${to.params.id}`)
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
                byMethod('delete', `/api/purchase_orders/${this.model.id}`)
                    .then(({data}) => {
                        if(data.deleted) {
                            this.$router.push('/purchase_orders')
                            this.$message.success(`You have successfully deleted purchase order!`)
                        }
                        this.$bar.finish()
                    })
                    .catch((err) => {})
            },
            markAs(status) {
                this.$refs.more.close()
                this.$bar.start()
                post(`/api/purchase_orders/${this.model.id}/mark`, {status: status})
                    .then(({data}) => {
                        if(data.saved) {
                            Vue.set(this.model, 'status_id', data.status_id)
                            Vue.set(this.model, 'is_editable', data.is_editable)
                            this.$message.success(`You have successfully marked purchase order!`)
                        }
                        this.$bar.finish()
                    })

            },
            setData(res) {
                Vue.set(this.$data, 'model', res.data.data)
                this.$title.set(`Purchase Order - ${this.model.number}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
