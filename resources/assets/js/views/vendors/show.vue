<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{model.text}}</span>
                <div>
                    <router-link :to="`/vendors`" class="btn" title="Go Back">
                        <i class="fa fa-arrow-left"></i>
                    </router-link>
                    <router-link :to="`/vendors/${model.id}/edit`"
                        class="btn" title="Edit">
                        <i class="fa fa-pencil"></i>
                    </router-link>
                    <a @click.stop="deleteModel" class="btn btn-danger">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col col-4">
                        <div class="bg-grey">
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-building"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.company}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.person}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-envelope"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.email}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.work_phone || '-'}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-mobile"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.mobile_number || '-'}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col col-2">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="col col-10">
                                    <p>{{model.currency.text}}</p>
                                </div>
                            </div>
                            <hr>
                            <strong>Billing Address</strong><br><br>
                            <pre>{{model.billing_address}}</pre>
                            <hr>
                            <strong>Shipping Address</strong><br><br>
                            <pre>{{model.shipping_address}}</pre>
                            <hr>
                            <strong>Payment Details</strong><br><br>
                            <pre>{{model.payment_details}}</pre>
                        </div>
                    </div>
                    <div class="col col-8">
                        <div class="stats">
                            <div class="stat">
                                <div class="stat-inner">
                                    <h2 class="stat-title">Account Payable</h2>
                                    <p class="stat-value">{{stats.account_payable | formatMoney(model.currency, false)}}</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-inner stat-last">
                                    <h2 class="stat-title">Total Expense</h2>
                                    <p class="stat-value">{{stats.total_expense | formatMoney(model.currency, false)}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats">
                            <div class="stat">
                                <div class="stat-inner">
                                    <h2 class="stat-title">Open Purchase Orders</h2>
                                    <p class="stat-value">{{stats.open_purchase_orders}}</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-inner stat-last">
                                    <h2 class="stat-title">Unpaid Bills</h2>
                                    <p class="stat-value">{{stats.unpaid_bills}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <mini-panel :resource="productURL"
            :heading="productColumns">
            <div slot="title">
                Products
            </div>
            <router-link :to="`/products/create?vendor_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/products/${props.item.id}`)">
                    <td>{{ props.item.product.item_code }}</td>
                    <td>{{ props.item.product.description | trim(90) }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.price | formatMoney(props.item.currency)}}</td>
                </tr>
        </mini-panel>
        <mini-panel :resource="expenseURL"
            :heading="expenseColumns">
            <div slot="title">
                Expenses
            </div>
            <router-link :to="`/expenses/create?vendor_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/expenses/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.payment_date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.amount_paid | formatMoney(props.item.currency)}}</td>
                </tr>
        </mini-panel>
        <mini-panel :resource="purchaseOrderURL"
            :heading="purchaseOrderColumns">
            <div slot="title">
                Purchase Orders
            </div>
            <router-link :to="`/purchase_orders/create?vendor_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/purchase_orders/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.total | formatMoney(props.item.currency)}}</td>
                    <td><purchase-order :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
        <mini-panel :resource="billURL"
            :heading="billColumns">
            <div slot="title">
                Bills
            </div>
            <router-link :to="`/bills/create?vendor_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/bills/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.total | formatMoney(props.item.currency)}}</td>
                    <td><bill :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
        <mini-panel :resource="paymentURL"
            :heading="paymentColumns">
            <div slot="title">
                Vendor Payments
            </div>
            <router-link :to="`/vendor_payments/create?vendor_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/vendor_payments/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.payment_date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.amount_paid | formatMoney(props.item.currency)}}</td>
                    <td><vendor-payment :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
        <mini-panel :resource="receiveURL"
            :heading="receiveColumns">
            <div slot="title">
                Receive Orders
            </div>
            <tr slot-scope="props" @click="$router.push(`/receive_orders/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.date }}</td>
                    <td>{{ props.item.note || '-' }}</td>
                    <td><receive-order :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get, byMethod } from '../../lib/api'
    import MiniPanel from '../../components/search/MiniPanel.vue'
    import PurchaseOrder from '../../components/status/PurchaseOrder.vue'
    import Bill from '../../components/status/Bill.vue'
    import VendorPayment from '../../components/status/VendorPayment.vue'
    import ReceiveOrder from '../../components/status/ReceiveOrder.vue'
    export default {
        components: {MiniPanel, PurchaseOrder, Bill, VendorPayment, ReceiveOrder},
        data() {
            return {
                show: false,
                productURL: `/api/mini/vendors/products/${this.$route.params.id}`,
                productColumns: ['Item Code', 'Description', 'Vendor Ref', 'Vendor Price'],
                expenseURL: `/api/mini/vendors/expenses/${this.$route.params.id}`,
                expenseColumns: ['Number', 'Payment Date', 'Reference', 'Amount'],
                purchaseOrderURL: `/api/mini/vendors/purchase_orders/${this.$route.params.id}`,
                purchaseOrderColumns: ['Number', 'Date', 'Reference', 'Amount', 'Status'],
                billURL: `/api/mini/vendors/bills/${this.$route.params.id}`,
                billColumns: ['Number', 'Date', 'Reference', 'Amount', 'Status'],
                paymentURL: `/api/mini/vendors/payments/${this.$route.params.id}`,
                paymentColumns: ['Number', 'Date', 'Reference', 'Amount Received', 'Status'],
                receiveURL: `/api/mini/vendors/receive_orders/${this.$route.params.id}`,
                receiveColumns: ['Number', 'Date', 'Note', 'Status'],
                model: {
                    currency: {}
                },
                stats: {}
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/vendors/${to.params.id}`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/vendors/${to.params.id}`)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            deleteModel() {
                const r = confirm("Are you sure!")
                if(r != true) { return }
                this.$bar.start()
                byMethod('delete', `/api/vendors/${this.model.id}`)
                    .then(({data}) => {
                        if(data.deleted) {
                            this.$router.push('/vendors')
                            this.$message.success(`You have successfully deleted vendor!`)
                        }
                        this.$bar.finish()
                    })
                    .catch((err) => {})
            },
            setData(res) {
                Vue.set(this.$data, 'model', res.data.data)
                Vue.set(this.$data, 'stats', res.data.stats)
                this.$title.set(`Vendors - ${this.model.text}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
