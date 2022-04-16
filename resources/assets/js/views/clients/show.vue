<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">{{model.text}}</span>
                <div>
                    <router-link :to="`/clients`" class="btn" title="Go Back">
                        <i class="fa fa-arrow-left"></i>
                    </router-link>
                    <router-link :to="`/clients/${model.id}/edit`"
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
                        </div>
                    </div>
                    <div class="col col-8">
                        <div class="stats">
                            <div class="stat">
                                <div class="stat-inner">
                                    <h2 class="stat-title">Account Receivable</h2>
                                    <p class="stat-value">{{stats.account_receivable | formatMoney(model.currency, false)}}</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-inner">
                                    <h2 class="stat-title">Total Revenue</h2>
                                    <p class="stat-value">{{stats.total_revenue | formatMoney(model.currency, false)}}</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-inner stat-last">
                                    <h2 class="stat-title">Open Sales Orders</h2>
                                    <p class="stat-value">{{stats.open_sales_orders}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="stats">
                            <div class="stat">
                                <div class="stat-inner">
                                    <h2 class="stat-title">Unpaid Invoices</h2>
                                    <p class="stat-value">{{stats.unpaid_invoices}}</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-inner">
                                    <h2 class="stat-title">Unused Credit</h2>
                                    <p class="stat-value">{{stats.unused_credit | formatMoney(model.currency, false)}}</p>
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-inner">
                                    <h2 class="stat-title">undrawn Payments</h2>
                                    <p class="stat-value">{{stats.advance_payments}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <mini-panel :resource="quotationURL"
            :heading="quotationColumns">
            <div slot="title">
                Quotations
            </div>
            <router-link :to="`/quotations/create?client_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/quotations/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.total | formatMoney(props.item.currency)}}</td>
                    <td><quotation :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
        <mini-panel :resource="salesOrderURL"
            :heading="salesOrderColumns">
            <div slot="title">
                Sales Orders
            </div>
            <router-link :to="`/sales_orders/create?client_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/sales_orders/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.total | formatMoney(props.item.currency)}}</td>
                    <td><sales-order :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
        <mini-panel :resource="invoiceURL"
            :heading="invoiceColumns">
            <div slot="title">
                Invoices
            </div>
            <router-link :to="`/invoices/create?client_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/invoices/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.total | formatMoney(props.item.currency)}}</td>
                    <td><invoice :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
        <mini-panel :resource="advancePaymentURL"
            :heading="advancePaymentColumns">
            <div slot="title">
                Advance Payments
            </div>
            <router-link :to="`/advance_payments/create?client_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/advance_payments/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.payment_date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.amount_received | formatMoney(props.item.currency)}}</td>
                    <td><advance-payment :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
        <mini-panel :resource="paymentURL"
            :heading="paymentColumns">
            <div slot="title">
                Client Payments
            </div>
            <router-link :to="`/client_payments/create?client_id=${model.id}`"
                class="btn btn-secondary btn-sm" slot="create">Create</router-link>
            <tr slot-scope="props" @click="$router.push(`/client_payments/${props.item.id}`)">
                    <td>{{ props.item.number }}</td>
                    <td>{{ props.item.payment_date }}</td>
                    <td>{{ props.item.reference || '-' }}</td>
                    <td>{{ props.item.amount_received | formatMoney(props.item.currency)}}</td>
                    <td><client-payment :id="props.item.status_id" /></td>
                </tr>
        </mini-panel>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get, byMethod } from '../../lib/api'
    import MiniPanel from '../../components/search/MiniPanel.vue'
    import Quotation from '../../components/status/Quotation.vue'
    import SalesOrder from '../../components/status/SalesOrder.vue'
    import Invoice from '../../components/status/Invoice.vue'
    import AdvancePayment from '../../components/status/AdvancePayment.vue'
    import ClientPayment from '../../components/status/ClientPayment.vue'
    export default {
        components: {MiniPanel, Quotation, SalesOrder, Invoice, AdvancePayment, ClientPayment},
        data() {
            return {
                show: false,
                quotationURL: `/api/mini/clients/quotations/${this.$route.params.id}`,
                quotationColumns: ['Number', 'Date', 'Reference', 'Amount', 'Status'],
                salesOrderURL: `/api/mini/clients/sales_orders/${this.$route.params.id}`,
                salesOrderColumns: ['Number', 'Date', 'Reference', 'Amount', 'Status'],
                invoiceURL: `/api/mini/clients/invoices/${this.$route.params.id}`,
                invoiceColumns: ['Number', 'Date', 'Reference', 'Amount', 'Status'],
                advancePaymentURL: `/api/mini/clients/advance_payments/${this.$route.params.id}`,
                advancePaymentColumns: ['Number', 'Date', 'Reference', 'Amount Received', 'Status'],
                paymentURL: `/api/mini/clients/payments/${this.$route.params.id}`,
                paymentColumns: ['Number', 'Date', 'Reference', 'Amount Received', 'Status'],
                model: {
                    currency: {}
                },
                stats: {}
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/clients/${to.params.id}`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/clients/${to.params.id}`)
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
                byMethod('delete', `/api/clients/${this.model.id}`)
                    .then(({data}) => {
                        if(data.deleted) {
                            this.$router.push('/clients')
                            this.$message.success(`You have successfully deleted client!`)
                        }
                        this.$bar.finish()
                    })
                    .catch((err) => {})
            },
            setData(res) {
                Vue.set(this.$data, 'model', res.data.data)
                Vue.set(this.$data, 'stats', res.data.stats)
                this.$title.set(`Client - ${this.model.text}`)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
