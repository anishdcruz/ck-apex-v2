<template>
    <div v-if="show">
        <div class="panel">
            <div class="panel-heading">
                <span class="panel-title">Overview</span>
            </div>
            <div class="panel-overview">
                <div class="stats">
                    <div class="stat">
                        <div class="stat-inner">
                            <h2 class="stat-title">Accounts Receivable</h2>
                            <p class="stat-value">{{model.accounts_receivable | formatMoney(model.currency, false)}}</p>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-inner">
                            <h2 class="stat-title">Total Revenue</h2>
                            <p class="stat-value">{{model.total_revenue | formatMoney(model.currency, false)}}</p>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-inner">
                            <h2 class="stat-title">Open Sales Orders</h2>
                            <p class="stat-value">{{model.open_sales_orders}}</p>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-inner stat-last">
                            <h2 class="stat-title">Unpaid Invoices</h2>
                            <p class="stat-value">{{model.unpaid_invoices}}</p>
                        </div>
                    </div>
                </div>
                <div class="stats">
                    <div class="stat">
                        <div class="stat-inner">
                            <h2 class="stat-title">Accounts Payable</h2>
                            <p class="stat-value">{{model.accounts_payable | formatMoney(model.currency, false)}}</p>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-inner">
                            <h2 class="stat-title">Total Expense</h2>
                            <p class="stat-value">{{model.total_expense | formatMoney(model.currency, false)}}</p>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-inner">
                            <h2 class="stat-title">Open Purchase Orders</h2>
                            <p class="stat-value">{{model.open_purchase_orders}}</p>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-inner stat-last">
                            <h2 class="stat-title">Unpaid bills</h2>
                            <p class="stat-value">{{model.unpaid_bills}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-8">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">Income vs Expense</span>
                    </div>
                    <div class="panel-body">
                        <line-chart :datasets="datasets" :labels="labels"></line-chart>
                    </div>
                </div>
            </div>
            <div class="col col-4" v-if="model.top_unpaid_invoices.length">
                <div class="panel">
                    <div class="panel-heading">
                        <span class="panel-title">Top 10 Unpaid Invoices</span>
                    </div>
                    <div class="panel-body">
                        <table class="table table-link">
                            <thead>
                                <tr>
                                    <th>Number</th>
                                    <th>Due Date</th>
                                    <th>Due Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="invoice in model.top_unpaid_invoices" @click="$router.push(`/invoices/${invoice.id}`)">
                                    <td>{{invoice.number}}</td>
                                    <td>{{invoice.due_date || '-'}}</td>
                                    <td>{{invoice.due_amount | formatMoney(invoice.currency)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { get } from '../../lib/api'
    import LineChart from '../../components/charts/Line.vue'
    export default {
        components: {LineChart},
        data() {
            return {
                show: false,
                datasets: [],
                labels: [],
                model: {
                    top_unpaid_invoices: [],
                    currency: {}
                }
            }
        },
        beforeRouteEnter(to, from, next) {
            get(`/api/dashboard`)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            this.show = false
            get(`/api/dashboard`)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set('Dashboard')
                Vue.set(this.$data, 'model', res.data.data)
                Vue.set(this.$data, 'labels', res.data.income_expense_chart.labels)
                Vue.set(this.$data, 'datasets', res.data.income_expense_chart.datasets)
                this.$bar.finish()
                this.show = true
            }
        }
    }
</script>
