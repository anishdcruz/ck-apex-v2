<template>
    <div>
        <panel ref="panel" :heading="heading" :resource="resource">
            <span slot="title">Expenses</span>
            <router-link slot="create" to="/expenses/create" class="btn btn-primary">
                New Expense
            </router-link>
            <tr slot-scope="props" @click="$router.push(`${resource}/${props.item.id}`)">
                    <td>{{ props.item.id }}</td>
                    <td class="width-2">{{ props.item.payment_date }}</td>
                    <td class="width-2">{{ props.item.number }}</td>
                    <td class="width-6" :title="props.item.vendor.text">
                        {{ props.item.vendor.text | trim(60) }}
                    </td>
                    <td class="width-2">{{ props.item.amount_paid | formatMoney(props.item.currency) }}</td>
                </tr>
        </panel>
    </div>
</template>
<script type="text/javascript">
    import Panel from '../../components/search/panel.vue'
    import { get } from '../../lib/api'

    export default {
        components: { Panel },
        data() {
            return {
                resource: '/expenses',
                heading: [
                    {title: 'ID', name: 'id', sort: true},
                    {title: 'Date', name: 'date', sort: true},
                    {title: 'Number', name: 'number', sort: true},
                    {title: 'Client', name: 'vendor', sort: false},
                    {title: 'Amount Paid', name: 'amount_paid', sort: true}
                ]
            }
        },
        beforeRouteEnter(to, from, next) {
            get('/api/expenses', to.query)
                .then(res => {
                    next(vm => vm.setData(res))
                })
                // catch 422
        },
        beforeRouteUpdate (to, from, next) {
            get('/api/expenses', to.query)
                .then(res => {
                    this.setData(res)
                    next()
                })
                //catch 422
        },
        methods: {
            setData(res) {
                this.$title.set(`Expenses`)
                this.$refs.panel.setData(res)
            }
        }
    }
</script>
